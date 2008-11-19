$(document).ready(function() {
    $nr = noserub();
    $nr.start(nr_data);
});

var noserub = function() {
    // If the context is global, return a new object
    if(window == this) {
        return new noserub();
    }
    
    return true;
};

noserub.fn = noserub.prototype = {
    debug: false,
    
    start: function(data) {
        if(typeof data.debug != 'undefined') {
            this.debug = data.debug;
        }
        this.log('noserub_js started');
        
        try {
            this[data.controller+'_'+data.action]();
            this.log(data.controller+'_'+data.action + '()');
        } catch(e) {
            this.log(data.controller+'_'+data.action + '() not found!');
        }
                
        $('div#message.success').animate({'opacity' : 0.25}, 'medium').animate({'opacity' : 1}, 'medium').animate({'opacity' : 0.5}, 'medium').animate({'opacity' : 1}, 'medium');
    },
    
    log: function(msg) {
        if(this.debug === true) {
            console.log(msg);
        }
    },
    
    Accounts_add_step_4_friends: function() {
        $('select').change(function() {
            $(this).parent('div').find('input[value="2"]').attr('checked', 'checked');
        });
        $(':text').focus(function() {
            $(this).parent('div').find('input[value="1"]').attr('checked', 'checked');
        });
    },
    
    Syndications_add: function() {
        $('.accounts_of_contact').hide();
        $('.mynetwork').hide();
        
        $(':checkbox.check_all').change(function() {
            if($(this).attr('checked') == true) {
                $(this).parent().parent().next('.accounts_of_contact').find(':checkbox').attr({checked: true});
            }
        });
        
        $('.accounts_of_contact').find(':checkbox').change(function() {
            if($(this).attr('checked') != true) {
                $(this).parent().parent().prev().find(':checkbox.check_all').attr({checked: false});
            }
        });
        
        $('a.specify').click(function(e) {
            e.preventDefault();
            $(this).parent().parent().next('.accounts_of_contact').toggle();
        });

        $('a.specifynetwork').click(function(e) {
            e.preventDefault();
            $('.mynetwork').toggle();
        });
    },
    
    social_stream_items: function() {
        $('ul.extended').hide();
        
        $('div#network span.more a').click(function(e) {
            e.preventDefault();
            $(this).parent().next().next().next().toggle('medium');
        });
    },
    
    Contacts_network: function() {
        this.micropublish_char_count();
        this.social_stream_items();
        if($('.locator option').size() > 1) {
            $('.locator :text').hide();
            $('.locator #locator_name').hide();
        }
        $('.locator select').change(function() {
            selected = $('.locator option:selected');
            if(selected.val() != 0) {
                $('.locator :text').hide();
                $('.locator #locator_name').hide();
            } else {
                $('.locator :text').attr({value : ''});
                $('.locator :text').show();
                $('.locator #locator_name').show();
            }
        });
    },
    
    Identities_index: function() {
        this.micropublish_char_count();
        this.social_stream_items();
    },
    
    Identities_social_stream: function() {
        this.social_stream_items();
    },
    
    Contacts_edit: function() {
        $('.hidejs').hide();
        $(':checkbox').hide();
        $(':radio').hide();
        
        $('.contact_type.check').click(function() {
            if($(this).prev().attr('checked')) {
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }            
        });
        $('.contact_type.radio').click(function(e) {
            $(this).siblings().removeClass('checked');
            if($(this).prev().attr('checked')) {
                e.preventDefault();
                $(this).siblings(':first').attr({checked: true});
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }
        });
    },
    
    micropublish_char_count: function() {
        $('#MicropublishValue').keyup(function(e) {
            $('#MicropublishCount').html(140-this.value.length);
        });
    }
};