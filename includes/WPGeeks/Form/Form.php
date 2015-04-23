<?php
/**
 * Form helper. Renders HTML form
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form
{
    protected $elements;
    protected $messages;

    /**
     * Add new element to form. Please note that
     * $element variable should be extension of 
     * WPGeeks_Form_Element
     * 
     * @access public
     * 
     * @param  $element Element object
     * 
     * @return this
     */
    public function add($element)
    {
        if (is_subclass_of($element, 'WPGeeks_Form_Element')) {
            $this->elements[$element->getName()] = $element;
        }

        return $this;
    }

    /**
     * Delete element from form.
     * 
     * @access public
     * 
     * @param  $elementName  Name of element you'd like to delete
     * 
     * @return this
     */
    public function delete($elementName)
    {
        if (isset($this->elements[$elementName])) {
            unset($this->elements[$elementName]);
        }
        
        return $this;
    }

    /**
     * Render Method
     * 
     * @access public
     * 
     * @param  $groupName  string  Name of group to render
     *                             group stands for group parameter
     *                             in form config. It could be useful
     *                             to display tabs, or just a part of
     *                             template
     * 
     * @return this
     */
    public function render($groupName = null)
    {
        $output = '';

        // prepare to render each element
        foreach ($this->elements as $element) {
            
            if (isset($groupName) && $element->getConfig('group') != $groupName) {
                continue;
            }

            $output .= $element->renderRow();
            
        }
        
        return $output;
        
    }

    public function elementRender($elementName) 
    {
        if (isset($elementName) && isset($this->elements[$elementName])) {
            return $this->elements[$elementName]->render();
        }

        return false;
    }

    /**
     * Set values of fields in form. It should be done before validating.
     * 
     * @access public
     * 
     * @param  array  $values  Array with values matching pattern:
     *                         $fieldName => $fieldValue
     * 
     * @return this
     */
    public function setValues(array $values)
    {
        if (empty($values)) {
            return $this;
        }

        foreach ($values as $elementName => $value) {
            if (!isset($this->elements[$elementName])) {
                continue;
            }

            $this->elements[$elementName]->setValue($value);
        }

        return $this;
    }

    /**
     * Checks if form is valid.
     * 
     * It fires all validators for each element
     * 
     * @access public
     * 
     * @return bool
     */
    public function isValid()
    {
        if (!is_array($this->elements) || empty($this->elements)) {
            return true;
        }

        foreach ($this->elements as $elementName => $element) {
            $response = $element->isValid();

            if (!is_bool($response)) {
                $this->setMessage($response);
                return false;
            }
        }

        return true;
    }

    /**
     * Sets error message
     * 
     * @access protected
     * 
     * @return this
     */
    protected function setMessage($message)
    {
        if (!is_array($this->messages)) {
            $this->messages = array();
        }

        $this->messages[] = $message;

        return $this;
    }

    /**
     * Gets all error messages
     * 
     * @access public
     * 
     * @return array
     */
    public function getMessages()
    {
        if (!is_array($this->messages) || empty($this->messages)) {
            return false;
        }

        return $this->messages;
    }

    public function getGroups()
    {
        $groups = array();

        foreach ($this->elements as $element) {

            $group = $element->getConfig('group');

            if (isset($group) && !in_array($group, $groups)) {
                $groups[] = $group;
            }

        }

        return $groups;
    }

}