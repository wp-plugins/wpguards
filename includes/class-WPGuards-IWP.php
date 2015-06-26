<?php
/**
 * The file that connects WPGuards with IWP
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/includes
 */

class WPGuards_IWP
{
    public function __construct()
    {
        /**
         * Remove IWP notices
         */
        $this->removeAction('IWP_MMB_Core', 'admin_notice', 'admin_notices');

        /**
         * Remove install/uninstall actions
         */
        $this->removeAction('IWP_MMB_Core', 'install', 'activate_' . WPGUARDS_DIR . 'dependencies/modules/manager/init.php');
        $this->removeAction('IWP_MMB_Core', 'uninstall', 'deactivate_' . WPGUARDS_DIR . 'dependencies/modules/manager/init.php');
    }

    private function removeAction($class, $method, $hook, $priority = 10) {

        global $wp_filter;

        if (isset($wp_filter[$hook][$priority]) && is_array($wp_filter[$hook][$priority])) {
            foreach ($wp_filter[$hook][$priority] as $id => $array) {

                if (is_object($array['function'][0]) && get_class($array['function'][0]) == $class && $array['function'][1] == $method) {
                    unset($wp_filter[$hook][$priority][$id]);
                }

            }
        }

    }
}