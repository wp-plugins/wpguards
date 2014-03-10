<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Admin Class
*/
class WPGDashboard {

	public $feed = 'http://wpguards.com/feed/?post_type=plugin_news';
	
	/**
	 * WPGDashboard Constructor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'wp_dashboard_setup', array($this, 'add_widget') );

	}

	/**
	 * Adds widgets to Admin Dashboard
	 * 
	 * @access public
	 * @return void
	 */
	public function add_widget() {

		wp_add_dashboard_widget(
				 'dashboard_wpguards',		    // Widget slug.
				 __('WPGuards', 'wpguards'),    // Title.
                 array($this, 'widget_output'), // Display function.
				 array($this, 'widget_config')  // Config function.
		);

	}

	/**
     * Widget output
     * 
     * @access public
     * @return void
     */
    public function widget_output() {

        $option = get_option('wpguards_widget_options');

        if ($option['display_news'])
            $this->news_output();

        if ($option['display_support'])
            $this->support_output();

        /*if ($option['display_analytics'])
            $this->analytics_output();*/

        if ($option['display_backups'])
            $this->backups_output();

        echo '<div class="clear"></div>';

    }

    /**
	 * Widget configuration
	 * 
	 * @access public
	 * @return void
	 */
	public function widget_config( $widget_id ) {

        $option = get_option('wpguards_widget_options');

        // Update widget options
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['wpguards_options_submit']) ) {
            update_option( 'wpguards_widget_options', $_POST['wpg_option'] );
        }
        ?>

        <h4><?php _e('Display settings', 'wpguards'); ?></h4>

        <p>
            <input type="checkbox" id="option-section-news" name="wpg_option[display_news]" value="1" <?php checked( $option['display_news'], 1); ?> />
            <label for="option-section-news"><?php _e('Display news', 'wpguards'); ?></label>
        </p>

        <p>
            <input type="checkbox" id="option-section-support" name="wpg_option[display_support]" value="1" <?php checked( $option['display_support'], 1); ?> />
            <label for="option-section-support"><?php _e('Display support', 'wpguards'); ?></label>
        </p>

        <!-- <p>
            <input type="checkbox" id="option-section-analytics" name="wpg_option[display_analytics]" value="1" <?php checked( $option['display_analytics'], 1); ?> />
            <label for="option-section-analytics"><?php _e('Display analytics', 'wpguards'); ?></label>
        </p> -->
        <p>
            <input type="checkbox" id="option-section-backups" name="wpg_option[display_backups]" value="1" <?php checked( $option['display_backups'], 1); ?> />
            <label for="option-section-backups"><?php _e('Display backups', 'wpguards'); ?></label>
        </p>
        
        <input name="wpguards_options_submit" type="hidden" value="1" />

        <?php
	}

	/**
	 * Handle div output
	 * 
	 * @access public
	 * @return void
	 */
	public function echo_handler() {

		//echo '<div class="handlediv" title="'.__('Click to toggle', 'wpguards').'"><br /></div>';

	}

	/**
	 * Handle see more link output
	 * 
	 * @access public
	 * @return void
	 */
	public function echo_more( $link ) {

		echo '<a href="'.admin_url('admin.php?page=wpguards_'.$link).'" class="see_more">'.__('See more', 'wpguards').'</a>';

	}

	/**
	 * News section output
	 * 
	 * @access public
	 * @return void
	 */
	public function news_output( $items = 3, $display_title = true ) {

		include_once(ABSPATH.WPINC.'/feed.php');
		
		$rss = fetch_feed( $this->feed );

		if (!is_wp_error( $rss ) ) {

			$maxitems = $rss->get_item_quantity( $items ); 
			$rss_items = $rss->get_items( 0, $maxitems ); 

		} else {
			$maxitems = 0;
		}
	
		echo '<section class="news">';

			if ($display_title) {
				echo '<div id="wpg-rss" class="handle">';
					echo '<span class="section-title">'.__('News', 'wpguards').'</span>';
					$this->echo_handler();
				echo '</div>';
			}
			
			echo '<div class="rss-widget wpg-rss">';

			echo '<ul>';
			
				if ( $maxitems == 0 ) {
					echo '<li>'.__( 'No item', 'wpguards').'.</li>';
				} else {

					foreach ( $rss_items as $item ) :

						$item_date = human_time_diff( $item->get_date('U'), current_time('timestamp')).' '.__( 'ago', 'guards' );
						
						echo '<li>';

							echo '<strong>'.esc_html( $item->get_title() ).'</strong>';

							echo ' <span class="rss-date">'.$item_date.'</span><br />';

							/*$content = wp_trim_words( $item->get_content(), 30 );
							echo $content;*/

                            echo $item->get_content();

						echo '</li>';

					endforeach;
				}

			echo '</ul></div>';

		echo '</section>';

	}

	/**
	 * Support section output
	 * 
	 * @access public
	 * @return void
	 */
	public function support_output() {

        $support = new WPGTickets();

		echo '<section class="support">';

			echo '<div id="wpg-support" class="handle">';
				echo '<span class="section-title">'.__('Support', 'wpguards').'</span>';
				$this->echo_handler();
				$this->echo_more('support');
			echo '</div>';

			echo '<div class="wpg-support">';

                echo '<strong>';
                    _e('Send new ticket', 'wpguards');
                echo '</strong>';

				$support->display_ticket_form();

			echo '</div>';

		echo '</section>';

	}

	/**
     * Analytics section output
     * 
     * @access public
     * @return void
     */
    public function analytics_output() {

        echo '<section class="analytics">';

            echo '<div id="wpg-analytics" class="handle">';
                echo '<span class="section-title">'.__('Analytics', 'wpguards').'</span>';
                $this->echo_handler();
                $this->echo_more('analytics');
            echo '</div>';

            echo '<div class="wpg-analytics">';

                ga_dash_content();

            echo '</div>';

        echo '</section>';

    }

    /**
	 * Backups section output
	 * 
	 * @access public
	 * @return void
	 */
	public function backups_output() {

		echo '<section class="backups">';

			echo '<div id="wpg-backups" class="handle">';
				echo '<span class="section-title">'.__('Backups', 'wpguards').'</span>';
				$this->echo_handler();
				$this->echo_more('backups');
			echo '</div>';

			echo '<div class="wpg-backups">';

				$backups = new WPGBackups();
				$backups->simple_backups();

				echo '<a href="'.admin_url('admin.php?page=wpguards_backups').'" class="button button-primary alignright">'.__('Download or restore', 'wpguards').'</a>';

			echo '</div>';

		echo '</section>';

	}

}

?>