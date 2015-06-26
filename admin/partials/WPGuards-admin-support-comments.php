<?php
/**
 * Comments view for WPGuards plugin
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin/partials
 */
?>
<?php if (empty($response->data) || !is_array($response->data)) : ?>
    <?php _e('Something went wrong. Please try again in a minute.','wpguards'); ?>
<?php return; ?>
<?php endif; ?>

<?php foreach ($response->data as $comment) : ?>

    <div class="comment <?php echo $comment->extra_class; ?>" data-id="<?php echo $comment->id; ?>">
        <?php echo $comment->html_body; ?>
        <div class="footer">

            <?php if (!empty($comment->attachments)) : ?>

            <div class="attachments">
                <strong>
                    <?php _e('Attachments', 'wpguards'); ?>
                </strong>

                <ul>
                <?php foreach ($comment->attachments as $attachment) : ?>

                <li>
                    <a href="<?php echo $attachment->content_url; ?>">
                        <?php echo $attachment->file_name; ?>
                    </a>
                </li>

                <?php endforeach; ?>
                </ul>
            </div>

            <?php endif;?>  

            <span class="time">
                <?php echo date('Y-m-d H:i', strtotime($comment->created_at)); ?>
            </span>
        </div>
        <?php if ($comment->extra_class == 'customer') : ?>
        <div class="<?php echo $comment->extra_class; ?>-avatar">
            <?php echo get_avatar(get_current_user_id(), '80'); ?>
        </div>
        <?php else: ?>
        <div class="<?php echo $comment->extra_class; ?>-avatar">
        <?php echo get_avatar('support@wpguards.com', '80'); ?>
        </div>
        <?php endif; ?>
    </div>

<?php endforeach; ?>
