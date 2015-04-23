<?php
/**
 * Form helper
 * Required validator
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Validator_Required extends WPGeeks_Form_Validator
{
    public function validate($elementName, $value = null)
    {
        if (!isset($value) || empty($value)) {
            throw new Exception( sprintf( __( '%s field cannot be empty.', 'pagebox' ), $elementName ) );
            // FormException? @todo
        }

        return true;
    }
}