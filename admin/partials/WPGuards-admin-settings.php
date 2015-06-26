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

<div class="wrap wpguards">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <div class="metabox-holder">

        <div class="columns">
            <div class="column_2">

                <?php if (!get_option('wpguards_connected', false)): ?>
                    <div class="postbox form">
                        <h3><?php _e('API key','wpguards'); ?></h3>
                        <div class="inside">
                            <form method="post">
                                <?php echo $this->forms->api->render(); ?>
                                <div class="fieldset button-holder">
                                    <button type="submit" class="button-primary"><?php _e('Install', 'wpguards'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- .postbox.form -->

                    <div class="postbox form">
                        <h3>
                            <?php _e('Register','wpguards'); ?>
                        </h3>

                        <div class="inside">
                            <span class="description">
                                <?php _e('Register your account at WPGuards.com to receive first API key.', 'wpguards'); ?>
                            </span>
                            <form method="post">
                                <?php echo $this->forms->register->render(); ?>
                                <div class="fieldset button-holder">
                                    <button type="submit" class="button-primary"><?php _e('Register', 'wpguards'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- .postbox.form -->
                <?php else: ?>

                    <div class="postbox form">

                        <h3><?php _e('Settings','wpguards'); ?></h3>

                        <div class="inside">

                            <form method="post">
                                <input type="hidden" name="wpgurds_save_settings" value="true">
                                <?php echo $this->forms->settings->render(); ?>
                                <div class="fieldset button-holder">
                                    <button type="submit" class="button-primary"><?php _e('Save', 'wpguards'); ?></button>
                                </div>
                            </form>

                        </div>

                    </div>

                <?php endif;?>

            </div>

            <div class="column_2">

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

                        <div class="align-center">
                            <a class="button button-primary button-large instructions" target="_blank" href="http://wpguards.com/downloads/WPGuards-Installation-and-General-Overview.pdf">
                                <?php _e('Download installation instructions [PDF]','wpguards'); ?>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div><!-- .wrap -->