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

<div class="wrap wpguards wpguards-uptime">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <?php if (!isset($uptimeMonitor->data->responsetime)) : ?>
    <div class="updated"><p><?php _e('Uptime service is not yet available. Please refresh this page in few minutes.','wpguards'); ?></p></div>
    <?php endif; ?>

    <?php if (get_transient('wpguards_checkConnection') != false && isset($uptimeMonitor->data->log)) : ?>
    <div class="metabox-holder">

        <div class="columns">
            <div class="column_3">

                <div class="postbox form">
                    <h3><span><label for="comment"><?php _e('Uptime','wpguards'); ?></label></span></h3>
                    <div class="inside">
                    <canvas id="uptime-chart" width="180" height="180"></canvas>
                    </div>
                </div>

            </div>

            <div class="column_3">

                <div class="postbox form">
                    <h3><span><label for="comment"><?php _e('Uptime data','wpguards'); ?></label></span></h3>
                    <div class="inside">
                        <table>
                            <tbody>
                                <tr>
                                    <th><?php _e('Site status', 'wpguards'); ?></th>
                                    <td><?php echo $this->getTypeClass($uptimeMonitor->data->status); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Uptime', 'wpguards'); ?></th>
                                    <td><?php echo $uptimeMonitor->data->alltimeuptimeratio; ?>%</td>
                                </tr>
                                <tr>
                                    <th><?php _e('Downtime', 'wpguards'); ?></th>
                                    <td><?php echo 100-$uptimeMonitor->data->alltimeuptimeratio; ?>%</td>
                                </tr>
                                <tr>
                                    <th><?php _e('Avg response', 'wpguards'); ?></th>
                                    <?php
                                        $responses = 0;
                                        foreach($uptimeMonitor->data->responsetime as $response) {
                                            $responses += $response->value;
                                        }
                                    ?>
                                    <td><?php echo intval($responses/count($uptimeMonitor->data->responsetime)); ?>ms</td>
                                </tr>
                                <tr>
                                    <th><?php _e('Latest downtime', 'wpguards'); ?></th>
                                    <td>
                                    <?php $i = 1; ?>
                                    <?php foreach ($uptimeMonitor->data->log as $log): ?>
                                        <?php if ($log->type == '1') continue; ?>
                                        <div>
                                            <i><?php echo date('Y-m-d H:i', strtotime($log->datetime)); ?></i>
                                        </div>
                                        <?php break; ?>
                                    <?php endforeach; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="column_3">

                <div class="postbox form">
                    <h3><span><label for="comment"><?php _e('24h uptime','wpguards'); ?></label></span></h3>
                    <div class="inside">
                    <canvas id="uptime-history-chart" width="180" height="180"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <div class="postbox form">
            <h3><span><label for="comment"><?php _e('Response time','wpguards'); ?></label></span></h3>
            <div class="inside">
            <canvas id="uptime-response-chart" width="180" height="400"></canvas>
            </div>
        </div>

    </div>

    <?php endif;?>

</div><!-- .wrap -->