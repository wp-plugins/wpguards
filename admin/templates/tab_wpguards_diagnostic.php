<?php defined('ABSPATH') OR exit; //prevent from direct access ?>

<?php $diagnostic = new WPGDiagnostic(); ?>

<div class="wpguards-tab-content diagnostic">

	<div class="metabox-holder">

		<div id="post-body">
            <div id="post-body-content">

            	<div class="postbox overview">
		            <h3><span><?php _e('Useful links','wpguards'); ?></span></h3>
		            <div class="inside">

		            	<?php _e('Here you can find some useful links to check your WordPress blog.','wpguards'); ?>


		            	<ul id="useful-links-list">
		            		<?php $diagnostic->get_services(); ?>
		            	</div>
                  <hr class="prop" />

		            </div>
		        </div>
            	
            </div> <!-- #post-body-content -->
        </div>

	</div>

</div>
<script>
jQuery("#useful-links-list li a").eq(0).css("background","rgba(00,80,00,0.6)");
jQuery("#useful-links-list li a").eq(1).css("background","rgba(255,0,0,0.6)");
jQuery("#useful-links-list li a").eq(2).css("background","rgba(0,0,255,0.6)");
jQuery("#useful-links-list li a").eq(3).css("background","rgba(128,0,128,0.6)");
jQuery("#useful-links-list li a").eq(4).css("background","rgba(215,183,0,0.6)");
jQuery("#useful-links-list li a").eq(5).css("background","rgba(75,0,130,0.6)");
</script>