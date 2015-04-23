<?php
/**
 * Form helper
 * Single element class - textarea
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Element_Textarea extends WPGeeks_Form_Element
{
    protected $value;

    public function __construct($name, array $elementConfig)
    {
        parent::__construct($name, $elementConfig);
        
        // set default tag
        $this->element->setTag('textarea');

        // add default name
        $this->element->setAttribute('name', $this->name);

        // set attributes
        $this->setAttributes($elementConfig);

        return $this;
    }

    public function setValue($value)
    {
        if (function_exists('esc_attr')) {
            $value = esc_attr($value);
        }
        
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function renderElement()
    {
        $output  = $this->element->getRenderedTag();
        $output .= $this->value;
        $output .= $this->element->getRenderedClosingTag();

        return $output;
    }
}