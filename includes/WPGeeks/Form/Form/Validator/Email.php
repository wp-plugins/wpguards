<?php
/**
 * Form helper
 * e-mail validator
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Validator_Email extends WPGeeks_Form_Validator
{
    public function validate($elementName, $value = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Exception( sprintf( __( '%s field requires valid e-mail.', 'pagebox' ), $elementName ) );
            // FormException? @todo
        }

        return true;
    }
}