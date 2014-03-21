<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<?php
ob_start();
$tickets = new WPGTickets();
?>

<div class="wpguards-tab-content support">

	<?php if ( wpg_check_apikey() ) : ?>

		<div class="metabox-holder has-right-sidebar">

			<div id="post-body">
	            <div id="post-body-content">

	            	<div class="postbox form">
			            <h3><span><label for="comment"><?php _e('Submit new ticket','wpguards'); ?></label></span></h3>
			            <div class="inside">

			            	<?php $tickets->display_ticket_form(); ?>

			            </div>
			        </div>
	            	
	            </div> <!-- #post-body-content -->
	        </div>

	        <?php ob_flush(); ?>

	        <?php $tickets->prepare_tickets(); ?>
	        <?php $tickets->maybe_pay(); ?>

		</div>

		<div class="your-tickets">
			<h3><?php _e('Your tickets','wpguards'); ?></h3>

			<?php $tickets->display_tickets(); ?>
		</div>

	<?php else : 

		wpg_no_apikey();

	endif; ?>

</div> <!-- .wpguards-tab-content .support -->
<?php $tickets->prepare_tickets_stats(); ?>