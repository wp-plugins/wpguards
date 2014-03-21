(function(jQuery) {
	jQuery(function() {
	
		jQuery('#backup_restore').submit(function(evt) {

			evt.preventDefault();

			jQuery( "#request_send" ).slideDown( "slow" );

			jQuery.post(ajaxurl, {
				
				action:	'restore_backup',							
				nonce: jQuery('#request_backup_restore').val(),	
				file: jQuery('#file').val(),	
				task: jQuery('#task').val(),	
				result: jQuery('#result').val(),	
					
			}, function(response) {
							
					if( 'done' == response ) {
					
						jQuery( "#request_send" ).slideUp( "slow", function() {
						   
							jQuery( "#restored" ).slideDown( "slow" );

						});

						
					} else if( 'noonce' == response ) {
					
						jQuery( "#request_send" ).slideUp( "slow", function() {
						   
							jQuery( "#wrong_noonce" ).slideDown( "slow" );

						});
						
					} else {
					
						jQuery( "h2.nav-tab-wrapper" ).after( '<div class="error"><p>' + response + '</p></div>' );
						
					}
		
			});

			// restoring progress bar
			var file_path = jQuery('#file_path').val();
			var file_size = jQuery('#file_size').val();

			jQuery( "#restore_cell" ).html( '<div id="progress"></div>' );
			jQuery( "#progress" ).load( "../wp-content/plugins/wpguards/assets/js/check_backup_size.php" );

            setInterval(function(){

                jQuery( "#progress" ).load( '../wp-content/plugins/wpguards/assets/js/check_backup_size.php?path=' + file_path + '&size=' + file_size );

            }, 1000);
            // end restoring progress bar
			
		});
		
	});
})(jQuery);