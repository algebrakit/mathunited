import { USER_OPTIONS } from "../options";

const path = require('path');
const fs = require('fs');
const xpath = require('xpath');
const dom = require('@xmldom/xmldom').DOMParser;

export class Component {
    name: string;
    folder: string;
    targetFolder: string;
    title: string;
    subtitle: string;
    subcomponents: string[]
    
    constructor(fname: string, sector: string) {
        this.name = path.basename(fname, '.xml');
        this.folder = path.dirname(fname);

        let content = fs.readFileSync(fname, 'utf8');
        let xml = new dom().parseFromString(content, 'text/xml');
        this.title = xpath.select('string(/component/description/title)', xml);
        this.subtitle = xpath.select('string(/component/description/subtitle)', xml);
        this.targetFolder = USER_OPTIONS.targetFolder + path.sep + 'content';

        if(USER_OPTIONS.variant && USER_OPTIONS.variant.useSector) {
            let _sector = sector || 'no-sector';
            this.targetFolder = this.targetFolder + path.sep + _sector;
        }

        this.targetFolder = this.targetFolder + path.sep + this.name

        // get all subfolders that start with the component name
        this.subcomponents = [];
        let files = fs.readdirSync(this.folder);
        for(let file of files) {
            let stat = fs.statSync(this.folder + path.sep + file);
            if(stat.isDirectory() && file.startsWith(this.name)) {
                this.subcomponents.push(file);
            }
        }
    }
}

export function isComponentFile(fname: string): boolean {
    let content = fs.readFileSync(fname, 'utf8');
    let xml = new dom().parseFromString(content, 'text/xml');
    let subcomponentTag = xpath.select('/component', xml);
    return subcomponentTag.length > 0;
}