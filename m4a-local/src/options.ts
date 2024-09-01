import { VARIANTS } from "./config";
import { Options } from "./types/main-types";

const commandLineArgs = require('command-line-args')
const fs = require('fs');
const path = require('path');

export const USER_OPTIONS: Options = readOptions();

/**
 * Read the command line options
 */
function readOptions(): Options {
    const optionDefinitions = [
        {
          name: 'input',
          alias: 'i',
          type: String,
          description: 'The folder of the XML module to be transformed. E.g. xml/hv/hv-gr1/hv-gr11'
        }, {
            name: 'output',
            alias: 'o',
            type: String,
            description: 'The base folder of the generated output'
        }, {
            name: 'variant',
            alias: 'v',
            // default: 'hv',
            type: String,
            description: 'The variant of the XSLT to be used. Options are [hv, hv_en, mbo]'
        }, {
            name: 'sector',
            type: String,
            description: 'The MBO sector. Options are [bev, asc, eng, mob, ict] or all'
        }
    ];
    const args = commandLineArgs(optionDefinitions)

    let stats = fs.statSync(args['input']);
    if(!stats.isDirectory()) {
        console.log("The input must be a directory containing the XML module to be transformed. E.g. xml/hv/hv-gr1/hv-gr11");
        process.exit(1);
    }

    let userFolder = process.cwd();
    let resourcesPath = path.normalize(__dirname + '/../resources');
    let xmlBaseFolder = path.normalize(userFolder + path.sep + args['input']);

    if(!args['variant'] || !VARIANTS[args['variant']]) {
        console.log("The variant must be specified. E.g. hv or mbo");
        process.exit(1);
    }

    let variant = VARIANTS[args['variant']];

    let xsltFile = resourcesPath + path.sep + variant.xsl + '.sef.json';
    if(!fs.existsSync(xsltFile)) {
        console.log("The XSLT file " + xsltFile + " does not exist");
        process.exit(1);
    }

    let sector:string[] = null;
    if(variant.useSector) {
        if(args['sector']) {
            switch(args['sector']) {
                case 'all':
                    sector = ['', 'bev', 'asc', 'eng', 'mob', 'ict'];
                    break;
                case 'bev':
                case 'asc':
                case 'eng':
                case 'mob':
                case 'ict':
                    sector = [args['sector']];
                    break;
                default:
                    console.log("The sector must be one of [bev, asc, eng, mob, ict] or all");
                    process.exit(1);
            }
        } else {
            sector = [''];
        }
    }

    let mainOptions: Options = {
        userFolder: userFolder,
        xmlBaseFolder: xmlBaseFolder,
        resourcesPath: resourcesPath,
        xsltJSON: require(xsltFile),
        targetFolder: args['output'],
        sector: sector,
        variant: variant
    }
        
    if(!fs.existsSync(mainOptions.targetFolder)) {
        fs.mkdirSync(mainOptions.targetFolder, { recursive: true });
    }

    return mainOptions;
}
