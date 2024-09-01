export interface VariantSetting {
    xsl: string;
    useSector: boolean;
}

export const VARIANTS:{[key:string]:VariantSetting} = {
    hv: {
        xsl: 'm4a_view',
        useSector: false
    },
    hv_en: {
        xsl: 'm4a_view_en',
        useSector: false
    },
    mbo: {
        xsl: 'm4a_view_mbo',
        useSector: true
    }
}

// the items in a subcomponent
// the order is relevant
export const ITEMS = {
    'introduction': {},
    'explore': {},
    'explanation': {
        multiple: true, // there can be multiple explanations
    },
    'theory': {
        exercises: true
    },
    'example': {
        multiple: true,
        exercises: true
    },
    'digest': {},
    'extra': {},
    'summary': {},
    'test': {},
    'application': {},
    'background': {},
    'exam': {},
    'answers': {
        always: true
    }
}

export const PARENT = 'https://math4all.nl';
