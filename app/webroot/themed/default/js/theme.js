function noserub_theme_start(context) {
	if(typeof console != "undefined") {
    	console.dir(context);
	}
	
    $('a.toggle').click(function(e) {
        e.preventDefault();
        if($(this).text() == '(-)') {
            $(this).text('(+)');
        } else {
            $(this).text('(-)');
        }
        $(this).siblings('ul').toggle();
    });
    
	// unobstrusive language drop down menu
	var langForm = $("#hd li.lang form");
	var langText = $("label", langForm).text();
	$("*", langForm).hide();
	$("#hd ul li:not(.lang)").addClass("left");
	$("#hd ul li.lang").css("margin-top", "0");
	$(langForm).append('<a href="javascript:void(0);">' + langText + '</a><ul class="lang-list"></ul>');
	var langToggle = $("a", langForm);
	var langList = $("ul.lang-list", langForm);
	$(langList).hide();
	$("#ConfigLanguage option", langForm).each(function() {
		var html = '<li';
		if(this.selected) {
			html += ' class="selected"';
		}
		html += '><a href="javascript:void(0);">' + $(this).text() + '</a></li>';
		$(langList).append(html);
	});
	$("#hd li.lang").mouseenter(function() {
		langList.toggle();
	});
	$("#hd li.lang").mouseleave(function() {
		langList.toggle();
	});
	$("ul.lang-list a", langForm).click(function() {
		var parentSet = $(this).parent().parent().children();
		var i = $(parentSet).index($(this).parent());
		$("#ConfigLanguage", langForm).get(0).selectedIndex = i;
		langForm.submit();
	});
}
