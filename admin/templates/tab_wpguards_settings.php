<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<div class="wpguards-tab-content settings">

	<div class="metabox-holder has-right-sidebar">

			<div id="post-body">
	            <div id="post-body-content">

	            	<div class="postbox form">
			            <h3><span><label for="comment"><?php _e('Settings','wpguards'); ?></label></span></h3>
			            <div class="inside">

			            	<form method="post" action="options.php" enctype="multipart/form-data">
		
								<?php 
								    settings_fields('wpguards_settings');
								    do_settings_sections('wpguards_settings');
								    submit_button();
								?>

							</form>

			            </div>
			        </div>

			        <div class="postbox form">
			            <h3><span><label for="comment"><?php _e('Notifications','wpguards'); ?></label></span></h3>
			            <div class="inside">

			            	<form method="post" action="options.php" enctype="multipart/form-data">
		
								<?php 
								    settings_fields('wpguards_notifications');
								    do_settings_sections('wpguards_notifications');
								    submit_button();
								?>

							</form>

			            </div>
			        </div>
	            	
	            </div> <!-- #post-body-content -->

	        </div>

	        <div class="inner-sidebar">

                <div class="postbox form">
                    <h3><span><label for="comment"><?php _e('Informations','wpguards'); ?></label></span></h3>
                    <div class="inside">

                    	<?php _e('If your server servs gziped content, loader may be not visible. Please be patient when the plugin pages will be loaded.','wpguards'); ?>

                    	<hr />

                        <?php _e('Please restrict the following IP addresses in your Analytics system. Those are from Uptime Robot and they can increase traffic on your website.','wpguards'); ?>
                        <br />
                        <br />
                        <code>*.uptimerobot.com, 74.86.158.106, 74.86.158.107, 74.86.179.130, 74.86.179.131, 46.137.190.132, 122.248.234.23, 74.86.158.108.</code>

                        <hr />

                        <a class="button-primary instructions" target="_blank" href="http://wpguards.com/downloads/WPGuards-Installation-and-General-Overview.pdf">
	                        <?php _e('Download installation instructions [PDF]','wpguards'); ?>
                        </a>

                    </div>
                </div>

            </div> <!-- .innner-sidebar -->

		</div>

	
	
</div>