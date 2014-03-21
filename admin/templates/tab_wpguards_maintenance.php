<?php defined('ABSPATH') OR exit; //prevent from direct access ?>


<div class="wpguards-tab-content maintenance">

	<?php if ( wpg_check_apikey() ) : ?>

		<div class="metabox-holder">

			<div id="post-body">
	            <div id="post-body-content">

	            	<div class="postbox uptime">
			            <h3><span><?php _e('Maintenance','wpguards'); ?></span></h3>
			            <div class="inside">

			            </div>
			        </div>
	            	
	            </div> <!-- #post-body-content -->
	        </div>

		</div>

	<?php else : 

		wpg_no_apikey();

	endif; ?>

</div>