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
    }
};