(function($) {
    'use strict';

    jQuery(document).ready(function(){

        jQuery('.scans .scan').click(function(){
            jQuery(this).find('.scan').slideToggle();
        });
        
    });

})(jQuery);
