<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<?php
global $WPGuards;
$comments = new WPGTickets();
$comments->prepare_comments($_GET['ticket']);
?>

<div class="wpguards-tab-content ticket">

	<?php if ( wpg_check_apikey() ) : ?>

		<?php if (!$comments->hack) : ?>

			<div class="metabox-holder has-right-sidebar">

				<div id="post-body">
		            <div id="post-body-content">

		            	<div class="postbox comments">
				            <h3><span>Comments</span></h3>
				            <div class="inside">
				            	<?php $comments->display_comments(); ?>
				            </div>
				        </div>
		            	
		            </div> <!-- #post-body-content -->
		        </div>

		        <div class="inner-sidebar">

		        	<div class="postbox form">
			            <h3><span><label for="comment"><?php _e('Submit new comment','wpguards'); ?></label></span></h3>
			            <div class="inside">

			            	<?php if (!$_GET['closed']) : ?>

				            	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" id="wpguards-comment-form">

									<textarea name="comment" id="comment" class="large-text" rows="6"></textarea>
									<input type="hidden" name="ticket" value="<?php echo $_GET['ticket']; ?>" />
									<?php wp_nonce_field('wpg_comment_request','wpg_new_comment'); ?>

									<?php submit_button( __('Submit','wpguards'), 'primary', 'submit-comment', false ); ?>

								</form>

							<?php else : ?>

								<?php _e('This ticket is closed and you can not reply to it.','wpguards'); ?>

							<?php endif; ?>

			            </div>
			        </div>

		        </div> <!-- .innner-sidebar -->

			</div>

			<div class="clear"></div>

		<?php endif; ?>

	<?php else : 

		wpg_no_apikey();

	endif; ?>

</div> <!-- .wpguards-tab-content .support -->
