/* 
 * Copyright (C) 2013 Martijn Slob <m.slob@math4all.nl>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//define(['jquery', 'app/TinyMCE', 'app/ContextMenu', 'algebrakit/Widget', 'elfinder', 'jqueryui'], function($, Editor, ContextMenu, akitWidget) {
define(['jquery', 'app/TinyMCE', 'app/ContextMenu',  'app/AlgebraKITSpecHandler', 'jqueryui'], function($, Editor, ContextMenu, AlgebraKITSpecHandler) {
    var isDocChanged = false;
    var root = null;  //set on init
    
    
    function prepareImages(repo, baseRepo) {
        //check if images exist, if not fallback to backup repository
        if(baseRepo && baseRepo.length>0){
            $('img').each(function() {
                var img = $(this);
                if(img[0].naturalWidth===0) {
                    var src = img[0].src;
                    img[0].src = src.replace(repo,baseRepo);
                }
            });
        }
        //explicitly set WIDTH and HEIGHT attributes on images for proper usage in tinymce
        $('img').each(function() {
            var img = $(this);
            var w = img.width();
            var h = img.height();
            if(w>0){
                img.attr('width',''+w);
            }
            if(h>0){
                img.attr('height',''+h);
            }
        });
    }
    
    //handlers to shift an item (exercise) to up or down 
    function setShiftHandlers() {
        $('.item-container').each(function() {
            var parent = $(this);
            $('.shift-handle-next',this).click(function() {
                var _num = 1+parseInt(parent.attr('num'));
                var nextLoc = $('#item-container-'+_num);
                if(nextLoc.parents('.item-container').length>0) {
                    nextLoc = nextLoc.parents('.item-container').first();
                }
                parent.insertAfter(nextLoc);
                labelAnchors();
                isDocChanged=true;
            });
            $('.shift-handle-prev',this).click(function() {
                var _num = -2+parseInt(parent.attr('num'));
                var nextLoc = $('#item-container-'+_num);
                if(nextLoc.parents('.item-container').length>0) {
                    nextLoc = nextLoc.parents('.item-container').first();
                }
                parent.insertAfter(nextLoc);
                labelAnchors();
                isDocChanged=true;
            });
        });
    }

    //open or close an item (section of a document)
    function addToggleItemContainerHandler() {
        $('.m4a-editor-item-container', root).each(function() {
            var par = $(this);
            $('.m4a-editor-item-title',par).unbind("click").click(function() {
                par.toggleClass('open');
            });
        });
    }
    
    function toggleVisibleButton(className, elm) {
        $('.'+className+'-button',elm).unbind('click').click(function() {
            var par = $(this).parent();
            var cont = $('.'+className+'-content',par);
            cont.toggleClass('visible');
            $(this).toggleClass('visible')
        });
    }
    
    function insertActions(elm) {
        //opening/closing of sections
        addToggleItemContainerHandler();
        AlgebraKITSpecHandler.init(elm);
        
        $("[no-edit]").addClass("no-edit");

        $('p,ul.paragraph,ol.paragraph,table,img.paperfigure',elm).each(function() {
            if ($(this).attr('no-edit')) {
                //this is an xml tag. Do not allow editing, because it would ruin the element. set
            } else {
                var parent = $(this);
                if(  parent.parents('.tiny-editor').length===0   //not already attached to an editor
                  && parent.parents('div[tag="componentcontent"]').length>0 //within editable content
                  && !parent.attr('_done')) {

                    new Editor.editor(parent);
                }
                
            }
        });
        $('*[_done]').removeAttr('_done');
        
        toggleVisibleButton('block', elm);
        toggleVisibleButton('worksheet', elm);
        toggleVisibleButton('answer', elm);
        toggleVisibleButton('example-answer',elm);
        $('.editor-choice-exercise-label',elm).unbind('click').click(function() {
            var alternative=$(this).parents('div[tag="alternative"]');
            var state = alternative.attr('state');
            if(state==='yes') {
                alternative.attr('state','no');
            } else {
                alternative.attr('state','yes');
            }
        });
        
    }

    return {
       init: function(repo, baseRepo) {
           root = $('div.editorDiv');
           prepareImages(repo, baseRepo);
           this.labelAnchors();
           setShiftHandlers();
           insertActions(root);
           ContextMenu.init(root);
           //add warning on 'dangerous' links that leave the editor (needed as window.onbeforeunload does not work on IOS)
           $('a._warn_if_doc_changed_').each(function(){
               var elm = $(this);
               var target = elm.attr('href');
               elm.attr('href','#');
               elm.attr('url', target);
               elm.click(function() {
                    var goOn = true;
                    var isiPad = navigator.userAgent.match(/iPad/i) != null;
                    if(isiPad && isDocChanged) {
                        goOn = confirm('Wilt u de editor verlaten? Wijzigingen die u niet heeft opgeslagen gaan verloren.');
                    }
                    if (goOn) {
                        elm.attr('href', target);
                        window.location.href = target;
                    }
               });
           });
       },
       reinit : function(elm) {
           insertActions(elm);
           ContextMenu.init(elm);
           this.labelAnchors();
       },
       isChanged: function() { return isDocChanged; },
       setChanged:function(b) { isDocChanged = b; },
       getRoot: function() {return root;},
       isLocked: function() { return $('#locked-message').length>0; },
       labelAnchors: function() {
            var shiftId=0;
            $('.item-container').each(function(){
                $(this).attr('id','item-container-'+shiftId);
                $(this).attr('num',shiftId);
                shiftId++;
            });
       },
       prepareForSubmit: function(elm) {
            Editor.closeAll();
            if(!elm) elm = root;

           //bugfix in MathJax: a '<' symbol messes up the xml document
            $('script',elm).each(function() { 
               var str = this.text.replace('<','&lt;');
               $(this).text(str);
            });
       }
   };
});