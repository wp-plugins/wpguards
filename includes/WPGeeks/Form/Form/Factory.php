<?php
/**
 * Form helper. 
 * Factory for WPGeeks_Form
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

class WPGeeks_Form_Factory extends WPGeeks_Form
{
    private $validElements = array(
        'text'      => 'WPGeeks_Form_Element_Text',
        'number'    => 'WPGeeks_Form_Element_Number',
        'hidden'    => 'WPGeeks_Form_Element_Hidden',
        'textarea'  => 'WPGeeks_Form_Element_Textarea',
        'nonce'     => 'WPGeeks_Form_Element_Nonce',
        'password'  => 'WPGeeks_Form_Element_Password',
        'select'    => 'WPGeeks_Form_Element_Select'
    );

    private $form;

    /**
     * Class constructor
     * 
     * @access  public
     * 
     * @param   array  $formConfig   array containing all form fields. Each element array must
     *                               have extra key: type. It varies class to load
     *                               depending on $elementsArray.
     *                               Another extra key (optional) is validators, it can contain
     *                               all validator objects as shown below:
     *                               array(
     *                                   'type' => 'text' // type of element existing in $elementsArray
     *                                   //... default input attributes
     *                                   'validators' => array(
     *                                       new WPGeeks_Form_Validator_Required,
     *                                       new WPGeeks_Form_Validator_MinChars(6)
     *                                   )
     *                               )
     * @return   this                WPGeeks_Form instance
     */
    public function __construct(array $formConfig)
    {
        // if no config was given, there is no need to continue form
        if (empty($formConfig)) {
            return $this;
        }

        foreach ($formConfig as $element) {
            $this->addFromArray($element);
        }

        return $this;
    }

    /**
     * Add form input using given config array
     *
     * @access  public
     * 
     * @param   array   $elementArray  input config array
     * @return  this
     */
    public function addFromArray(array $elementArray)
    {
        $class = 'WPGeeks_Form_Element_' . ucfirst($elementArray['type']);

        if (!class_exists($class)) {
            return $this;
        }

        // generate new input
        $input = new $class($elementArray['name'], $elementArray);

        // set validators
        if (isset($elementArray['validators']) && !empty($elementArray['validators'])) {
            foreach ($elementArray['validators'] as $validator) {

                $input->setValidator($validator);

            }
        }

        // set values
        if (isset($elementArray['values'])) {
            $input->setValue($elementArray['values']);
        }

        // add field to Form instance
        $this->add($input);

        return $this;
    }
}
