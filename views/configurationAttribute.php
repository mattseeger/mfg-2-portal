<?php if($context->Type == 'picklist'){?>
    <label><?=$context->Label?></label>
    <select class="w3-select w3-border" name="ca_<?=$context->Name?>">
    <?php foreach($context->PicklistValues as $val){ ?>
        <option value="<?=$val?>"><?=$val?></option>
    <?php } ?>    
    </select>
<?php } elseif (TRUE) {?>
    <label><?=$context->Label?></label>
    <input class="w3-input" name="ca_<?=$context->Name?>" />
<?php } ?>