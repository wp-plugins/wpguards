<?php
/**
 * Receipt view for WPGuards plugin
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin/partials
 */
?>

<?php if (empty($response->data)) : ?>
    <?php _e('All your tickets are paid. Thank you!','wpguards'); ?>
<?php return; ?>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th class="col-ticket" colspan="4">Ticket #</th>
            <th class="col-cost">Cost</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($response->data as $ticketID => $ticketPayment) : ?>
        <tr>
            <td class="col-ticket"><?php echo $ticketID; ?></td>
            <td class="col-time"><?php echo $ticketPayment->time; ?></td>
            <td class="col-at">@</td>
            <td class="col-price"><?php echo $basicData->supportPrice->{$ticketPayment->currency} . $ticketPayment->currency; ?></td>
            <td class="col-total"> = <?php echo $ticketPayment->cost . ' ' . $ticketPayment->currency; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="col-label" colspan="4">Total</td>
            <td class="col-total"><?php echo $total . ' ' . $currency; ?></td>
        </tr>
    </tfoot>
</table>

<?php if (defined('WPGUARDS_DEBUG') && WPGUARDS_DEBUG === true) : ?>
<form id="paypal-button" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<?php else: ?>
<form id="paypal-button" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<?php endif;?>
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="paypal@wpguards.com">
    <input type="hidden" name="currency_code" value="<?php echo $currency; ?>">
    <input type="hidden" name="notify_url" value="<?php echo API_URL; ?>/api/v1/payment/paypalTicket">
    <input type="hidden" name="return" value="<?php echo admin_url('admin.php?page=wpguards_payments&paid=1'); ?>">
    <input type="hidden" name="cancel_return" value="<?php echo admin_url('admin.php?page=wpguards_support'); ?>">
    <?php $i = 1; ?>
    <?php foreach ($response->data as $ticketID => $ticketPayment) : ?>
        <input type="hidden" name="item_name_<?php echo $i; ?>" value="Ticket <?php echo $ticketID; ?>">
        <input type="hidden" name="item_number_<?php echo $i; ?>" value="<?php echo $ticketID; ?>">
        <input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo $ticketPayment->cost; ?>">
        <?php $i++; ?>
    <?php endforeach; ?>
    <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>