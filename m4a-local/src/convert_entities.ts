// to generate the SEF file of the XSLT:
// xslt3 -xsl:resources/xslt/m4a_view.xslt -export:m4a_view.sef.json -nogo     


const fs = require('fs');
const path = require('path');

const SOURCE = '/Users/martijnslob/github/mathunited/m4a-local/xml/saba';
const TARGET = '/Users/martijnslob/github/mathunited/m4a-local/xml/saba-out';
const RESOURCES = '/Users/martijnslob/github/mathunited/m4a-local/resources/';
const ENTITY_MAP = {};


async function main() {
    try{
        initEntities();
        await processFolder(SOURCE, TARGET);
    } catch(err) {
        console.log(err);
    }
}

function initEntities() {
    // read all entities from the file
    let entitiesFile = fs.readFileSync(RESOURCES + path.sep + '/entities.xml', 'utf8');
    // parse entity objects, like <!ENTITY AE "&#x00C6;">
    let lines = entitiesFile.split('\n');
    lines.forEach(line => {
        let match = line.match(/^\s*<!ENTITY\s+([a-zA-Z]+) "(\&[#a-zA-Z0-9]+;)">/);
        if(match) {
            ENTITY_MAP['&'+match[1]+';'] = match[2];
        }
    })
}

async function processFolder(sourceFolder, targetFolder) {
    let files = fs.readdirSync(sourceFolder);
    for(let file of files) {
        let sourceFile = sourceFolder + path.sep + file;
        let targetFile = targetFolder + path.sep + file;
        if(fs.statSync(sourceFile).isDirectory() && sourceFile.indexOf('-') > 0) { //skip dox, geogebra, etc
            if(!fs.existsSync(targetFile)) {
                fs.mkdirSync(targetFile, { recursive: true });
            }
            await processFolder(sourceFile, targetFile);
        } else if(file.endsWith('.xml') && file!='entities.xml') {
            let content = fs.readFileSync(sourceFile, 'utf8');
            content = replaceEntities(content,file);
            fs.writeFileSync(targetFile, content);
        }
    }
}

function replaceEntities(content:any, fname:string):string {
    let matches = content.match(/\&[a-zA-Z]+;/g);
    let entities = {}
    if(matches) {
        console.log('Found entities in '+fname);
        // remove duplicates
        matches.forEach(_m => entities[_m] = true);

        Object.keys(entities).forEach(_entity => {
            let def = ENTITY_MAP[_entity];
            if(!def) {
                if(['&lt;','&gt;','&amp;'].includes(_entity)) {
                    //no problem
                } else {
                    console.log("Unresolved entity found: "+_entity);
                    // process.exit(-1);
                }
            } else {
                // replace all occurrences of the entity
                content = content.replaceAll(_entity, def);
            }
        });
    }

    return content;
}

main();