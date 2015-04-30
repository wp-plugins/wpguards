(function($) {
    'use strict';

    jQuery(document).ready(function(){

        // generate calendar
        jQuery('#calendar').fullCalendar({
            header              : 
            { 
                left  : 'title',
                right : 'prev,next'
            },
            firstDay            : 1,
            events              : WPGuards.calendar,
            eventAfterAllRender : function() {
                jQuery('.fc-day-number').wrapInner('<span></span>');
            }
        });

        // generate uptime chart
        var chartElement = jQuery('#uptime-chart').get(0).getContext('2d');
        new Chart(chartElement).Doughnut(WPGuards.uptime);
        
    });

})(jQuery);
