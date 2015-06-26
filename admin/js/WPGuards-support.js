(function($) {
    'use strict';

    jQuery(document).ready(function() {

        // Generate user avatar
        var userAvatar = new Identicon(WPGuards.userAvatar, 80, 0, [255,255,255]).toString();
        
        // Generate WPGuards avatar
        var supportAvatar = new Identicon(WPGuards.supportAvatar, 80, 0, [255,255,255]).toString();
        
        // Get Receipt
        jQuery.ajax({
            url  : WPGuards.ajaxURL,
            data : {action:'getReceipt'}
        }).done(function(response) {
            jQuery('#receipt').removeClass('loader').html(response);
        });
        
        // Get Tickets
        jQuery.ajax({
            url  : WPGuards.ajaxURL,
            data : {action:'getTickets'}
        }).done(function(response) {
            jQuery('#tickets_holder').removeClass('loader').html(response);
        });

        // Get comments
        jQuery(document).on('click', 'tr.ticket', function() { 
            var ticketID    = jQuery(this).attr('data-id'),
                commentsDiv = jQuery(this).next().find('.comments');

            jQuery(this).next().find('.comments-expand').slideToggle();

            if (commentsDiv.hasClass('loaded')) {
                return;
            }

            jQuery.ajax({
                url  : WPGuards.ajaxURL,
                data : {
                    action : 'getComments',
                    ticket : ticketID
                }
            }).done(function(response) {
                commentsDiv.removeClass('loader').addClass('loaded').html(response);
            });
        });

    });

})(jQuery);
