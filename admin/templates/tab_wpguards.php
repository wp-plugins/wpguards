<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<div class="wpguards-tab-content home">

<?php
global $WPGuards;
$home = new WPGHome();
?>

	<?php if ( wpg_check_apikey() ) : ?>

		<div class="metabox-holder has-right-sidebar">

			<div id="post-body">
	            <div id="post-body-content">

	            	<div class="postbox overview">
			            <h3><span><?php _e('Overview','wpguards'); ?></span></h3>
			            <div class="inside">

			            	<?php $home->display_statistics(); ?>

			            </div>
			        </div>

			        <?php if ( $home->can_upgrade() ) $home->display_news(); ?>
	            	
	            </div> <!-- #post-body-content -->
	        </div>

	        <div class="inner-sidebar">

	        	<?php $home->features(); ?>

	        </div> <!-- .innner-sidebar -->

		</div>

	<?php else : 

		wpg_no_apikey();

	endif; ?>

</div>