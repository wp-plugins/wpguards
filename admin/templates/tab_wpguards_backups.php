<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<?php
$backups = new WPGBackups();
$backups->prepare_backups();
?>

<div class="wpguards-tab-content backups">

	<?php if ( wpg_check_apikey() ) : ?>

		<?php do_action('wpg_backup_restore_request', $_POST); ?>

		<div class="metabox-holder has-right-sidebar">

			<div id="post-body">
	            <div id="post-body-content">

	            	<?php $backups->display_backups(); ?>
	            	
	            </div> <!-- #post-body-content -->
	        </div>

	        <div class="inner-sidebar">

            	<?php $backups->display_backups_sidebar(); ?>

	        </div> <!-- .innner-sidebar -->

		</div>

		<?php $backups->prepare_backups_stats(); ?>

	<?php else : 

		wpg_no_apikey();

	endif; ?>

</div> <!-- .wpguards-tab-content .support -->
