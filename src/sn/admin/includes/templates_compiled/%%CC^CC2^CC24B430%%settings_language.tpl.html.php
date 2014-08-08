<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:24
         compiled from file:style/default/templates/settings_language.tpl.html */ ?>
<div id="change_success" style="display: none;">
    <div id="change_success_inner">
        <img src="style/default/img/check.png"/><?php echo $this->_tpl_vars['admin_settings_saved_changes']; ?>

    </div>
</div>
<h3><?php echo $this->_tpl_vars['admin_settings_language']; ?>
</h3>
<p><?php echo $this->_tpl_vars['admin_settings_language_please_select']; ?>
</p>
<script language="javascript" type="text/javascript">
$("document").ready(function(){
    $("#german").change(function(){
        $("#english_cell").removeClass();
        $("#english_cell").addClass("cell_none");
        $("#german_cell").removeClass();
        $("#german_cell").addClass("cell_on");
    })
    
    $("#english").change(function(){
        $("#german_cell").removeClass();
        $("#german_cell").addClass("cell_none");
        $("#english_cell").removeClass();
        $("#english_cell").addClass("cell_on");
    })
})
</script>
<form action="settings.php?c=language" method="POST">
    <label for="german">
    <div class="cell_<?php echo $this->_tpl_vars['GERMAN_POWER']; ?>
" style="width: 120px; float: none;" id="german_cell">
    <input type="radio" name="language" id="german" value="german" <?php echo $this->_tpl_vars['GERMAN']; ?>
 style="width: auto;"/>
    <?php echo $this->_tpl_vars['admin_settings_language_german']; ?>

    </div>
    </label>
    <label for="english">        
    <div class="cell_<?php echo $this->_tpl_vars['ENGLISH_POWER']; ?>
" style="width: 120px; float: none;" id="english_cell">
    <input type="radio" name="language" id="english" value="english" <?php echo $this->_tpl_vars['ENGLISH']; ?>
 style="width: auto;"/>
    <?php echo $this->_tpl_vars['admin_settings_language_english']; ?>
        
    </div>
    </label>
    <br class="clear"/>    
    <input type="submit" value="<?php echo $this->_tpl_vars['admin_settings_save']; ?>
" name="send" class="jui-button"/>
</form>