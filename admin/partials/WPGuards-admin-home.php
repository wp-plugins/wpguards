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

<div class="wrap wpguards wpguards-home">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <?php if (get_transient('wpguards_checkConnection') != false) : ?>
    <div class="metabox-holder">

        <div id="post-body">

            <div class="columns">
                <div class="column_3">
                    <div id="profile" class="postbox">
                        <div class="inside">
                            <div id="user_avatar">
                                <?php echo get_avatar(get_current_user_id(), '80'); ?>
                            </div>
                            <h3>
                                <?php _e('Hello,', 'wpguards'); ?>
                                <?php echo $user->user_login; ?>
                            </h3>
                            <br clear="all" />
                            <table>
                                <tbody>
                                    <tr>
                                        <th><?php _e('User since', 'wpguards'); ?></th>
                                        <td><?php echo $basicData->userRegister; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Currency', 'wpguards'); ?></th>
                                        <td><?php echo $basicData->userCurrency; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Location', 'wpguards'); ?></th>
                                        <td><?php echo $basicData->userLocation; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="column_3">
                    <div id="plan" class="postbox">
                        <div class="inside">
                            <h3><?php _e('Your plan', 'wpguards'); ?></h3>
                            <table>
                                <tbody>
                                    <tr>
                                        <th><?php _e('Current plan', 'wpguards'); ?></th>
                                        <td><?php echo $basicData->planName; ?></td>
                                    </tr>
                                    <?php if ($basicData->planID != '2') : ?>
                                    <tr>
                                        <th><?php _e('Plan expires', 'wpguards'); ?></th>
                                        <td><?php echo $basicData->planExpire; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th><?php _e('Backup', 'wpguards'); ?></th>
                                        <td><?php echo _e($basicData->planBackups, 'wpguards'); ?></td>
                                    </tr>
                                    <?php if ($basicData->planID == '2' || $basicData->planID == '4') : ?>
                                    <tr>
                                        <th><?php _e('Malware scanner', 'wpguards'); ?></th>
                                        <td><?php echo _e($basicData->planSecurity, 'wpguards'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th><?php _e('Support', 'wpguards'); ?></th>
                                        <td><?php echo $basicData->supportPrice->{$basicData->userCurrency}; ?> <?php echo $basicData->userCurrency; ?> / <?php _e('hour', 'wpguards'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="align-center">
                                <?php if ($basicData->planID != '4') : ?>
                                <a href="<?php echo API_URL; ?>" class="button button-primary button-large">
                                <?php _e('Upgrade your plan', 'wpguards'); ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column_3">
                    <div id="uptime" class="postbox">
                        <div class="inside">
                            <h3><?php _e('Uptime monitor', 'wpguards'); ?></h3>
                            <div class="chart-wrapper">
                                <canvas id="uptime-chart" width="180" height="180"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="postbox form">
                <div class="inside">

                    <div id="calendar"></div>

                </div>
            </div> <!-- .postbox.form -->
                
        </div> <!-- #post-body -->

    </div>
    <?php endif; ?>

</div><!-- .wrap -->