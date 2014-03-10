<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<div class="wpguards-tab-content analytics">

	<?php if ( wpg_check_apikey() ) : ?>

		<div class="metabox-holder has-right-sidebar">

			<div id="post-body">
	            <div id="post-body-content">

	            	<div class="postbox statistics">
			            <h3><span><?php _e('Statistics','wpguards'); ?></span></h3>
			            <div class="inside">

			            	<?php ga_dash_content(); ?>

			            </div>
			        </div>
	            	
	            </div> <!-- #post-body-content -->
	        </div>

	        <div class="inner-sidebar">

	        	<div class="postbox settings">
		            <h3><span><?php _e('Settings','wpguards'); ?></span></h3>
		            <div class="inside">

		            	<?php ga_dash_admin(); ?>

		            </div>
		        </div>

	        </div> <!-- .innner-sidebar -->

		</div>

	<?php else : 

		wpg_no_apikey();

	endif; ?>

</div>