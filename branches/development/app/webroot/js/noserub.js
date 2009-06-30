$(document).ready(function() {
    $('div#message').animate({'opacity' : 0.25}, 'medium').animate({'opacity' : 1}, 'medium').animate({'opacity' : 0.5}, 'medium').animate({'opacity' : 1}, 'medium');
    
    noserub_theme_start(noserub_context);
    
    if(typeof noserub_start_webcam != "undefined") {
        noserub_start_webcam(noserub_context.base_url);
    }
});