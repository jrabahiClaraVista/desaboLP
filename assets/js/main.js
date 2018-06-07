// main.js

var $ = require('jquery');
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
    $('[data-toggle="popover"]').popover();
});
