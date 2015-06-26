<?php
/**
 * Form helper
 * Single element class - number input
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Element_Number extends WPGeeks_Form_Element
{
    public function __construct($name, array $elementConfig)
    {
        parent::__construct($name, $elementConfig);
        
        // set default tag
        $this->element->setTag('input');

        // add default input type
        $this->element->setAttribute('type', 'number');

        // add default name
        $this->element->setAttribute('name', $this->name);

        // set attributes
        $this->setAttributes($elementConfig);

        return $this;
    }
}