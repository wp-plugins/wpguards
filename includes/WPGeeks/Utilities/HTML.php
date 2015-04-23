<?php
/**
 * HTML helper. Renders HTML tag with attributes
 * based on provided array
 * 
 * How to use it?
 *   Let's prepare simple form input with name, id and placeholder
 *   First, we need to prepare array with element attributes
 * 
 *   $inputAtts = array(
 *       'type'        => 'text',
 *       'id'          => 'simpleInput',
 *       'name'        => 'simpleInput',
 *       'class'       => 'fancyInput fancyInputBig',
 *       'placeholder' => 'Insert your data'
 *   );
 *   
 *   Then create instance of WPGeeks_HTML. First parameter stands
 *   for tag name, second for input attributes
 * 
 *   $formInput = new WPGeeks_HTML('input', $inputAtts);
 * 
 * Ready!
 *   It is always possible to overwrite all attributes using
 *   setAttributes method or single attribute using setAttribute
 *   method. Check out its parameters (especially $append which
 *   is useful for css classes)
 *   After we decide to render input. It is possible to display
 *   just HTML atribute using getAttributes method
 *   
 *   echo $formInput->getRenderedAttributes();
 *   // returns: type="text" id="simpleInput" name="simple...
 * 
 *   tag with attributes using getTag method
 * 
 *   echo $formInput->getRenderedTag();
 *   echo $formInput;
 *   // both returns: <input type="text" id="simpleInput" ...
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Utilities
 * @since       2.0
 */

class WPGeeks_HTML 
{
    public $tag;
    private $attributes;

    /**
     * Constructor method sets tag and attributes settings
     * 
     * @access public
     * @param  string  $tag         HTML tag (such as a, p, or meta)
     * @param  array   $attributes  HTML attributes with values
     *                              'href' => 'http://example.com'
     * 
     * @return this
     */
    public function __construct($tag = null, $attributes = null)
    {
        if (isset($tag)) {
            $this->setTag($tag);
        }

        if (isset($attributes)) {
            $this->setAttributes($attributes);
        }

        return $this;
    }

    /**
     * Sets HTML tag (such as a, p or meta)
     * 
     * @access public
     * @param   string  $tag  tag name
     * 
     * @return  $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Sets HTML attributes ('href' => 'http://example.com')
     * 
     * @access public
     * @param   array  $attributes  attributes array
     * 
     * @return  $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Sets HTML attribute
     * 
     * @access public
     * @param   string  $attribute  attribute name (href)
     * @param   string  $value      attribute value (http://example.com/)
     * @param   bool    $append     on true $value variable appends in the end
     *                              of attribute value string
     *                              on false $value replaces current string
     * 
     * @return  $this
     */
    public function setAttribute($attribute, $value = false, $append = false)
    {
        if ($append && isset($this->attributes[$attribute])) {
            $this->attributes[$attribute] .= ' ' . $value;
        } else {
            $this->attributes[$attribute] = $value;
        }

        return $this;
    }

    /**
     * Gets value of HTML attribute
     * 
     * @access public
     * @param   string  $attributeName  attribute name
     * 
     * @return  mixed
     */
    public function getAttribute($attributeName)
    {
        if (isset($this->attributes[$attributeName])) {
            return $this->attributes[$attributeName];
        }

        return null;
    }

    /**
     * Returns rendered HTML attributes
     * 
     * @access public
     * 
     * @return string rendered HTML atts
     */
    public function getRenderedAttributes()
    {
        $output = '';

        if (is_array($this->attributes) && !empty($this->attributes)) {

            $attributes = array();

            foreach ($this->attributes as $attribute => $value) {
                if ($value) {
                    $attributes[] = $attribute . '="' . $value . '"'; 
                } else {
                    $attributes[] = $attribute; 
                }  
            }

            $output .= implode(' ', $attributes);
        }

        return $output;
    }

    /**
     * Returns rendered HTML tag (with attributes)
     * 
     * @access public
     * 
     * @return string rendered HTML tag with attributes
     */
    public function getRenderedTag()
    {
        $output  = '<';
        $output .= $this->tag;

        $attributes = $this->getRenderedAttributes();

        if ($attributes != '') {
            $output .= ' ' . $attributes;
        }

        $output .= '>';

        return $output;
    }

    /**
     * Returns rendered HTML closing tag
     * 
     * @access public
     * 
     * @return string rendered HTML closing tag
     */
    public function getRenderedClosingTag()
    {
        return '</' . $this->tag . '>';
    }

    /**
     * Alias for $this->getTag()
     * 
     * @access public
     */
    public function __toString()
    {
        return $this->getRenderedTag();
    }
}