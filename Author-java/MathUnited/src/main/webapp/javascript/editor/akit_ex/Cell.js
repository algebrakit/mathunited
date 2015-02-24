/* 
 * Copyright (C) 2013 AlgebraKIT <info@algebrakit.nl>
 *
 */

// Strategic cell:
//   wraps a header with hint-info and a container with nested cells
//   the hint explains what is the goal of the currect derivation
// Procedure cell:
//   wraps a header with a hint at procedure level and a single result
//   this result is either a nested cell or a simple expression
// these cells are hierarchically ordered, according to
// - key : list of VariableDefinition-ids in the solution model
// - var   : model variable that corresponds to the *derivation* result
// - procid: identifies the step (procedure) in the current derivation
define(["require","jquery"], function(require, $) { 
    var cellHTML =
           '<div class="akit-input-cell" cell-id="">'
     +        '<div class="akit-cell-header">'
     +            '<div class="akit-hint-container">'
     +              '<div class="akit-hint-main"></div>'
     +              '<div class="akit-hint-detail"></div>'
     +            '</div>'
     +        '</div>'
     +        '<div class="akit-cell-container"/>'
     +        '<div class="akit-cell-footer">'
     +            '<div class="akit-hint-container">'
     +              '<div class="akit-hint-main"></div>'
     +              '<div class="akit-hint-detail"></div>'
     +            '</div>'
     +        '</div>'
     +        '<div class="akit-cell-placeholder">'
     +            '<span class="akit-placeholder-icon">&#9654;</span>'
     +            '<span class="akit-input-label"></span>'
     +            '<span class="akit-placeholder-expression"></span>'
     +        '</div>'
     +     '</div>';

    //akit-cell-container contains 
    var inputHTML = 
           '<div class="akit-input-elm">'
     +       '<span class="akit-result-icon"></span>'
     +       '<span class="akit-input-label"></span>'
     +       '<span class="akit-input-expression"></span>'
     +       '<span class="akit-item-msg"/>'
     +       '<span class="akit-buggy"/>'
     +     '</div>';
     
    var lastHintChecksum;
    //spec:
    // - key
    // - answerVariable
    // - exerciseItem : parent exercise item
    // - domParent : parent element for the cells (only required if parent===null)
    // - parent    : parent Cell, of null
    // - nestingLevel
    // - input     : rendered input expression
    // - solutionCell: WKSolutionCell, generated by AlgebraKIT
    // - isCorrect : whether input is correct or not
    // - label     : fixed prefix for formula
    function Cell(spec) {
        var cellContainer_jq;
        var headerContainer_jq;
        var footerContainer_jq;
        var domParent = spec.parentDOM;
        if(spec.parent) domParent = spec.parent.getContainer_jq();
        var nestingLevel = 0;
        if(spec.parent) nestingLevel = spec.parent.nestingLevel+1;
        if(spec.key===undefined) spec.key = spec.solutionCell.key;
        
        var _obj = {
            /** id = [ 'varname_number' ]. Determined by AlgebraKIT, defines hierarchy of cells. */
            id: spec.id,
            answerVariable: spec.answerVariable,
            exerciseItem: spec.exerciseItem,
            parent: spec.parent,
            key : spec.key,
            childs: [],
            nestingLevel: nestingLevel,
            label : spec.label,
            lead: null,
            dom: null,
            init: function() {
                this.dom = $(cellHTML);
                if(!this.key || this.key.length<=1) $('.akit-cell-placeholder',this.dom).remove();
                var keyStr='';
                for(var ii=0; ii<this.key.length; ii++) {
                    keyStr+=this.key[ii]+';';
                }
                this.dom.attr('cell-id', keyStr);
                domParent.append(this.dom);
                cellContainer_jq = $('.akit-cell-container', this.dom);
                headerContainer_jq = $('.akit-cell-header', this.dom);
                footerContainer_jq = $('.akit-cell-footer', this.dom);
                if(this.parent) this.parent.addChild(this);
                this.key = spec.key;
                this.lead = spec.lead;
                return this;
            },
            getContainer_jq: function() {return cellContainer_jq;},
            addChild: function(child) { this.childs.push(child); },
            addInput: function(inputMML, inputLatex, inputAKIT, resultType) {
                var elm = $(inputHTML);
                cellContainer_jq.append(elm);
                $('.akit-hint-main',footerContainer_jq).html('');
                $('.akit-hint-detail',footerContainer_jq).html('');
                $('.akit-input-expression',elm).html(inputMML);
                switch(resultType){
                    case 'NO_MATCH': $('.akit-result-icon', elm)[0].className = 'akit-result-icon akit-icon-error'; break;
                    case 'FINISHED': $('.akit-result-icon', elm)[0].className = 'akit-result-icon akit-icon-success'; 
                                     if(inputAKIT) this.lead = inputAKIT;
                                     break;
                    default: $('.akit-result-icon', elm)[0].className = 'akit-result-icon akit-icon-correct'; 
                             if(inputAKIT) this.lead = inputAKIT; 
                             break;
                }
                if(this.label){
                    $('.akit-input-label',elm).html(this.label);
                    $('.akit-input-label',elm).css('display','inline-block');
                }
                //on click, copy the formula into the active editor
                var latex=inputLatex;
                elm.click(function(){
                    _obj.exerciseItem.editor.latex(latex);
                    _obj.exerciseItem.editor.focus();
                });
                lastHintChecksum=null;
                MathJax.Hub.Queue(["Typeset", MathJax.Hub, elm[0]]);
            },
            isVirgin: function() {return cellContainer_jq.children('.akit-input-elm').length===0;},
            //display a hint, input is WKHint. 
            //spec:
            // - spec.html 
            // - spec.wkhint
            setHint: function(spec) {
                var hintMain, hintDetail;
                if(spec.html) hintMain = spec.html;
                else hintMain = spec.wkhint.hintMain;
                if(spec.wkhint) hintDetail = spec.wkhint.hintDetail;

                var msg = hintMain;
                if(!msg) msg=hintDetail;
                if(!msg) return;
                if(msg===lastHintChecksum) return;
                lastHintChecksum=msg;
                
                //var prepTxt = ' ';
                //for(var ii=0; ii<this.nestingLevel; ii++) prepTxt='&#9654;'+prepTxt;
                var container;
                if(spec.wkhint && spec.wkhint.type==='step') container = footerContainer_jq;
                else container = headerContainer_jq;
                var jq_sd = $('.akit-hint-main',container).css('opacity','0');
                var jq_hint = $('.akit-hint-detail',container).css('opacity','0');
                if(hintMain) jq_sd.html(hintMain);
                if(hintDetail) {
                    jq_hint.html(hintDetail);
                    jq_hint.css('display','inline-block');
                } else jq_hint.html('');
                MathJax.Hub.Queue(["Typeset", MathJax.Hub, container[0]]);
                jq_sd.animate({opacity:1},500);
                jq_hint.animate({opacity:1},500);
            },
            getCell: function(key, doCreate) {
                var result = null;
                var n = this.key.length;
                
                if(key.length === this.key.length) {
                    return this;
                } else {
                    for(var ii=0; ii<this.childs.length; ii++) {
                        if(key[n]===this.childs[ii].key[n]){
                            result = this.childs[ii].getCell(key, doCreate);
                            break;
                        }
                    }
                    if(doCreate && !result) {
                        //create a new child cell
                        var newKey = [];
                        for(var ii=0; ii<=n; ii++) newKey.push(key[ii]);
                        var cell = new Cell({
                            key          : newKey,
                            solutionCell   : null,
                            parent         : this,
                            exerciseItem   : this.exerciseItem,
                            label          : null
                        });
                        this.childs.push(cell);
                        result = cell.getCell(key, doCreate);
                    }
                }
                
                return result;
            },
            clearFeedback: function() {
                $('.akit-hint-main',headerContainer_jq).html('');
                $('.akit-hint-detail',headerContainer_jq).html('').css('display','none');
                $('.akit-hint-main',footerContainer_jq).html('');
                $('.akit-hint-detail',footerContainer_jq).html('');
            }
        };
        
        return _obj.init();
    }
    return (Cell);
}
);