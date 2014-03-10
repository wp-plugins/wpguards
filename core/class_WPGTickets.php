<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Tickets Table class
*/
class WPGTickets {

	public $raw_tickets;
    public $tickets;
    public $payable_tickets;

    public $prices;

    public $authors;

    public $raw_comments;
	public $comments;


    public $hack;
	public $error;

    /**
     * Class contructor
     */
    function __construct() {

    	$this->hack = 0;

    }


    /**
     * Downloads, prepares and sorts tickets in tickets array
     * 
     * @return void
     */
    public function prepare_tickets() {
        global $WPGuards;

        $this->raw_tickets = $WPGuards->WPGConnection->getTickets();
        $this->prices = $WPGuards->WPGConnection->getPrices();

        $tickets_plan = $WPGuards->WPGConnection->getSiteTicketsPlan();

        if (isset($this->raw_tickets->error))
            return $this->error = array('status' => 1, 'description' => $this->raw_tickets->error);

        $this->tickets = array();
        foreach ($this->raw_tickets->tickets as $int => $value) {

            $updated_time = strtotime($value->updated_at);
            $custom = $this->get_custom_fields($value->fields);
            $id = $value->id;
            
            $this->tickets[$updated_time]['id'] = $value->id;
            $this->tickets[$updated_time]['created'] = strtotime($value->created_at);
            $this->tickets[$updated_time]['updated'] = $updated_time;
            $this->tickets[$updated_time]['type'] = $value->type;
            $this->tickets[$updated_time]['time'] = $custom['time'];
            $this->tickets[$updated_time]['cost'] = $this->calculate_cost($custom['time'], $tickets_plan->$id);
            $this->tickets[$updated_time]['paid'] = $custom['paid'];
            $this->tickets[$updated_time]['status'] = $value->status;
            $this->tickets[$updated_time]['subject'] = $value->subject;

        }

        krsort($this->tickets);

        $this->check_payable();

    }

    /**
     * Downloads, prepares and sorts comments in comments array
     * 
     * @return void
     */
    public function prepare_comments($id) {
        global $WPGuards;

        $this->raw_comments = $WPGuards->WPGConnection->getTicketComments($id);

        if (isset($this->raw_comments->error))
        	return $this->hack = 1;

        // get author ids
        foreach ($this->raw_comments->comments as $comment) {
            $authors[$comment->author_id] = $comment->author_id;
        }

        $this->authors = $this->get_author_info($authors);

        $this->comments = array();
        foreach ($this->raw_comments->comments as $comment) {

            $created_time = strtotime($comment->created_at);
            
            $this->comments[$created_time]['id'] = $comment->id;
            $this->comments[$created_time]['author'] = $comment->author_id;
            $this->comments[$created_time]['attachments'] = $comment->attachments;
            $this->comments[$created_time]['body'] = $comment->html_body;

        }

        krsort($this->comments);

    }

    /**
     * Displays tickets container
     * 
     * @return void
     */
    public function display_ticket_form() {
        ?>
        
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" id="wpguards-form">
            <table class="form-table">
                <tbody>

                    <tr valign="top">
                        <th scope="row"><?php _e('Type','wpguards'); ?>&nbsp;<span class="required">*</span></th>
                        <td>
                            <span class="support-type-select">
                                <input name="type" type="radio" id="type-problem" value="problem" checked />
                                <label for="type-problem"><i class="icon-bug"></i> <?php _e('Problem','wpguards'); ?></label>
                            </span>

                            <span class="support-type-select">
                                <input name="type" type="radio" id="type-incident" value="incident" />
                                <label for="type-incident"><i class="icon-exclamation"></i> <?php _e('Incident','wpguards'); ?></label>
                            </span>

                            <span class="support-type-select">
                                <input name="type" type="radio" id="type-question" value="question" />
                                <label for="type-question"><i class="icon-question"></i> <?php _e('Question','wpguards'); ?></label>
                            </span>

                            <span class="support-type-select">
                                <input name="type" type="radio" id="type-task" value="task" />
                                <label for="type-task"><i class="icon-tasks"></i> <?php _e('Quote','wpguards'); ?></label>
                            </span>
                            
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="subject"><?php _e('Subject','wpguards'); ?></label>&nbsp;<span class="required">*</span></th>
                        <td><input name="subject" type="text" id="subject" class="regular-text" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="description"><?php _e('Description','wpguards'); ?></label>&nbsp;<span class="required">*</span>
                            <p class="description"><?php _e('Please, be specific','wpguards'); ?></p>
                        </th>
                        <td>
                            <textarea name="description" id="description" class="large-text" rows="6"></textarea>
                            <span class="right">
                                <span class="required">*</span> - <?php _e('required','wpguards'); ?>
                            </span>
                        </td>
                    </tr>

                    <?php wp_nonce_field('wpg_ticket_request','wpg_new_ticket'); ?>

                    <tr valign="top" align="right">
                        <th scope="row"></th>
                        <td><?php submit_button( __('Submit','wpguards'), 'primary', 'submit-ticket', false ); ?></td>
                    </tr>           

                </tbody>
            </table>
        </form>

        <?php
    }

    /**
     * Displays table legend
     * @return void
     */
    public function display_legend() {

        echo '<div class="legend">';
            echo '<strong>'.__('Legend','wpguards').'</strong>: ';

            $items = array('new', 'open', 'pending', 'unpaid', 'closed');

            // unpaid fix
            __('unpaid', 'wpguards');

            foreach ($items as $item) {
                echo wpg_render_icon($item, '').' '.__($item, 'wpguards').', ';
            }
        echo '</div>';

    }


    /**
     * Displays tickets container
     * 
     * @return void
     */
    public function display_tickets() {

        if ($this->error['status']) {
            if ($this->error['description'] == 'RecordNotFound')
                _e('You havn\'t any tickets yet.','wpguards');
            else
                _e('Tickets error','wpguards');

            return false;
        }

        if (empty($this->tickets)) {
            _e('You havn\'t any tickets yet.','wpguards');
            return false;
        }

        echo '<table class="wp-list-table widefat fixed tickets" cellspacing="0">';

            $this->display_tickets_header();

            $this->display_tickets_content();

        echo '</table>';

        $this->display_legend();

    }

    /**
     * Displays comments container
     * 
     * @return void
     */
    public function display_comments() {

    	if (empty($this->comments))
    		return;

        echo '<table class="comments">';

            foreach ($this->comments as $time => $comment) {
                echo '<tr class="comment">';
                    $this->display_comment($time, $comment);
                echo '</tr>';
            }

        echo '</table>';

    }

    /**
     * Displays comment
     * 
     * @return void
     */
    public function display_comment($time, $com) {

        $author = $this->authors[$com['author']];

        $time = wpg_format_time($time);

        //check if admin or agent and grant badge before name
        ($author['role'] == 'agent' || $author['role'] == 'admin') ? $badge = '<i class="icon-star" title="'.__('support','wpguards').'"></i> ' : $badge = ''; 

        echo '<td class="avatar">';
            echo '<img src="'.$author['avatar'].'" />';
        echo '</td>';

        echo '<td class="content">';
            echo '<p class="description">'.$badge.$author['name'].', '.$time.'</p>';
            echo $com['body'];
            $this->display_attachments($com['attachments']);
        echo '</td>';

    }

    /**
     * Displays tickets table header
     * 
     * @return void
     */
    public function display_tickets_header() {
    	?>
    	<thead>
			<tr>
				<th scope="col" id="status" class="manage-column column-status sortable desc" style=""><a><?php _e('Status','wpguards'); ?></a></th>
				<th scope="col" id="subject" class="manage-column column-subject sortable desc" style=""><a><?php _e('Subject','wpguards'); ?></a></th>
                <th scope="col" id="type" class="manage-column column-type sortable desc" style=""><a><?php _e('Type','wpguards'); ?></a></th>
                <th scope="col" id="time" class="manage-column column-time sortable desc" style=""><a><?php _e('Time spent (h:m)','wpguards'); ?></a></th>
				<th scope="col" id="cost" class="manage-column column-cost sortable desc" style=""><a><?php _e('Cost','wpguards'); ?></a></th>
				<th scope="col" id="created" class="manage-column column-created sortable desc" style=""><a><?php _e('Created','wpguards'); ?></a></th>
				<th scope="col" id="updated" class="manage-column column-updated sorted desc" style=""><a><span><?php _e('Updated','wpguards'); ?></span><span class="sorting-indicator"></span></a></th>
			</tr>
		</thead>
		<?
    }

    /**
     * Displays tickets content
     * 
     * @return void
     */
    public function display_tickets_content() {
        ?>
        <tbody id="the-list" data-wp-lists="list:ticket">
            <?php
            foreach ($this->tickets as $ticket) {
            	$created = wpg_format_time($ticket['created']);
            	$updated = wpg_format_time($ticket['updated']);

            	if ($ticket['status'] == 'solved' || $ticket['status'] == 'closed') {
            		($ticket['cost'] == 0 || $ticket['paid']) ? $status = $ticket['status'] : $status = 'unpaid';
            	} else {
            		 $status = $ticket['status'];
            	}

                echo '<tr class="alternate">';

                    echo '<td class="status column-status" align="center">'.wpg_render_icon( $status ).'</td>'; //<div class="row-actions"><span class="view"><a href="#">'.__('View','wpguards').'</a></div></td>';
                    echo '<td class="subject column-subject">'.wpg_create_ticket_link($ticket).'</td>';
                    echo '<td class="type column-type">'.wpg_render_icon($ticket['type']).'</td>';
                    echo '<td class="type column-time">'.$ticket['time'].'</td>';
                    echo '<td class="type column-cost">'.wpg_format_price($ticket['cost']).'</td>';
                    echo '<td class="created column-created">'.$created.'</td>';
                    echo '<td class="updated column-updated">'.$updated.'</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
        <?
    }

    /**
     * Gets info about the comment author
     * 
     * @return array author
     */
    public function get_author_info($authors) {
        global $WPGuards;

        $v = array();
        foreach ($authors as $author) {

            $user = $WPGuards->WPGConnection->getCommentUser( $author )->user;
            $v[$author]['name'] = $user->name;
            $v[$author]['avatar'] = $this->get_author_avatar( $user->photo, $user->email );
            $v[$author]['role'] = $user->role;

        }

        return $v;
    }

    /**
     * Checks if user has set zendesk image and if not downloads gravatar
     * 
     * @return string image url
     */
    public function get_author_avatar($photo, $email) {

        if ( !empty($photo->content_url)) return $photo->content_url;
        else return 'http://www.gravatar.com/avatar/'.md5($email).'?s=80&d=mm';

    }

    /**
     * Displays attachements
     * 
     * @return void
     */
    public function display_attachments($attachments) {

        if ( empty($attachments) ) return 0;

        echo '<p class="attachments">';

	        foreach ($attachments as $att) {
	        	echo '<span class="attachment">';

		        	( empty($att->thumbnails) ) ? $thumb = '<i class="icon-file thumbnail"></i>' : $thumb = '<img src="'.$att->thumbnails[0]->content_url.'" class="thumbnail" />';

		        	echo '<a href="'.$att->content_url.'" target="_blank">';
		        		echo $thumb.'<br />';
		        		echo $att->file_name;
		        	echo '</a>';

	        	echo '</span>';
	        }

        echo '</p>';

    }

    /**
     * Gets ticket custom fields
     * 
     * @return array custom fields
     */
    public function get_custom_fields($fields) {

        foreach ($fields as $field) {
            
            switch ($field->id) {
                case '23540418': // time spent
                    
                    if ($field->value) $custom['time'] = $field->value;
                    else $custom['time'] = '0:00';
                    
                    break;

                case '23567826': // website
                    
                    $custom['website'] = $field->value;
                    
                    break;

                case '23540428': // paid
                    
                    $custom['paid'] = $field->value;
                    
                    break;
            }

        }

        return $custom;
    }

    /**
     * Check if user has to pay
     * 
     * @return bool
     */
    public function has2pay() {

        if (empty($this->payable_tickets)) {

        	WPGAdmin::unsetGlobalNotice(66);

            return 0;
        }

        $notice = '<a href="'.admin_url('admin.php?page=wpguards_support').'">'.__('Please, pay for the WPGuards support','wpguards').'</a>';
        WPGAdmin::setGlobalNotice(66, 'error', $notice );

        return 1;
    }

    /**
     * Separates payable tickets
     * 
     * @return array tickets
     */
    public function check_payable() {

        $this->payable_tickets = array();
        foreach ($this->tickets as $ticket) {
            
            if ($ticket['status'] == 'solved' || $ticket['status'] == 'closed') {

                if ($ticket['cost'] != 0 && $ticket['paid'] != 1) {
                    $this->payable_tickets[$ticket['id']] = array(
                        'cost' => $ticket['cost'],
                        'title' => $ticket['subject'],
                    );
                }

            }

        }

    }

    /**
     * Calculates cost of support
     * 
     * @return string cost
     */
    public function calculate_cost($time, $plan, $currency = false) {

        /* Don't even try, we will verify it anyway ;) */

        $cost = $this->time2float($time) * $this->prices->$plan;

        $cost = number_format( round($cost, 2), 2 );

        if ($currency)
            return $cost.' &#163;';

        return $cost;

    }

    /**
     * Changes time to float
     * 
     * @return float time
     */
    public function time2float($time) {

        $ar = explode(':', $time);
        $h = $ar[0];
        $m = $ar[1] * 10/6;

        return floatval( $h.'.'.$m );
    }

    /**
     * Changes float to time h:m
     * 
     * @return string time
     */
    public function float2time($float) {

        $h = floor( $float );
        $m_f = $float - $h;

        $m = round( 60*$m_f, 0 );

        return $h.':'.$m;
    }

    /**
     * Check if user have to pay and displays form
     * 
     * @return void
     */
    public function maybe_pay() {

        if ( $this->has2pay() ) : ?>

            <div class="inner-sidebar">

                <div class="postbox form">
                    <h3><span><label for="comment"><?php _e('Please, pay your bill','wpguards'); ?></label></span></h3>
                    <div class="inside">

                        <?php _e('You have some unpaid tickets','wpguards'); ?>

                        <?php $this->get_unpaid_tickets(); ?>

                        <?php $this->get_paypal_button(); ?>

                    </div>
                </div>

            </div> <!-- .innner-sidebar -->

        <?php endif;

    }

    /**
     * Displays PayPal button
     * 
     * @return void
     */
    public function get_paypal_button() {
    ?>
        <form id="paypal-button" action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_cart">
            <input type="hidden" name="upload" value="1">
            <input type="hidden" name="business" value="paypal@wpguards.com">
            <input type="hidden" name="currency_code" value="<?php echo get_option('wpguards_currency', 'USD'); ?>">
            <input type="hidden" name="notify_url" value="http://api.wpguards.com/api/v1/paypal">
            <input type="hidden" name="return" value="<?php echo admin_url('admin.php?page=wpguards_support&paid=1'); ?>">
            <input type="hidden" name="cancel_return" value="<?php echo admin_url('admin.php?page=wpguards_support'); ?>">
        <?php
            $i = 1;
            foreach ($this->payable_tickets as $id => $ticket) {

                echo '<input type="hidden" name="item_name_'.$i.'" value="'.$ticket['title'].'">';
                echo '<input type="hidden" name="item_number_'.$i.'" value="'.$id.'">';
                echo '<input type="hidden" name="amount_'.$i.'" value="'.$ticket['cost'].'">';

                $i++;
            }

        ?>
            <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
        </form>
    <?php
    }

    /**
     * Displays table with unpaid tickets
     * 
     * @return void
     */
    public function get_unpaid_tickets() {
    ?>
        <table id="unpaid-tickets">

            <tr class="header">
                <td><?php _e('Title','wpguards'); ?></td>
                <td><?php _e('Cost','wpguards'); ?></td>
            </tr>

            <?php
            foreach ($this->payable_tickets as $ticket) {
                echo '<tr>';
                    echo '<td class="title">'.wp_trim_words( $ticket['title'], 10 ).'</td>';
                    echo '<td class="cost">'.wpg_format_price($ticket['cost']).'</td>';
                echo '</tr>';
            }
            ?>

            <tr class="total">
                <td class="desc"><strong><?php _e('TOTAL','wpguards'); ?></strong></td>
                <td class="amount"><?php echo wpg_format_price( $this->get_total_cost() ); ?></td>
            </tr>

        </table>
    <?php
    }

    /**
     * Counts total cost of unpaid tickets
     * 
     * @return float cost
     */
    public function get_total_cost() {

        $total = 0;
        foreach ($this->payable_tickets as $ticket) {
            $total += $ticket['cost'];
        }

        return number_format($total, 2);
    }

    /**
     * Prepares stats
     * 
     * @return void
     */
    public function prepare_tickets_stats() {

        if ( empty($this->tickets) )
            return false;

        $solve_time = $time_spent = $types = array();
        foreach ($this->tickets as $ticket) {

            // solve time
            if ( $ticket['status'] == 'solved' || $ticket['status'] == 'closed' )
                $solve_time[] = $ticket['updated'] - $ticket['created'];

            // time spent
            if ( $this->time2float( $ticket['time'] ) > 0 )
                $time_spent[] = $this->time2float( $ticket['time'] );

            // types
            $types[] = $ticket['type'];

        }

        $types = empty($types) ? __('n/a', 'wpguards') : array_count_values($types);
        arsort($types);

        $av_spent = ( empty($time_spent) ) ? __('n/a', 'wpguards') : $this->float2time( array_sum($time_spent) / count($time_spent) );

        // number of tickets
        $count = count($this->tickets);

        $stats = array(
            'count' => $count,
            'average_solve_time' => ( empty($solve_time) ) ? __('n/a', 'wpguards') : seconds2human( array_sum($solve_time) / count($solve_time) ),
            'average_time_spent' => $av_spent,
            'types' => $types,
        );

        update_option( 'wpguards_tickets_stats', $stats );

    }
    
}