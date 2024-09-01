// to generate the SEF file of the XSLT:
// xslt3 -xsl:resources/xslt/m4a_view.xslt -export:m4a_view.sef.json -nogo     

import { processFolder } from "./generate";
import { USER_OPTIONS } from "./options";   

const fs = require('fs');
const path = require('path');

async function main() {
    try{
        // copy css and js files to the target folder
        copyFiles();
        // search for modules in the folder
        if(USER_OPTIONS.sector) {
            for(let sector of USER_OPTIONS.sector) {
                console.log('Sector ' + (sector?sector:'no-sector'));
                await processFolder(USER_OPTIONS.xmlBaseFolder, sector);
            }
        } else {
            await processFolder(USER_OPTIONS.xmlBaseFolder);
        }
    } catch(err) {
        console.log(err);
    }
}

/**
 * Copy the css and js files to the target folder
 */
function copyFiles() {
    ['css', 'js', 'sources', 'sources_ma'].forEach(folder => {
        fs.cpSync(USER_OPTIONS.resourcesPath + path.sep + folder, USER_OPTIONS.targetFolder + path.sep + folder, { recursive: true });
    });
}



main();