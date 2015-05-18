<?php
/**
 * Scans page view for WPGuards plugin
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin/partials
 */
?>

<div class="wrap wpguards wpguards-scans">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <?php if (get_transient('wpguards_checkConnection') != false) : ?>
    <div class="metabox-holder">

        <div id="post-body">

            <div class="postbox form">
                <h3><?php _e('Information','wpguards'); ?></h3>
                <div class="inside">
                    <p>
                        <?php _e('This page contains results of malware, virus, trojans and worms scans. Any suspicious content will be marked with a red dot.','wpguards'); ?>
                    </p>
                </div>
            </div>

            <div class="updated"><p><?php _e('Your scan is missing? It might take up to 4 hours to fully process the request.','wpguards'); ?></p></div>

            <?php if ($positive) : ?>
            <div class="error"><p><?php printf(__('Scanner has performed %1$s tests which %2$s of them found suspicious content on your website. Please investigate the problem or ask for a <a href="%3$s">professional help</a>','wpguards'), $scan->tests, $scan->positives, admin_url('admin.php?page=wpguards_support')); ?></p></div>
            <?php else: ?>
            <div class="updated"><p><?php printf(__('Scanner has performed %1$s tests which none of them found suspicious content on your website. Click on test details to get more info.','wpguards'), $scan->tests); ?></p></div>
            <?php endif; ?>

            <div class="postbox form">
                <h3><?php _e('Scans','wpguards'); ?></h3>
                <div class="inside">

                    <table class="scans">
                        <tbody>
                            <tr class="schdeuled-scan">
                                <td class="col-scan">
                                    <span class="dashicons dashicons-lock"></span>
                                    <strong><?php _e('Schdeuled scan','wpguards'); ?></strong>
                                    <div class="info">
                                        <div>
                                            <?php _e('Date','wpguards'); ?>: 
                                            <i><?php echo date('Y-m-d H:i:s', $nextScanTransient); ?></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                            if (!empty($scanTransient)) : 
                                foreach ($scanTransient as $scan) :
                            ?>
                            <tr class="scan <?php echo ($scan->positives == '0') ? 'green' : 'red'; ?>">
                                <td class="col-scan">
                                    <span class="dashicons dashicons-lock"></span>
                                    <strong><?php _e('Scan','wpguards'); ?></strong>
                                    <div class="info">
                                        <div>
                                            <?php _e('Date','wpguards'); ?>: 
                                            <i><?php echo $scan->date; ?></i>
                                        </div>
                                    </div>
                                    <?php $scan->result = unserialize($scan->result); ?>
                                    <?php if (!empty($scan->result)) : ?>
                                    <div class="scan" style="display: none;">
                                    <table>
                                        <tbody>
                                            <?php foreach ($scan->result as $name => $scan) : ?>
                                            <tr class="<?php echo ($scan->detected) ? 'red' : 'green'; ?>">
                                                <td></td>
                                                <td class="col-page">
                                                    <?php echo $name; ?>
                                                </td>
                                                <td class="col-status">
                                                    <?php if ($scan->detected) : ?>
                                                        <?php _e('Suspicious','wpguards'); ?>
                                                    <?php else: ?>
                                                        <?php _e('Clean','wpguards'); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="col-result">
                                                    <i><?php echo $scan->result; ?></i>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </tbody>
                    </table>

                </div>
            </div> <!-- .postbox.form -->
                
        </div> <!-- #post-body -->

    </div>
    <?php endif; ?>

</div><!-- .wrap -->