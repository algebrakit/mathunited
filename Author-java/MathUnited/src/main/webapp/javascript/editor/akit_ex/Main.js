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

//This is a stub for the actual AlgebraKIT-engine which runs on the server.
define(['jquery', 'akitex/Exercise','mathquill','mathjax'], function($, Exercise,mathquill) {
    MathJax.Hub.Startup.onload();
    var idCounter = 0;
    
    //utility plugin to prevent automatic zooming on double tap on iPhone
    //note: does not work on Android (unless using Chrome)!
    (function($) {
      $.fn.nodoubletapzoom = function() {
          $(this).bind('touchstart', function preventZoom(e) {
            var fingers = e.originalEvent.touches.length;
            if (fingers > 1) return; // not double-tap

            e.preventDefault(); // double tap - prevent the zoom
            // also synthesize click events we just swallowed up
            $(this).trigger('click');
          });
      };
    })($);

    return {
        isTouchDevice: ("ontouchstart" in document.documentElement),
        exercises: [],
        activeExercise: null,
        addExercise: function(parent) {
          var  ex = new Exercise(idCounter, parent);
          idCounter+=1;
          this.exercises.push(ex);
          ex.init();
        },
        setActiveExercise: function(ex) {
            if(this.activeExercise) {
                if(ex.id === this.activeExercise.id) return;
                this.activeExercise.setInactive();
            }
            this.activeExercise = ex;
            ex.setActive();
        }
    };
  }
);


