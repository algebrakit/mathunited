import { Component } from "./component";

import path = require('path');
import fs = require('fs');
import xpath = require('xpath');
import { removeDoctype } from "../util/xml";
const dom = require('@xmldom/xmldom').DOMParser;

export class Subcomponent {
    id: string;
    prevId: string;
    followingId: string;
    component: Component;
    title: string;
    number: number;
    folder: string;
    file: string
    targetFolder: string;
    domain: string;
    subdomain: string;
    section: string;

    constructor(file:string, component: Component, nr:number, props: any) {
        this.component = component;
        this.number = nr;
        this.title = props.title;
        this.id = props.id;
        this.file = file;
        this.prevId = props.prev_id;
        this.followingId = props.following_id;
        this.folder = path.dirname(file);
        this.targetFolder = component.targetFolder + path.sep + path.basename(file, '.xml');
        let content = fs.readFileSync(file, 'utf8');
        content = removeDoctype(content);
        let xml = new dom().parseFromString(content, 'text/xml');
        this.domain = ''+xpath.select('string(/subcomponent/description/domain)', xml);
        this.subdomain = ''+xpath.select('string(/subcomponent/description/subdomain)', xml);
        this.section = ''+xpath.select('string(/subcomponent/description/section)', xml);
    }
}

export function isSubcomponentFile(fname: string): boolean {
    try{
        let content = fs.readFileSync(fname, 'utf8');
        let xml = new dom().parseFromString(content, 'text/xml');
        let subcomponentTag = xpath.select('/subcomponent', xml);
        return (subcomponentTag as any).length > 0;
    } catch(e) {
        return false;
    }
}