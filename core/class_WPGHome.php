<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Home tab class
*/
class WPGHome {

    public $user_plan;
    public $features;
    public $currency;

    /**
     * Class contructor
     */
    function __construct() {
        global $WPGuards;

        $this->user_plan = get_option( 'wpguards_user_plan', 'trial' );
        $this->features = $WPGuards->WPGConnection->getFeatures();
        $this->currency = get_option('wpguards_currency', 'USD');

    }

    /**
     * Displays Home sidebar
     * @return void
     */
    public function features() {
        ?>
        <div class="postbox upgrade">
            <h3><span><?php _e('Your current plan','wpguards'); ?></span></h3>
            <div class="inside">

                <div class="current-plan">
                    <div class="small-alert blue"><?php echo ucfirst($this->user_plan); ?></div>
                </div>
                
                <table id="plans">
                    <?php $this->plan_table_header(); ?>
                    <tbody>
                        <?php $this->plan_table_body(); ?>
                    </tbody>
                </table>

                <table id="backups">
                    <?php $this->backups_table_header(); ?>
                    <tbody>
                        <?php $this->backups_table_body(); ?>
                    </tbody>
                </table>

                <?php if ($this->can_upgrade()) echo order_button( '', __('Upgrade your plan','wpguards') ); ?>

            </div>
        </div>
        <?php

    }

    /**
     * Displays WPGuards news from blog
     * @return void
     */
    public function display_news() {
        global $WPGuards;
        ?>
        <div class="postbox news">
            <h3><span><?php _e('News','wpguards'); ?></span></h3>
            <div class="inside">

                <?php $WPGuards->WPGDashboard->news_output( 5, false ); ?>

            </div>
        </div>
        <?php
    }

    /**
     * Checks if user can upgrade website plan
     * @return boolean
     */
    public function can_upgrade() {

        if ($this->user_plan == 'professional' || $this->user_plan == 'custom' )
            return 0;

        return 1;
    }

    /**
     * Displays plans table header
     * @return void
     */
    public function plan_table_header() {

        echo '<thead>';
            echo '<tr>';
                echo '<td>'.__('Feature', 'wpguards').'</td>';
                echo '<td>'.__('Value', 'wpguards').'</td>';
            echo '</tr>';
        echo '</thead>';

    }

    /**
     * Displays backups table header
     * @return void
     */
    public function backups_table_header() {

        echo '<thead>';
            echo '<tr>';
                echo '<td colspan="3">'.__('Backups', 'wpguards').'</td>';
            echo '</tr>';

            echo '<tr>';
                echo '<td class="subtitle">'.__('Type', 'wpguards').'</td>';
                echo '<td class="subtitle">'.__('Period', 'wpguards').'</td>';
                echo '<td class="subtitle">'.__('Stored backups', 'wpguards').'</td>';
            echo '</tr>';
        echo '</thead>';

    }

    /**
     * Displays backups table body
     * @return void
     */
    public function backups_table_body() {

        $backups = $this->features->backups;

        foreach ($backups as $backup) {
            
            echo '<tr>';
                echo '<th>'.ucfirst(__($backup->destination, 'wpguards')).'</th>';
                echo '<td>'.ucfirst(__($backup->period, 'wpguards')).'</td>';
                echo '<td>'.__($backup->kept, 'wpguards').'</td>';
            echo '</tr>';

        }

        // translation purposes
        __('daily', 'wpguards');
        __('weekly', 'wpguards');
        __('server', 'wpguards');
        __('dropbox', 'wpguards');

    }

    /**
     * Displays plans table body
     * @return void
     */
    public function plan_table_body() {

        $opt = $this->features->general;

        if (empty($opt)) {
            echo '<tr><td colspan="2">';
            _e('Error with getting plan details.', 'wpguards');
            echo '</td></tr>';
            return false;
        }

        ?>

        <tr>
            <th><?php _e('Security monitoring', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->security_monitoring_type == 'auto' ) {
                    _e('Automatic scan', 'wpguards');
                    echo ' ';
                    printf( _n( 'every %d day', 'every %d days', $opt->security_monitoring_period, 'wpguards' ), $opt->security_monitoring_period );
                }
                else _e('Manual scan', 'wpguards'); 

                echo ', <br />';

                if ( $opt->security_monitoring_removal_fee == 1 ) _e('malvare removal at extra charge', 'wpguards');
                else _e('malvare removal included', 'wpguards');
                ?>
            </td>
        </tr>

        <tr>
            <th><?php _e('Theme and plugin updates', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->updates == 1 ) _e('Yes, monthly', 'wpguards');
                else _e('No', 'wpguards'); ?>
            </td>
        </tr>

        <tr>
            <th><?php _e('Database Optimization', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->db_optimization == 1 ) _e('Yes', 'wpguards');
                else _e('No', 'wpguards'); ?>
            </td>
        </tr>

        <tr>
            <th><?php _e('Uptime Monitoring', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->uptime == 1 ) _e('Yes', 'wpguards');
                else _e('No', 'wpguards'); ?>
            </td>
        </tr>

        <!-- <tr>
            <th><?php _e('Google Analytics', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->analytics == 1 ) _e('Yes', 'wpguards');
                else _e('No', 'wpguards'); ?>
            </td>
        </tr> -->

        <tr>
            <th><?php _e('Website diagnostic tools', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->diagnostic == 1 ) _e('Yes', 'wpguards');
                else _e('No', 'wpguards'); ?>
            </td>
        </tr>

        <tr>
            <th><?php _e('Maintenance', 'wpguards'); ?></th>
            <td>
                <?php if ( $opt->maintenance == 0 ) _e('No', 'wpguards');
                else _e($opt->maintenance, 'wpguards'); 

                // translation purposes
                __('monthly', 'wpguards');
                __('twice a year', 'wpguards');
                __('annualy', 'wpguards');
                ?>
            </td>
        </tr>

        <tr>
            <th><?php _e('Online support', 'wpguards'); ?></th>
            <td>
                <?php echo $opt->support_price.' '.$this->currency.' '.__('per hour', 'wpguards'); ?>
            </td>
        </tr>

        <?php
    }

    /**
     * Displays plugin statistics
     * @return void
     */
    public function display_statistics() {

        // General section
        echo '<div class="section-title">'.__('General', 'wpguards').'</div>';
        $this->render_general_stats();

        // Tickets section
        echo '<div class="section-title">'.__('Tickets statistics', 'wpguards').' <i class="icon-info-sign" style="cursor: pointer;" title="'.__('Please go to the Support tab to refresh those statistics', 'wpguards').'"></i></div>';
        $this->render_tickets_stats();

        // Backups section
        echo '<div class="section-title">'.__('Backups statistics', 'wpguards').' <i class="icon-info-sign" style="cursor: pointer;" title="'.__('Please go to the Backups tab to refresh those statistics', 'wpguards').'"></i></div>';
        $this->render_backups_stats();

        // Uptime monitor section
        echo '<div class="section-title">'.__('Uptime Monitor', 'wpguards').'</div>';
        $this->render_uptime_stats();

    }

    /**
     * Displays general statistics
     * @return void
     */
    public function render_general_stats() {
        $plugin = about_plugin();

        printf(__('Version: %s', 'wpguards'), $plugin['Version']);
        echo '<br />';

        printf(__('Currency: %s', 'wpguards'), $this->currency);
        echo '<br />';

        printf(__('Plan: %s', 'wpguards'), ucfirst($this->user_plan) );
        if ( $this->can_upgrade() ) echo order_button( false, __('Upgrade', 'wpguards'), 'button-secondary upgrade-plan' );
        echo '<br />';

        if ($this->user_plan != 'basic' ) // && $this->user_plan != 'trial')
            printf(__('Plan expiration date: %s', 'wpguards'), $this->get_expiration_date());

    }

    /**
     * Gets plan expiration date
     * @return string date
     */
    public function get_expiration_date() {
        global $WPGuards;

        $timestamp = $WPGuards->WPGConnection->getExpirationDate();
 
        if ($timestamp == 0) return __('unexpired', 'wpguards');

        if ($timestamp - time() < 0) return __('in a few minutes', 'wpguards');

        $days = round( ( $timestamp - time() ) / 86400 );
 
        $left = ($days) ? wpg_format_time($timestamp).', '.sprintf( _n( '%d day left', '%d days left', $days, 'wpguards' ), $days ) : __('today at', 'wpguards').' '.date( "G:i", $timestamp + $WPGuards->time_offset );
        return $left;
    }

    /**
     * Displays tickets statistics
     * @return void
     */
    public function render_tickets_stats() {

        $default = array(
            'count' => 0,
            'average_solve_time' => 0, 
            'average_time_spent' => 0,
            'types' => array(
                        'problem' => 0,
                        'incident' => 0,
                        'question' => 0,
                        'task' => 0
                    ),
        );
        $tickets_stats = get_option( 'wpguards_tickets_stats', $default );


        echo '<table id="tickets-stats">';

            foreach ($tickets_stats as $name => $stat) {
                
                echo '<tr class="'.$name.'">';

                    switch ($name) {
                        case 'count':
                            $this->display_stats_count_row($stat);
                            break;

                        case 'average_solve_time':
                            $this->display_stats_average_solve_time_row($stat);
                            break;

                        case 'average_time_spent':
                            $this->display_stats_average_time_spent_row($stat);
                            break;

                        case 'types':
                            $this->display_stats_types_row($stat);
                            break;
                        
                    }

                echo '</tr>';

            }

        echo '</table>';  

    }

    /**
     * Displays backups statistics
     * @return void
     */
    public function render_backups_stats() {
        global $WPGuards;

        $time = $WPGuards->WPGConnection->getNextBackupTime();

        $default = array(
            'available_backups' => 0,
            'average_size' => 0,
            'time' => $time, 
        );
        $backups_stats = get_option( 'wpguards_backups_stats', $default );

        // refresh stats if they are too old
        if ($backups_stats['time'] != $time) {
            $backups = new WPGBackups();
        	$backups_stats = $backups->prepare_backups_stats();
        }

        echo '<table id="backups-stats">';

            foreach ( (array) $backups_stats as $name => $stat) {
                
                echo '<tr class="'.$name.'">';

                    switch ($name) {
                        case 'available_backups':
                            $this->display_stats_available_backups_row($stat);
                            break;
                        
                        case 'average_size':
                            $this->display_stats_average_size_row($stat);
                            break;

                        case 'time':
                            $this->display_stats_time_row($stat);
                            break;
                        
                    }

                echo '</tr>';

            }

        echo '</table>';  

    }

    /**
     * Displays Uptime Monitor
     * @return void
     */
    public function render_uptime_stats() {

        $diagnostic = new WPGDiagnostic();
        $uptime = $diagnostic->get_uptime_monitor();

        echo '<div class="uptime-timeline">';
            foreach ( array_reverse($uptime) as $bar) {

                echo '<span class="element '.$bar['type'].'" title="'.$bar['description'].'" style="width: '.$bar['percent'].'%;"></span>';

            }
        echo '</div>';

        echo $diagnostic->get_uptime_monitor_overview($uptime);

    }


    /* Tickets */


    /**
     * Displays tickets count row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_count_row($stat) {

        echo '<th>'.__('Sent tickets', 'wpguards').'</th>';
        echo '<td>'.$stat.'</td>';

    }

    /**
     * Displays tickets solve time row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_average_solve_time_row($stat) {

        echo '<th>'.__('Average solving time', 'wpguards').'</th>';
        echo '<td>'.$stat.'</td>';

    }

    /**
     * Displays tickets time spent row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_average_time_spent_row($stat) {

        echo '<th>'.__('Average time spent on ticket', 'wpguards').'</th>';
        echo '<td>'.$stat.'</td>';

    }

    /**
     * Displays tickets types row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_types_row($stat) {

        echo '<th>'.__('Types of tickets', 'wpguards').'</th>';
        echo '<td>';
            foreach ($stat as $type => $count) {
                echo '<span class="type-counter">';
                    printf( _n( '%d '.$type, '%d '.$type.'s', $count, 'wpguards' ), $count );
                echo '</span>';
            }
        echo '</td>';

    }


    /* Backups */


    /**
     * Displays backups size row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_average_size_row($stat) {

        echo '<th>'.__('Average backup size', 'wpguards').'</th>';
        echo '<td>'.$stat.'</td>';

    }

    /**
     * Displays backups number row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_available_backups_row($stat) {

        echo '<th>'.__('Available backups', 'wpguards').'</th>';
        echo '<td>'.$stat.'</td>';

    }

    /**
     * Displays backups time row
     * @param  array $stat single statistics
     * @return void
     */
    public function display_stats_time_row($stat) {

        if ($stat <= 0) {
            _e('Refresh statistics by accessing the Backups tab', 'wpguards');
            return false;
        }

        echo '<th>'.__('Next backup will be made within', 'wpguards').'</th>';
        echo '<td>';
            echo '<acronym title="'.wpg_format_time($stat).'">';
                echo seconds2human( $stat - time() );
            echo '</acronym>';
        echo '</td>';

    }

    
}