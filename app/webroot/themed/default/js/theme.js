function noserub_theme_start(context) {
    $('a.toggle').click(function(e) {
        e.preventDefault();
        if($(this).text() == '(-)') {
            $(this).text('(+)');
        } else {
            $(this).text('(-)');
        }
        $(this).siblings('ul').toggle();
    });
    
    console.dir(context);
}