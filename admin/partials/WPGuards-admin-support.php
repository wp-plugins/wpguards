<?php
/**
 * Settings page view for WPGuards plugin
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin/partials
 */
?>

<div class="wrap wpguards wpguards-support">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <?php if (get_transient('wpguards_checkConnection') != false) : ?>
    <div class="metabox-holder">

        <div id="post-body">
            <div id="post-body-content">

                <div class="columns">
                    <div class="column_2">
                        <div class="postbox form">
                            <h3><?php _e('Submit new ticket','wpguards'); ?></h3>
                            <div id="new-ticket" class="inside">
                                <form method="post">
                                    <?php echo $this->forms->ticket->render(); ?>
                                    <div class="fieldset button-holder">
                                        <button type="submit" class="button-primary"><?php _e('Submit', 'wpguards'); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- .postbox.form -->
                    </div><!-- .column_2 -->
                    <div class="column_2">
                        <div class="postbox form">
                            <h3><?php _e('Receipt','wpguards'); ?></h3>
                            <div id="receipt" class="inside loader small-icon"></div>
                        </div><!-- .postbox.form -->
                    </div><!-- .column_2 -->
                </div><!-- .columns -->

                <div id="tickets_holder">
                    <div id="tickets" class="loader large-icon"></div>
                </div>
                
            </div> <!-- #post-body-content -->
        </div> <!-- #post-body -->

    </div><!-- .metabox-holder -->
    <?php endif; ?>
    
</div><!-- .wrap -->