import { XSLTParameters } from "./types/generate-types";
import { createFolderIfNotExists } from "./util/files";
import { Component, isComponentFile } from "./types/component";
import { isSubcomponentFile, Subcomponent } from "./types/subcomponent";
import { ITEMS, PARENT } from "./config";
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
    // get the folder name
    let parts = folder.split(path.sep);
    let folderName = parts[parts.length - 1];

    // if the folder contains a subcomponent file, process it
    let fileName = folder + '/' + folderName + '.xml';
    if(fs.existsSync(fileName)) {
        if(isSubcomponentFile(fileName)) {
            await generateSubcomponent(folder, folderName, sector);
            return;
        } else if(isComponentFile(fileName)) {
            let component = new Component(fileName, sector);
            copyComponentResources(component);
            generateGeogebra(component);
        }
    } 

    // otherwise, process subfolders
    let files = fs.readdirSync(folder);
    for(let file of files) {
        let stat = fs.statSync(folder + path.sep + file);
        if(stat.isDirectory()) {
            await processFolder(folder + path.sep + file, sector);
        }
    }
}

 async function generateSubcomponent(subcomponentFolder:string, subcomponentName:string, sector: string) {
    console.log('Subcomponent ' + subcomponentName);

    // get the component name. E.g. hv-gr1 if the subcomponent folder is hv-gr11
    let componentFolder = path.dirname(subcomponentFolder);
    let componentName = path.basename(componentFolder);
    let component = new Component(componentFolder + path.sep + componentName + '.xml', sector);
    let subcomponent = new Subcomponent(subcomponentName, component);
    
    createFolderIfNotExists(subcomponent.targetFolder);

    let subcomponentFileName = subcomponentFolder + path.sep + subcomponentName + '.xml';
    let xmlFile = fs.readFileSync(subcomponentFileName, 'utf8');
    let firstItem:string = null;

    for(let item of Object.keys(ITEMS)) {
        let spec = ITEMS[item];
        if(spec.always || xmlFile.includes('<' + item)) {
            let nr = 1;
            if(spec.multiple) {
                let matches = xmlFile.match(new RegExp('<' + item, 'g'));
                nr = matches.length
            }
            for(let i = 1; i <= nr; i++) {
                let params: XSLTParameters = {
                    comp: componentName,
                    subcomp: subcomponentName,
                    num: spec.multiple? i.toString(): '',
                    item: item,
                    parent: PARENT,
                    component_title: component.title,
                    subcomponent_title: subcomponent.section,
                    subcomponent_index: (component.subcomponents.indexOf(subcomponentName)).toString(),
                    subcomponent_count: component.subcomponents.length.toString(),
                    sector: sector
                }

                // generate html for the item
                let result = await transform(subcomponentFolder, params);
                let fname = subcomponent.targetFolder + path.sep +subcomponentName + '-' + item 
                    + ((spec.multiple)?('-'+ i + '.html'):( '.html'));
                if(!firstItem) firstItem = fname;
                fs.writeFileSync(fname, result);

                // check for worksheets
                let regExp = new RegExp(subcomponentName+'-worksheet-[^"]+', 'g');
                let matches = result.match(regExp);
                if(matches) {
                    matches.forEach(async match => {
                        let worksheet = match.substring(subcomponentName.length+11);
                        let worksheetParams: XSLTParameters = {
                            ...params,
                            ws_id: worksheet
                        }
                        let worksheetResult = await transform(subcomponentFolder, worksheetParams);
                        fs.writeFileSync(subcomponent.targetFolder + path.sep + subcomponentName + '-worksheet-' + worksheet + '.html', worksheetResult);
                    })
                }

            }
        }
    }

    if(firstItem) {
        fs.writeFileSync(subcomponent.targetFolder + path.sep + 'index.html', 
            '<html><head><meta http-equiv="refresh" content="0; url=' + path.basename(firstItem) + '"></head></html>');
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
async function transform(moduleFolder:string, params: XSLTParameters) {
    const options = { 
        stylesheetInternal: USER_OPTIONS.xsltJSON,
        destination: "serialized",
        sourceFileName: moduleFolder+'/'+params.subcomp+'.xml',
        stylesheetParams: {
            docbase: moduleFolder + '/',
            ...params
        }
    };
    
    let output = await SaxonJS.transform(options, "async");
    return output.principalResult;
}
