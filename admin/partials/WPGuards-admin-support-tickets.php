<?php
/**
 * Tickets view for WPGuards plugin
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin/partials
 */
?>

<?php if (empty($response->data)) : ?>
    <div class="updated">
        <p><?php _e('No tickets', 'wpguards'); ?></p>
    </div>
<?php return; ?>
<?php endif; ?>

<div id="tickets">
    <div class="postbox form">
        <table>
            <thead>
                <tr>
                    <th><?php _e('Subject','wpguards'); ?></th>
                    <th><?php _e('Type','wpguards'); ?></th>
                    <th><?php _e('Time spent','wpguards'); ?></th>
                    <th><?php _e('Cost','wpguards'); ?></th>
                    <th><?php _e('Updated','wpguards'); ?></th>
                </tr>
            </thead>
            <?php foreach ($response->data as $ticket) : ?>
            <tbody>
                <tr class="ticket" data-id="<?php echo $ticket->id;?>">
                    <td class="col-subject">
                        <?php if (in_array($ticket->status, array('new', 'open', 'pending', 'hold'))) : ?>
                        <span class="dashicons dashicons-edit"></span>
                        <?php else : ?>
                        <span class="dashicons dashicons-visibility"></span>
                        <?php endif; ?>
                        <?php _e($ticket->subject, 'wpguards'); ?>
                    </td>
                    <td class="col-type">
                        <?php _e($ticket->type, 'wpguards'); ?>
                    </td>
                    <td class="col-time">
                        <?php echo $ticket->payment->time_spent; ?>
                    </td>
                    <td class="col-cost">
                        <?php 
                        if (isset($ticket->payment->cost)) : 
                            echo $ticket->payment->cost . ' ' . $ticket->payment->currency;
                        endif;
                        ?>
                    </td>
                    <td class="col-updated">
                        <?php echo date('Y-m-d H:i:s', strtotime($ticket->updated_at)); ?>
                    </td>
                </tr>
                <tr class="expand">
                    <td colspan="5">
                        <div class="comments-expand">
                            <div class="comments loader small-icon">
                                
                            </div>

                            <?php if (in_array($ticket->status, array('new', 'open', 'pending', 'hold'))) : ?>

                                <div class="response">
                                    <form method="post">
                                        <?php 
                                            $respond = new WPGeeks_Form;
                                            $respond->add(new WPGeeks_Form_Element_Hidden('ticketID', array('value' => $ticket->id)));

                                            $input = new WPGeeks_Form_Element_Textarea('description', array());
                                            $input->setValue(__('Your response', 'wpguards'));
                                            $respond->add($input);

                                            echo $respond->render();
                                        ?>
                                        <div class="fieldset button-holder align-right">
                                            <button type="submit" class="button-primary"><?php _e('Submit', 'wpguards'); ?></button>
                                        </div>
                                    </form>
                                </div>

                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            <?php endforeach;?>
        </table>
    </div>
</div>