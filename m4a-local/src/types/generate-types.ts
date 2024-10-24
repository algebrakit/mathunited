
/**
 * The attributes needed by the XSLT. Not every attribute is needed for every XSLT.
 */
export interface XSLTParameters  {
    comp: string;
    subcomp: string;
    num: string;
    item: string;
    parent: string;
    component_title: string;
    subcomponent_title: string;
    subcomponent_index: string;
    subcomponent_count: string;
    subcomponent_preceding_id: string;
    subcomponent_following_id: string;
    component_number: string;
    subcomponent_number: string;
    ws_id?: string;
    sector?: string;
}