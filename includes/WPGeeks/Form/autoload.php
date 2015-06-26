<?php
/**
 * autoloader
 * 
 * @author      Max Matloka (max@matloka.me)
 * @package     WPGeeks
 * @subpackage  Forms
 */

function WPGeeks_Form_autoload($className)
{
    if (substr_compare($className, 'WPGeeks_Form', 0, 12) !== 0) {
        return;
    }

    $className     = substr($className, 8);
    $classElements = explode('_', $className);

    $fileName      = dirname(__FILE__) . '/' . implode(DIRECTORY_SEPARATOR, $classElements) . '.php';

    if (file_exists($fileName)) {
    	require $fileName;
    }
   
}

spl_autoload_register('WPGeeks_Form_autoload');
