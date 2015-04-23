<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Other tab class
*/
class WPGDiagnostic {

    public $uptime;

    /**
     * Display Diagnostic services
     * @return void
     */
    public function get_services() {

        $this->respelt();
        $this->pingdom();
        $this->w3valid();
        $this->w3links();
        $this->pagespeed();

    }

    /**
     * Displays respelt box
     * @return void
     */
    public function respelt() {

        echo '<li>
            <a href="http://respelt.com/?url='.site_url().'" target="_blank">'.__('Respelt spell checker','wpguards').' 
            <span>';
                _e('Check the spelling on your website with this simple tool.','wpguards');
        echo '</span></a></li>';

    }

    /**
     * Displays pingdom tools box
     * @return void
     */
    public function pingdom() {

        echo '<li>
            <a href="http://tools.pingdom.com/fpt/" target="_blank">'.__('Page loading time','wpguards').'
            <span>';
                _e('Your website is slow? Check why! With this tool you\'l get informations about the server requests and useful tips.','wpguards');
        echo '</span></a></li>';


    }

    /**
     * Displays W3C validator box
     * @return void
     */
    public function w3valid() {

        echo '<li>
            <a href="http://validator.w3.org/check?uri='.site_url().'" target="_blank">'.__('Validate HTML','wpguards').'
            <span>';
                _e('Google like clean websites so check your HTML sytax with w3c\'s tool.','wpguards');
        echo '</span></a></li>';


    }

    /**
     * Displays W3C links checker box
     * @return void
     */
    public function w3links() {

        echo '<li>
            <a href="http://validator.w3.org/checklink?uri='.site_url().'" target="_blank">'.__('Broken link checker','wpguards').'
            <span>';
                _e('Still getting 404 pages? Check broken links on your website.','wpguards');
        echo '</span></a></li>';


    }

    /**
     * Displays PageSpeed box
     * @return void
     */
    public function pagespeed() {

        echo '<li>
            <a href="https://developers.google.com/speed/pagespeed/insights/?url='.site_url().'" target="_blank">'.__('Google PageSpeed Insights','wpguards').'
            <span>';
                _e('Site performace testing tool from Google. Get some tips and make your website faster!','wpguards');
        echo '</span></a></li>';

    }

    /**
     * Gets the Uptime Robot Monitor array
     * @return array monitor
     */
    public function get_uptime_monitor() {
        global $WPGuards;

        $this->uptime = $WPGuards->WPGConnection->getUptime();

        if ( empty($this->uptime->log) ) return array();

        $month_dur = time() - strtotime( end($this->uptime->log)->datetime ) + $WPGuards->time_offset;

        $width = 0;
        $lenght = array(0, 0);
        $last = time() - $WPGuards->time_offset;
        $duration = array();
        foreach ( $this->uptime->log as $id => $log) {
            
            $dur = $last - strtotime( $log->datetime ) + $WPGuards->time_offset;
            $type = $this->get_uptime_type($log->type);
            $width += ceil( ( $dur / $month_dur ) * 100 );

            // for longest element fix
            $current_lenght = ceil( ( $dur / $month_dur ) * 100 );
            if ($current_lenght > $lenght[1])
                $lenght = array($id, $current_lenght);
            

            $duration[] = array(
                'duration' => $dur,
                'percent' => $current_lenght,
                'description' => date('d.m.Y H:i:s', strtotime( $log->datetime) + $WPGuards->time_offset).' # '.seconds2human($dur).' '.$type,
                'type' => $type
            );
            $last = strtotime($log->datetime) - $WPGuards->time_offset;

        }

        // longest element duration fix
        $duration[$lenght[0]]['percent'] = 100 - ($width - $duration[$lenght[0]]['percent']);

        return $duration;
    }

    /**
     * Displays Uptime Monitor Overview
     * @param  array $array monitor
     * @return string        overview
     */
    public function get_uptime_monitor_overview($array) {

        $output = $this->get_uptime_monitor_status().'. ';

        if ($this->uptime->status == 0) return $output;

        $output .= $this->uptime->alltimeuptimeratio.'% '.__('up all time', 'wpguards').', ';
        $output .= $this->uptime->customuptimeratio.'% '.__('up last month', 'wpguards');
        $output .= '<br /><br />';
        $output .= '<strong>'.__('Recorded events', 'wpguards').':</strong><br />';

        foreach ($array as $bar) {

            $output .= $bar['description'].'<br />';

        }

        return $output;

    }

    /**
     * Gets the Uptime Monitor Status by array
     * @return string status
     */
    public function get_uptime_monitor_status() {

        switch ($this->uptime->status) {
            case 0:
                return __('Monitor is paused','wpguards');
                break;

            case 1:
                return __('Your website is not checked yet','wpguards');
                break;

            case 2:
                return __('Your website is up','wpguards');
                break;

            case 8:
                return __('Your website seems to be down','wpguards');
                break;

            case 9:
                return __('Your website is down','wpguards');
                break;
            
            default:
                return 'undefined';
                break;
        }

    }

    /**
     * Gets the Uptime Monitor type by array
     * @return string type
     */
    public function get_uptime_type($type) {

        switch ($type) {
            case 1:
                return 'down';
                break;

            case 2:
                return 'up';
                break;

            case 98:
                return 'started';
                break;

            case 99:
                return 'paused';
                break;
            
            default:
                return 'undefined';
                break;
        }

    }
    
}