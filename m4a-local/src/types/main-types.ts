import { VariantSetting } from "../config";

export interface Options {
    userFolder: string;
    xmlBaseFolder: string;
    resourcesPath: string;
    xsltJSON: string;
    targetFolder: string;
    sector: string[];
    variant: VariantSetting
}
