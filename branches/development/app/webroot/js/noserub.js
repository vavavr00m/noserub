$(document).ready(function() {
    $('div#message').animate({'opacity' : 0.25}, 'medium').animate({'opacity' : 1}, 'medium').animate({'opacity' : 0.5}, 'medium').animate({'opacity' : 1}, 'medium');
    
    if(typeof navigator.geolocation != "undefined") {
        navigator.geolocation.getCurrentPosition(function(data){noserub_context.coords = {latitude: data.coords.latitude, longitude: data.coords.longitude};});
    } else {
        noserub_context.coords = {latitude: 0, longitude: 0};
    }

    noserub_theme_start(noserub_context);
    
    if(typeof noserub_start_webcam != "undefined") {
        noserub_start_webcam(noserub_context);
    }
});