<?php
/**
 * Form table view file
 * 
 * @author      Kuba Mikita
 * @package     WPGeeks
 * @subpackage  Forms
 */
?>

<div class="fieldset">
    <?php 
    if ($this->getConfig('label')):
        echo $label->getRenderedTag() . $this->getConfig('label') . $label->getRenderedClosingTag();
    endif; 
    ?>

    <?php echo $element; ?>
    
    <?php if ($this->getConfig('description')): ?>
    <span class="description"><?php echo $this->getConfig('description'); ?></span>
    <?php endif; ?>
</div>