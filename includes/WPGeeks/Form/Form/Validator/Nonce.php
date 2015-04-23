<?php
/**
 * Form helper
 * Nonce validator
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Validator_Nonce extends WPGeeks_Form_Validator
{
    public function validate($elementName, $value = null)
    {
        if (function_exists('wp_verify_nonce')) {
            if (!wp_verify_nonce($value, 'wpgeeks')) {
                throw new Exception( __( 'Something went wrong.', 'pagebox' ) );
                // FormException? @todo
            }
        }

        return true;
    }
}