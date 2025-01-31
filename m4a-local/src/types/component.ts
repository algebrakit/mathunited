import { USER_OPTIONS } from "../options";
import { removeDoctype } from "../util/xml";
import { Subcomponent } from "./subcomponent";

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
    number: number;
    subcomponents: Subcomponent[]
    
    constructor(fname: string, sector: string) {
        this.name = path.basename(fname, '.xml');
        this.folder = path.dirname(fname);

        let content = fs.readFileSync(fname, 'utf8');
        content = removeDoctype(content);
        let xml = new dom().parseFromString(content, 'text/xml');
        this.title = xpath.select('string(/component/description/title)', xml);
        this.subtitle = xpath.select('string(/component/description/subtitle)', xml);
        this.number = xpath.select('number(/component/description/number)', xml);
        this.targetFolder = USER_OPTIONS.targetFolder + path.sep + 'content';

        if(USER_OPTIONS.variant && USER_OPTIONS.variant.useSector) {
            let _sector = sector || 'no-sector';
            this.targetFolder = this.targetFolder + path.sep + _sector;
        }

        this.targetFolder = this.targetFolder + path.sep + this.name

        // get all subfolders that start with the component name
        this.subcomponents = xpath.select('/component/subcomponents/subcomponent', xml).map((elm,_nr) => {
            let nr = xpath.select('number(@number)', elm);
            if(!nr) nr = _nr;
            let id = xpath.select('string(@id)', elm);
            let title = xpath.select('string(title)', elm);
            let file =  xpath.select('string(file)', elm);
            let abfile = this.folder + path.sep + file;
            if(fs.existsSync(abfile)) {
                return new Subcomponent(abfile, this, nr, {id: id, title: title, prev_id: null, following_id: null});
            } else {
                return null;
            }
        }).filter((subcomponent) => subcomponent !== null);
        this.subcomponents.forEach((subcomponent, index) => {
            if(index > 0) {
                subcomponent.prevId = this.subcomponents[index-1].id;
            }
            if(index < this.subcomponents.length - 1) {
                subcomponent.followingId = this.subcomponents[index+1].id;
            }
        });
    }
}

export function isComponentFile(fname: string): boolean {
    try{
        let content = fs.readFileSync(fname, 'utf8').trim();
        let xml = new dom().parseFromString(content, 'text/xml');
        let componentTag = xpath.select('/component', xml);
        return componentTag.length > 0;    
    } catch(e) {
        return false;
    }
}