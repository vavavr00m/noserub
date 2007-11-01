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
        } catch(e) {
            this.log(data.controller+'_'+data.action + '() not found!');
        }
                
        $('div#message.success').animate({'opacity' : 0.25}, 'medium').animate({'opacity' : 1}, 'medium').animate({'opacity' : 0.5}, 'medium').animate({'opacity' : 1}, 'medium');
    },
    
    log: function(msg) {
        if(this.debug === true) {
            window.console.log(msg);
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

        $('a.shownetwork').click(function(e) {
            e.preventDefault();
            $(this).parent().parent().next('.mynetwork').toggle();
        });
    }
};