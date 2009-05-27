$(document).ready(function() {
    $('a.toggle').click(function(e) {
        e.preventDefault();
        if($(this).text() == '(-)') {
            $(this).text('(+)');
        } else {
            $(this).text('(-)');
        }
        $(this).siblings('ul').toggle();
    })
});
