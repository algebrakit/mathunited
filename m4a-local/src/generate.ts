import { XSLTParameters } from "./types/generate-types";
import { createFolderIfNotExists } from "./util/files";
import { Component, isComponentFile } from "./types/component";
import { Subcomponent } from "./types/subcomponent";
import { PARENT, VARIANTS } from "./config";
import { USER_OPTIONS } from "./options";

const fs = require('fs');
const path = require('path');
const SaxonJS = require('saxon-js');

const GEOGEBRA_SOURCE = 'https://www.geogebra.org/apps/deployggb.js';

/**
 * Search for modules in the folder or subfolders.  If a module is found, generate all web pages for it and write them
 * in the target folder.
 * @param folder 
 * @returns 
 */
export async function processFolder(folder: string, sector?: string) {
    let files = fs.readdirSync(folder);
    let componentFiles = files.filter(file => file.endsWith('.xml') && !file.endsWith('entities.xml') && isComponentFile(folder + path.sep + file));
    // let subcomponentFiles = files.filter(file => file.endsWith('.xml') && isSubcomponentFile(folder + path.sep + file));
    if(componentFiles.length==0) {
        // otherwise, process subfolders
        for(let file of files) {
            let stat = fs.statSync(folder + path.sep + file);
            if(stat.isDirectory()) {
                await processFolder(folder + path.sep + file, sector);
            }
        }
    } else if(componentFiles.length == 1) {
        let fileName = folder + path.sep + componentFiles[0];
        let component = new Component(fileName, sector);
        copyComponentResources(component);
        generateGeogebra(component);

        await processComponent(component, sector);
    } else {
        throw new Error('Multiple component files found in ' + folder);
    }
     
}

async function processComponent(component: Component, sector?: string) {
    let subcomponents = component.subcomponents;
    for(let subcomponent of subcomponents) {
        await generateSubcomponent(subcomponent, sector);
    }
}

 async function generateSubcomponent(subcomponent: Subcomponent, sector: string) {
    console.log('Subcomponent ' + subcomponent.id);
    
    createFolderIfNotExists(subcomponent.targetFolder);

    let xmlFile = fs.readFileSync(subcomponent.file, 'utf8').trim();
    let firstItem:string = null;

    // generate items in the right order. We want to know which item is first, to use as default for the index.html file.
    let items = null;
    items = Object.keys(USER_OPTIONS.variant.items).sort((a, b) => USER_OPTIONS.variant.items[a].order - USER_OPTIONS.variant.items[b].order);
    for(let item of items) {
        let spec = USER_OPTIONS.variant.items[item];
        // check if the item exists in the html. Some items (like answers) are always generated.
        if(spec.always || xmlFile.includes('<' + item)) {
            let nr = 1;
            if(spec.multiple) {
                // some items occur multiple times, such as exercise items. A 'num' attribute identifies the item.
                let matches = xmlFile.match(new RegExp('<' + item, 'g'));
                nr = matches.length
            }

            for(let i = 1; i <= nr; i++) {
                let params: XSLTParameters = {
                    comp: subcomponent.component.name,
                    subcomp: subcomponent.id,
                    num: spec.multiple? i.toString(): '',
                    item: item,
                    parent: PARENT,
                    component_title: subcomponent.component.title,
                    component_number: subcomponent.component.number.toString(),
                    subcomponent_title: subcomponent.title,
                    subcomponent_number: subcomponent.number.toString(),
                    subcomponent_index: ''+subcomponent.number,
                    subcomponent_count: subcomponent.component.subcomponents.length.toString(),
                    subcomponent_preceding_id: subcomponent.prevId,
                    subcomponent_following_id: subcomponent.followingId,
                    sector: sector
                }

                // generate html for the item
                let result = await transform(subcomponent, params);
                let fname = subcomponent.targetFolder + path.sep +subcomponent.id;
                if(item) fname+= '-' + item;  // WM has an empty item, so leave out the '-'
                fname+= ((spec.multiple)?('-'+ i + '.html'):( '.html'));
                if(!firstItem) firstItem = fname;
                fs.writeFileSync(fname, result);

                // check for references to worksheets in the generated html
                let regExp = new RegExp(subcomponent.id+'-worksheet-[^"]+', 'g');
                let matches = result.match(regExp);
                if(matches) {
                    // let the XSLT generate the worksheet html
                    matches.forEach(async match => {
                        let worksheet = match.substring(subcomponent.id.length+11).replace('.html','');
                        let worksheetParams: XSLTParameters = {
                            ...params,
                            ws_id: worksheet
                        }
                        let worksheetResult = await transform(subcomponent, worksheetParams);
                        fs.writeFileSync(subcomponent.targetFolder + path.sep + subcomponent.id + '-worksheet-' + worksheet + '.html', worksheetResult);
                    })
                }

            }

        }    
    }
        

    if(firstItem) {
        // generate an index.html file that redirects to the first item
        let basename = path.basename(firstItem);
        fs.writeFileSync(subcomponent.targetFolder + path.sep + 'index.html', 
//            '<html><head><meta http-equiv="refresh" content="0; url=' + path.basename(firstItem) + '"></head></html>'
`
<html>
   <head>
     <script type="text/javascript">
        const urlParams = new URLSearchParams(window.location.search);
        let parent = urlParams.get('parent');
        if(parent) {
            window.location.href = '${basename}?parent=' + parent;
        } else {
            window.location.href = '${basename}';
        }
     </script>
   </head>
</html>
`
        );
    }
}

/**
 * Transform the geogebra files to html files for this component
 * @param folder 
 * @param targetFolder 
 */
function generateGeogebra(component: Component) {
    let geogebraFolder = component.folder + path.sep + 'geogebra';
    if(!fs.existsSync(geogebraFolder)) return;

    let files = fs.readdirSync(geogebraFolder);
    for(let file of files) {
        if(file.endsWith('.ggb')) {
            let ggbFname = geogebraFolder + path.sep + file;
            let base64 = fs.readFileSync(ggbFname, 'base64'); 
            let html = `
            <html style='overflow:hidden'>
                <head>
                    <style type='text/css'>
                        <!--body { font-family:Arial,Helvetica,sans-serif; margin-left:40px }-->
                    </style>
                    <script type='text/javascript' language='javascript' src='${GEOGEBRA_SOURCE}'></script>
                </head>
                <body>
                    <article class='geogebraweb' style='display:inline-block;' data-param-ggbbase64='${base64}'></article>
                    <div id="ggb-element"></div>
                    <script>  
                        var params = {"appName": "geometry", "showToolBar": false, "showAlgebraInput": false, "showMenuBar": false };
                        var applet = new GGBApplet(params, true);
                        window.addEventListener("load", function() { 
                            applet.inject('ggb-element');
                        });
                    </script>
                </body>
            </html>
            `

            let geoegebraTargetFolder = component.targetFolder + path.sep + 'geogebra';
            createFolderIfNotExists(geoegebraTargetFolder);
            fs.writeFileSync(geoegebraTargetFolder + path.sep + path.basename(file, '.ggb') + '.html', html);
        }
    }
}

function copyComponentResources(component:Component) {
    ['dox', 'images'].forEach(folder => {
        let sourceFolder = component.folder + path.sep + folder;
        if (fs.existsSync(sourceFolder)) {
            fs.cpSync(sourceFolder, component.targetFolder + path.sep + folder, { recursive: true });
        }
    });
}

/**
 * 
 * @param fname the xml filename in the module folder
 */
async function transform(subcomponent:Subcomponent, params: XSLTParameters) {
    const options = { 
        stylesheetInternal: USER_OPTIONS.xsltJSON,
        destination: "serialized",
        sourceFileName: subcomponent.file,
        stylesheetParams: {
            docbase: subcomponent.folder + '/',
            ...params
        }
    };
    
    let output = await SaxonJS.transform(options, "async");
    return output.principalResult;
}
