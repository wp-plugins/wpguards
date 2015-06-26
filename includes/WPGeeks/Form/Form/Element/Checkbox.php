<?php
/**
 * Form helper
 * Single element class - checkbox input
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Element_Checkbox extends WPGeeks_Form_Element
{
    private $value;

    public function __construct($name, array $elementConfig)
    {
        parent::__construct($name, $elementConfig);
        
        // set default tag
        $this->element->setTag('input');

        // add default input type
        $this->element->setAttribute('type', 'checkbox');

        // set attributes
        $this->setAttributes($elementConfig);

        // add default name
        $this->element->setAttribute('name', $this->name . '[]');

        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function renderElement()
    {
        $output = '';

        $options = $this->getConfig('options');
        if (is_array($this->getConfig('options')) && !empty($options)) {
            foreach ($this->getConfig('options') as $slug => $readable) {
                $viewFile = dirname(dirname(__FILE__)) . '/View/Elements/' . ucfirst($this->getConfig('type')) . '/Element.php';
                
                $this->element->setAttribute('value', $slug);

                if (is_array($this->getValue()) && in_array($slug, $this->getValue())) {
                    $this->element->setAttribute('checked', true);
                }

                if (file_exists($viewFile)) {
                    ob_start();
                        include ($viewFile);
                    $output .= ob_get_clean();
                }
            }
        }

        return $output;
    }
}