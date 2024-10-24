export interface VariantSetting {
    xsl: string;
    useSector: boolean;
    items: {[key:string]:any};
    sources: string[]
}

export const SECTORS:string[] = ['bev', 'asc', 'eng', 'mob', 'ict', 'all'];


// the items in a subcomponent
// the order is relevant
const ITEMS = {
    'introduction': {
        order: 1
    },
    'explore': {
        order: 2
    },
    'explanation': {
        order: 3,
        multiple: true, // there can be multiple explanations
    },
    'theory': {
        order: 4,
        exercises: true
    },
    'example': {
        order: 5,
        multiple: true,
        exercises: true
    },
    'digest': {
        order: 6
    },
    'summary': {
        order: 7
    },
    'test': {
        order: 8
    },
    'application': {
        order: 9
    },
    'extra': {
        order: 10
    },
    'background': {
        order: 11
    },
    'exam': {
        order: 12
    },
    'answers': {
        order: 13,
        always: true
    }
}

export const VARIANTS:{[key:string]:VariantSetting} = {
    hv: {
        xsl: 'm4a_view',
        useSector: false,
        items: ITEMS,
        sources: ['css', 'js', 'sources', 'sources_ma']
    },
    // hv_en: {
    //     xsl: 'm4a_view_en',
    //     useSector: false
    // },
    saba: {
        xsl: 'm4a_view_saba',
        useSector: false,
        items: {
            'theory': ITEMS['theory'],
            'digest': ITEMS['digest'],
            'answers': ITEMS['answers']
        },
        sources: ['css', 'js', 'sources', 'sources_ma']
    },
    mbo: {
        xsl: 'm4a_view_mbo',
        useSector: true,
        items: ITEMS,
        sources: ['css', 'js', 'sources', 'sources_ma']
    },
    wm: {
        xsl: 'wm_view',
        useSector: false,
        items: {
            '': {order:1},
            'answers': {always: true, order:2}
        },
        sources: ['css', 'js', 'sources', 'sources_wm']
    }
}
export const PARENT = 'https://math4all.nl';
