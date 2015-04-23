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
                            <tr class="scan">
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
                                    <div class="scan">
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