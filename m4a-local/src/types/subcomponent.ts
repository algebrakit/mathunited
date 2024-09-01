import { Component } from "./component";

import path = require('path');
import fs = require('fs');
import xpath = require('xpath');
const dom = require('@xmldom/xmldom').DOMParser;

export class Subcomponent {
    name: string;
    folder: string;
    targetFolder: string;
    domain: string;
    subdomain: string;
    section: string;

    constructor(name:string, component: Component) {
        this.name = name;
        this.folder = component.folder + path.sep + name;
        this.targetFolder = component.targetFolder + path.sep + name;
        let fname = this.folder + path.sep + name + '.xml';
        let content = fs.readFileSync(fname, 'utf8');
        let xml = new dom().parseFromString(content, 'text/xml');
        this.domain = ''+xpath.select('string(/subcomponent/description/domain)', xml);
        this.subdomain = ''+xpath.select('string(/subcomponent/description/subdomain)', xml);
        this.section = ''+xpath.select('string(/subcomponent/description/section)', xml);
    }
}

export function isSubcomponentFile(fname: string): boolean {
    let content = fs.readFileSync(fname, 'utf8');
    let xml = new dom().parseFromString(content, 'text/xml');
    let subcomponentTag = xpath.select('/subcomponent', xml);
    return (subcomponentTag as any).length > 0;
}