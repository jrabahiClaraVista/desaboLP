// main.js

//var $ = require('jquery');
// require jQuery normally
const $ = require('jquery');
// create global $ and jQuery variables
global.$ = global.jQuery = $;


// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap');

// require the JavaScript
//require('bootstrap-star-rating');
// require 2 CSS files needed
//require('bootstrap-star-rating/css/star-rating.css');
//require('bootstrap-star-rating/themes/krajee-svg/theme.css');

// or you can include specific pieces
// require('bootstrap-sass/javascripts/bootstrap/tooltip');
// require('bootstrap-sass/javascripts/bootstrap/popover');

$(document).ready(function() {
    //$('[data-toggle="popover"]').popover();

    var element, circle, d, x, y;


    $("button span")
        .click(function(e){ 

            e.preventDefault();

            element = $(this);
          
            if(element.find(".circle").length == 0)
                element.prepend("<span class='circle'></span>");
                
            circle = element.find(".circle");
            circle.removeClass("animate");
            
            if(!circle.height() && !circle.width())
          {
                d = Math.max(element.outerWidth(), element.outerHeight());
                circle.css({height: d, width: d});
            }
            
            x = e.pageX - element.offset().left - circle.width()/2;
            y = e.pageY - element.offset().top - circle.height()/2;
            
            circle.css({top: y+'px', left: x+'px'}).addClass("animate");
    })

});
