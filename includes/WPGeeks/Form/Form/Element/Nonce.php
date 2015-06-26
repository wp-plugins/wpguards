<?php
/**
 * Form helper
 * Single element class - text input
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Element_Nonce extends WPGeeks_Form_Element
{
    public function __construct($name, array $elementConfig)
    {
        parent::__construct($name, $elementConfig);
    }

    public function renderElement()
    {
        return wp_nonce_field('wpgeeks', $this->name, true, false);
    }
}