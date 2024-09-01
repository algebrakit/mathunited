import { VARIANTS, SECTORS } from "./config";
import { Options } from "./types/main-types";

const commandLineArgs = require('command-line-args')
const fs = require('fs');
import path = require('path');
import readline = require('readline');

type InputSpec = {[key:string]:string}
export let USER_OPTIONS: Options;

export function getOptions(): Promise<Options> {
    if(USER_OPTIONS) { return Promise.resolve(USER_OPTIONS); }

    return readOptions().then(options => {
        USER_OPTIONS = options;

        return options;
    });
}



/**
 * Read the command line options.
 * If no options are given in the command line, the tool falls back to interactive mode.
 */
async function readOptions(): Promise<Options> {
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
    const args:InputSpec = commandLineArgs(optionDefinitions)
    if(!args['input'] || !args['output'] || !args['variant']) {
        await getInteractiveOptions(args);
    }

    let confirm = `
            XML Folder: ${args['input']},
            Target Folder: ${args['output']},
            Variant: ${args['variant']},
            Sector: ${args['sector']}
    `;
    console.log(confirm);

    return createOptionObject(args);
}

async function getInteractiveOptions(args?:InputSpec): Promise<InputSpec> {
    let inputSpec = args? args: {};

    if(!inputSpec['input']) {
        inputSpec['input'] = await questionWithValidation(
            "Folder with XML to be transformed: ", 
            (input) => fs.existsSync(input)
        );
    }        

    if(!inputSpec['output']) {
        inputSpec['output'] = await questionWithValidation(
            "Folder for the generated output: ", 
            (input) => true
        );
    }

    if(!inputSpec['variant']) {
        let variants = Object.keys(VARIANTS).join(', ');
        inputSpec['variant'] = await questionWithValidation(
            `Variant of the XSLT to be used (${variants}): `, 
            (input) => VARIANTS[input] != null
        );
    }

    let variantSpec = VARIANTS[inputSpec['variant']];

    let sector:string = null;
    if(variantSpec.useSector && !inputSpec['sector']) {
        let allSectors = SECTORS.join(', ');
        inputSpec['sector'] = await questionWithValidation(
            `Sector (${allSectors}, all): `, 
            (input) => [...SECTORS, 'all', 'none'].includes(input)
        );

        if(inputSpec['sector'] == 'none') {
            delete inputSpec['sector'];
        }
    }

    return inputSpec
}

async function questionWithValidation(text:string, validate:(input:string)=>boolean): Promise<string> {
    let valid = false;
    let input: string = null;
    while(!valid) {
        input = await question(text);
        valid = validate(input);
        if(!valid) console.log("Invalid input");
    }
    return input;
}

function question(text:string): Promise<string> {
    return new Promise((resolve, reject) => {
        const rl = readline.createInterface({
            input: process.stdin,
            output: process.stdout,
        });

        rl.question(text, (input) => {
            resolve(input);
            rl.close();
        });
    });
}
function createOptionObject(inputSpec:InputSpec): Options {

    let stats = fs.statSync(inputSpec['input']);
    if(!stats.isDirectory()) {
        console.log("The input must be a directory containing the XML module to be transformed. E.g. xml/hv/hv-gr1/hv-gr11");
        process.exit(1);
    }

    let userFolder = process.cwd();
    let resourcesPath = path.normalize(__dirname + '/../resources');
    let xmlBaseFolder = path.normalize(userFolder + path.sep + inputSpec['input']);

    if(!inputSpec['variant'] || !VARIANTS[inputSpec['variant']]) {
        console.log("The variant must be specified. E.g. hv or mbo");
        process.exit(1);
    }

    let variant = VARIANTS[inputSpec['variant']];

    let xsltFile = resourcesPath + path.sep + variant.xsl + '.sef.json';
    if(!fs.existsSync(xsltFile)) {
        console.log("The XSLT file " + xsltFile + " does not exist");
        process.exit(1);
    }

    let sector:string[] = null;
    if(variant.useSector) {
        if(inputSpec['sector']) {
            switch(inputSpec['sector']) {
                case 'all':
                    sector = ['', 'bev', 'asc', 'eng', 'mob', 'ict'];
                    break;
                case 'bev':
                case 'asc':
                case 'eng':
                case 'mob':
                case 'ict':
                    sector = [inputSpec['sector']];
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
        targetFolder: inputSpec['output'],
        sector: sector,
        variant: variant
    };
        
    if(!fs.existsSync(mainOptions.targetFolder)) {
        fs.mkdirSync(mainOptions.targetFolder, { recursive: true });
    }

    return mainOptions;
}
