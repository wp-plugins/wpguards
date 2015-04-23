<?php
/**
 * Form helper
 * Single element class - text input
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Element_Select extends WPGeeks_Form_Element
{
    public function __construct($name, array $elementConfig)
    {
        parent::__construct($name, $elementConfig);
        
        // set default tag
        $this->element->setTag('select');

        // add default name
        $this->element->setAttribute('name', $this->name);

        // set attributes
        $this->setAttributes($elementConfig);

        return $this;
    }

    public function renderElement()
    {
        // render <select> tag
        $output  = $this->element->getRenderedTag();

        // render options
        $options = $this->getConfig('options');
        if (is_array($options) && !empty($options)) {
            foreach ($this->getConfig('options') as $slug => $readable) {
                // create <option> tag
                $option = new WPGeeks_HTML('option');
                $option->setAttribute('value', $slug);

                // set value
                if ($this->getValue() != null && $this->getValue() == $slug) {
                    $option->setAttribute('selected', 'selected');
                }
                
                $output .= $option->getRenderedTag();
                $output .= $readable;
                $output .= $option->getRenderedClosingTag();
            }
        }

        $output .= $this->element->getRenderedClosingTag();

        return $output;
    }
}