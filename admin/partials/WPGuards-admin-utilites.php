<?php
/**
 * Utilities page view for WPGuards plugin
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin/partials
 */
?>

<div class="wrap wpguards wpguards-utilities">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <div class="metabox-holder">

        <div class="postbox form">
            <h3><?php _e('Information','wpguards'); ?></h3>
            <div class="inside">
                <p>
                    <?php _e('WPGuards plugin offers a bunch of online tools for webmasters and webdesigners. These utilities might help to improve your website experience and performance in a minute','wpguards'); ?>
                </p>
            </div>
        </div>

        <div id="post-body">

            <div id="post-body-content">
                <div class="postbox form utility">
                    <span class="dashicons dashicons-editor-spellcheck"></span>
                    <h3>
                        <a href="http://respelt.com/?url=<?php echo site_url(); ?>" target="_blank">
                            <?php _e('Respelt spell checker', 'wpguards'); ?>
                        </a>
                    </h3>
                    <div class="inside">
                        <?php _e('Check the spelling on your website with this simple tool.', 'wpguards'); ?>
                    </div>
                </div>

                <div class="postbox form utility">
                    <span class="dashicons dashicons-dashboard"></span>
                    <h3>
                        <a href="http://tools.pingdom.com/fpt/" target="_blank">
                            <?php _e('Page loading time', 'wpguards'); ?>
                        </a>
                    </h3>
                    <div class="inside">
                        <?php _e('Your website is slow? Check why! With this tool you\'l get informations about the server requests and useful tips.', 'wpguards'); ?>
                    </div>
                </div>

                <div class="postbox form utility">
                <span class="dashicons dashicons-media-code"></span>
                    <h3>
                        <a href="http://validator.w3.org/check?uri=<?php echo site_url(); ?>" target="_blank">
                            <?php _e('Validate HTML', 'wpguards'); ?>
                        </a>
                    </h3>
                    <div class="inside">
                        <?php _e('Google like clean websites so check your HTML sytax with w3c\'s tool.', 'wpguards'); ?>
                    </div>
                </div>

                <div class="postbox form utility">
                    <span class="dashicons dashicons-admin-links"></span>
                    <h3>
                        <a href="http://validator.w3.org/checklink?uri=<?php echo site_url(); ?>" target="_blank">
                            <?php _e('Broken link checker', 'wpguards'); ?>
                        </a>
                    </h3>
                    <div class="inside">
                        <?php _e('Still getting 404 pages? Check broken links on your website.', 'wpguards'); ?>
                    </div> 
                </div>

                <div class="postbox form utility">
                    <span class="dashicons dashicons-clock"></span>
                    <h3>
                        <a href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo site_url(); ?>" target="_blank">
                            <?php _e('Google PageSpeed Insights', 'wpguards'); ?>
                        </a>
                    </h3>
                    <div class="inside">
                        <?php _e('Site performace testing tool from Google. Get some tips and make your website faster!', 'wpguards'); ?>
                    </div>
                </div> 

                <?php if (isset($basicData->planID) && $basicData->planID == '2') : ?>
                <div class="postbox form utility">
                    <span class="dashicons dashicons-clock"></span>
                    <h3>
                        <a href="https://www.virustotal.com/en" target="_blank">
                            <?php _e('Malware scanner', 'wpguards'); ?>
                        </a>
                    </h3>
                    <div class="inside">
                        <?php _e('Free service that facilitates the quick detection of viruses, worms, trojans, and all kinds of malware.', 'wpguards'); ?>
                    </div>
                </div> 
                <?php endif; ?>
            </div> <!-- #post-body-content -->
        </div> <!-- #post-body -->

    </div><!-- .metabox-holder -->
    
</div><!-- .wrap -->