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

<div class="wrap wpguards wpguards-backups">

    <?php echo WPGuards_Admin::getNotices(); ?>

    <?php if (get_transient('wpguards_checkConnection') != false) : ?>
    <div class="metabox-holder">

        <div id="post-body">

            <div class="postbox form">
                <h3><?php _e('Informations','wpguards'); ?></h3>
                <div class="inside">
                    <p>
                        <?php _e('On cheap servers there might be a problem with restoring backups due to timeouts. In this case recommended is to download backup to your hard drive and upload it manually to the FTP.','wpguards'); ?>
                    </p>
                    <?php
                        if ($basicData->planID == '1' || $basicData->planID == '2') : 
                            _e('Currently you are using free version of WPGuards. Backups are made on a weekly basis and they are kept locally.','wpguards');
                        elseif ($basicData->planID == '3') :
                            _e('Currently you are using standard version of WPGuards. Backups are made on a weekly basis and they are kept on the external Dropbox drive.','wpguards');
                        elseif ($basicData->planID == '4') :
                            _e('Currently you are using premium version of WPGuards. Backups are made on a daily basis and they are kept on the external Dropbox drive.','wpguards');
                        endif;

                        if ($basicData->planID != '4') :
                            printf( __('%sUpgarde your version%s and do a backup <strong>every day!</strong>','wpguards'), ' <a href=' . API_URL . '>', '</a>' );
                        endif;
                    ?>
                </div>
            </div>

            <div class="updated"><p><?php _e('Your backup is missing? It might take up to 4 hours to fully process the request.','wpguards'); ?></p></div>

            <div class="postbox form">
                <h3><?php _e('Backups','wpguards'); ?></h3>
                <div class="inside">

                    <table class="backups">
                        <tbody>
                            <tr class="schdeuled-backup">
                                <td class="col-backup">
                                    <span class="dashicons dashicons-backup"></span>
                                    <strong><?php _e('Schdeuled backup','wpguards'); ?></strong>
                                    <div class="info">
                                        <div>
                                            <?php _e('Date','wpguards'); ?>: 
                                            <i><?php echo date('Y-m-d H:i:s', $nextBackupTransient); ?></i>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-download">
                                </td>
                                <td class="col-restore">
                                </td>
                            </tr>
                            <?php 
                            if (!empty($backupTransient)) : 
                                foreach ($backupTransient as $backup) :
                            ?>
                            <tr class="backup">
                                <td class="col-backup">
                                    <span class="dashicons dashicons-backup"></span>
                                    <strong><?php _e('Full backup','wpguards'); ?></strong>
                                    <div class="info">
                                        <div>
                                            <?php _e('Size','wpguards'); ?>:
                                            <i><?php echo number_format($backup->fileSize/1024, 1, '.', ' ') . 'MB'; ?></i>
                                        </div>
                                        <div>
                                            <?php _e('Date','wpguards'); ?>: 
                                            <i><?php echo date('Y-m-d H:i:s', $backup->time); ?></i>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-download">
                                    <?php if (get_transient('wpguards_backup_' . $backup->backupID) === false) : ?>
                                    <form method="post">
                                        <input type="hidden" name="action" value="getLink">
                                        <input type="hidden" name="backupID" value="<?php echo $backup->backupID; ?>">
                                        <input type="hidden" name="time" value="<?php echo $backup->time; ?>">
                                        <button type="submit" class="button-secondary button-large"><?php _e('Get Download Link', 'wpguards'); ?></button>
                                    </form>
                                    <?php else: ?>
                                    <a href="<?php echo get_transient('wpguards_backup_' . $backup->backupID); ?>" class="button button-primary button-large">Download</a>
                                    <?php endif;?>
                                </td>
                                <td class="col-restore">
                                <form method="post">
                                    <input type="hidden" name="action" value="restore">
                                    <input type="hidden" name="backupID" value="<?php echo $backup->backupID; ?>">
                                    <input type="hidden" name="time" value="<?php echo $backup->time; ?>">
                                    <button type="submit" class="button-primary button-large" onclick="return confirm('<?php _e('Are you sure?', 'wpguards'); ?>');">
                                        <?php _e('Restore', 'wpguards'); ?>
                                    </button>
                                </form>
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