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

    // Display or hide password, Checkou out the DOM order to make sure it works properly
    $('.visible_pwd').on('click', function(event){
        if($(this).attr('data') == "hidden"){
            $(this).children('i').removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).attr('data','visible')
            $(this).parent().prev('input').attr('type','text');
        }
        else{
            $(this).children('i').removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).attr('data','hidden')
            $(this).parent().prev('input').attr('type','password');
        }
    });

});
