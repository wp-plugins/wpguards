(function($) {
    'use strict';

    jQuery(document).ready(function(){

        // generate uptime chart
        var chartElement = jQuery('#uptime-response-chart');
        var parent = jQuery('#uptime-response-chart').parent();

        chartElement.attr('width', parent.width());

        new Chart(chartElement.get(0).getContext('2d')).Line(WPGuards.response);

        // uptime chart 
        var chartElement = jQuery('#uptime-chart');
        var parent = jQuery('#uptime-chart').parent();

        //chartElement.attr('width', parent.width());
        //chartElement.attr('height', parent.width());

        new Chart(chartElement.get(0).getContext('2d')).Doughnut(WPGuards.uptime);

        // uptime chart history
        var chartElement = jQuery('#uptime-history-chart');

        //chartElement.attr('width', parent.width());
        //chartElement.attr('height', parent.width());

        new Chart(chartElement.get(0).getContext('2d')).Doughnut(WPGuards.history, {
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%>about <%= Math.floor(value/60) %> hours"
        });

        var height = jQuery('#uptime-chart').parents('.postbox').height();
        jQuery('.column_3 .postbox').height(height);
    });

})(jQuery);