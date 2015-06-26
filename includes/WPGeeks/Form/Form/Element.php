<?php
/**
 * Form helper
 * Single element class
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

abstract class WPGeeks_Form_Element
{
    protected $name;
    protected $config;
    protected $element;
    protected $validators;

    /**
     * Attributes valid for the input tag
     *
     * @var array
     */
    protected $validAttributes = array(
        'name'           => true,
        'accept'         => true,
        'alt'            => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'checked'        => true,
        'dirname'        => true,
        'disabled'       => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'height'         => true,
        'list'           => true,
        'max'            => true,
        'maxlength'      => true,
        'min'            => true,
        'multiple'       => true,
        'pattern'        => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'size'           => true,
        'src'            => true,
        'step'           => true,
        'value'          => true,
        'width'          => true,

        // global HTML atts
        'class'          => true,
        'hidden'         => true,
        'id'             => true,
        'style'          => true,
        'title'          => true,
    );

    /**
     * Class constructor. Creates instance of element.
     * 
     * @access public
     * 
     * @param  string  $name           Element name
     * @param  array   $elementConfig  array containing important data for field.
     *                                 It is allowed to use all HTML attributes inside
     *                                 but 'type'
     * 
     * @return this
     */
    public function __construct($name, array $elementConfig)
    {
        $this->name    = $name;
        $this->config  = $elementConfig;
        $this->element = new WPGeeks_HTML;

        return $this;
    }

    /**
     * Returns element name
     * 
     * @access public
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns element ID
     *
     * Strips brackets from name
     * 
     * @access public
     * 
     * @return string
     */
    public function getID()
    {

        $id = str_replace( '[', '_', $this->name );
        $id = str_replace( ']', '', $id );
        $id = str_replace( '-', '_', $id );
        $id = strtolower( $id );

        return $id;
    }

    /**
     * Returns element label
     * 
     * @access public
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->config['label'];
    }

    /**
     * Returns element value
     * 
     * @access public
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->element->getAttribute('value');
    }

    /**
     * Returns config or single value if $param was given
     * 
     * @access public
     * 
     * @param  string  $param  Name of parameter to return
     * 
     * @return mixed
     */
    public function getConfig($param = null)
    {
        if (isset($param)) {
            if (isset($this->config[$param])) {
                return $this->config[$param];
            } else {
                return false;
            }
        }

        return $this->config;
    }


    /**
     * Sets new element config
     * 
     * @access public
     * 
     * @param  array   $elementConfig  array containing important data for field.
     *                                 It is allowed to use all HTML attributes inside
     *                                 but 'type'
     * 
     * @return this
     */
    protected function setAttributes(array $elementConfig)
    {
        $this->config = $elementConfig;

        // element settings
        if (!empty($this->config)) {
            // set element attributes
            foreach ($this->config as $param => $value) {

                // continue on invalid attributes or another settings
                // please note that type attribute is ALWAYS omitted
                if (!(isset($this->validAttributes[$param]) || 'data-' == substr($param, 0, 5))) {
                    continue;
                }

                // add attributes
                $this->element->setAttribute($param, $value);
            }
        }

        return $this;
    }

    /**
     * Sets new validator for current field
     * 
     * @access public
     * 
     * @param  WPGeeks_Form_Validator  elementConfig  Validator object
     * 
     * @return this
     */
    public function setValidator($validator)
    {
        if (is_subclass_of($validator, 'WPGeeks_Form_Validator')) {
            $this->validators[] = $validator;
        }

        return $this;
    }

    /**
     * Sets new value for current field
     * 
     * @access public
     * 
     * @param  string  value  New value of field
     * 
     * @return this
     */
    public function setValue($value)
    {
        if (function_exists('esc_attr')) {
            $value = esc_attr($value);
        }
        
        $this->element->setAttribute('value', $value);

        return $this;
    }

    /**
     * Method fired all available validators
     * 
     * @access public
     * 
     * @return bool|string  true on success
     *                      error message on fail
     */
    public function isValid()
    {
        // if there are no validators return true
        if (!is_array($this->validators) || empty($this->validators)) {
            return true;
        }

        // try validators
        try {
            foreach ($this->validators as $validator) {
                $validator->validate($this->getLabel(), $this->getValue());
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Method renders row with input in it
     * 
     * @access public
     *
     * @param  string  $template  template name
     * @return string
     */
    public function renderRow($template = null)
    {
        // check first input specific view
        if (isset($template)) {
            $viewFile = dirname(__FILE__) . '/View/Elements/' . ucfirst($this->getConfig('type')) . '/Row-' . $template . '.php';
        } else {
            $viewFile = dirname(__FILE__) . '/View/Elements/' . ucfirst($this->getConfig('type')) . '/Row.php';
        }

        // then check template specific view
        if (!file_exists($viewFile) && isset($template)) {
            $viewFile = dirname(__FILE__) . '/View/Row-' . $template . '.php';
        }

        // then check default view
        if (!file_exists($viewFile)) {
            $viewFile = dirname(__FILE__) . '/View/Row.php';
        }

        // render element
        $element = $this->renderElement();

        // set label data
        $label = new WPGeeks_HTML('label');

        if ($this->getConfig('id') != null) {
            $label->setAttribute('for', $this->getConfig('id'));
        }

        ob_start();
            include ($viewFile);
        return ob_get_clean();
    }

    /**
     * Method renders element
     * 
     * @access public
     * 
     * @return string
     */
    public function renderElement()
    {
        $viewFile = dirname(__FILE__) . '/View/Elements/' . ucfirst($this->getConfig('type')) . '/Element.php';
        
        if (file_exists($viewFile)) {
            ob_start();
                include ($viewFile);
            return ob_get_clean();
        }

        // return element rendered by WPGeeks_HTML class
        return $this->element->getRenderedTag();
    }
}