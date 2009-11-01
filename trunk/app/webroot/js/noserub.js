$(document).ready(function() {
    $('div#message').animate({'opacity' : 0.25}, 'medium').animate({'opacity' : 1}, 'medium').animate({'opacity' : 0.5}, 'medium').animate({'opacity' : 1}, 'medium');
    
    if(noserub_context.logged_in_identity != null &&
       typeof navigator.geolocation != "undefined") {
        navigator.geolocation.getCurrentPosition(function(data){noserub_context.coords = {latitude: data.coords.latitude, longitude: data.coords.longitude};});
    } else {
        noserub_context.coords = {latitude: 0, longitude: 0};
    }

    noserub_theme_start(noserub_context);
    
    if(typeof noserub_start_webcam != "undefined") {
        noserub_start_webcam(noserub_context);
    }
    
    if(typeof GBrowserIsCompatible != "undefined") {
        var latitude = 0.0;
        var longitude = 0.0;
        
        if(typeof noserub_context.location != "undefined") {
            latitude = noserub_context.location.latitude;
            longitude = noserub_context.location.longitude;
        } else if(typeof noserub_context.event != "undefined") {
            latitude = noserub_context.event.latitude;
            longitude = noserub_context.event.longitude;
        } else if(typeof noserub_context.identity != "undefined") {
            latitude = noserub_context.identity.latitude;
            longitude = noserub_context.identity.longitude;
        }
        if(latitude != 0.0 && longitude != 0.0 && GBrowserIsCompatible()) {
            var map = new GMap2(document.getElementById("widget_map"));
            map.addControl(new GSmallMapControl());
            var point = new GLatLng(latitude, longitude);
            map.setCenter(point, 13);
            map.addOverlay(new GMarker(point));
        }
     }
     
     $('.wysiwyg').uEditor({toolbarItems: ['htmlsource', 'bold', 'italic', 'link', 'unorderedlist', 'orderedlist']});
});