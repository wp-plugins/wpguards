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

<div class="wrap wpguards wpguards-payments">

    <?php echo WPGuards_Admin::getNotices(); ?>
    <?php if (isset($_GET['paid']) && $_GET['paid']) : ?>
    <div class="updated"><p><?php _e('We are processing your request. You will be notified shortly about status of the payment.'); ?></p></div>
    <?php endif; ?>
    <div class="updated"><p><?php _e('Your payment is missing? It might take a while to list it below.','wpguards'); ?></p></div>

    <?php if (empty($payments->data)) : ?>
    <div class="updated"><p><?php _e('No payments found in database.'); ?></p></div>
    <?php endif; ?>

    <?php if (get_transient('wpguards_checkConnection') != false && !empty($payments->data)) : ?>
    <div class="metabox-holder">

        <div id="post-body">

            <div class="postbox form">
                <h3><?php _e('Payments','wpguards'); ?></h3>
                <div class="inside">

                    <table class="payments">
                        <tbody>
                            <?php foreach ($payments->data as $payment) : ?>
                            <tr class="payment">
                                <td class="col-payment <?php echo ($payment->type == 'success') ? 'green' : 'red'; ?>">
                                    <span class="dashicons <?php echo ($payment->type == 'success') ? 'dashicons-yes' : 'dashicons-no-alt'; ?>"></span>
                                    <strong><?php _e('Payment','wpguards'); ?></strong>
                                    <div class="info">
                                        <div>
                                            <?php _e('Paypal ID','wpguards'); ?>:
                                            <i><?php echo $payment->txnID; ?></i>
                                        </div>
                                        <div>
                                            <?php _e('Date','wpguards'); ?>: 
                                            <i><?php echo $payment->date; ?></i>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-status">
                                    <?php _e('Payment status:','wpguards'); ?>
                                    <strong><?php echo $payment->type; ?></strong>
                                    <?php if (!empty($payment->paymentAdditionalData)) : ?>
                                    <div class="info">
                                        <div>
                                            <?php _e('Additional info', 'wpguards'); ?>:
                                            <i><?php echo $payment->paymentAdditionalData; ?></i>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div> <!-- .postbox.form -->
                
        </div> <!-- #post-body -->

    </div>
    <?php endif; ?>

</div><!-- .wrap -->