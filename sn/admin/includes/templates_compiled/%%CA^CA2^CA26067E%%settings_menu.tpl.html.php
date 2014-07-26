<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:26
         compiled from file:style/default/templates/settings_menu.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_settings_menu']; ?>
</h3>
<p><?php echo $this->_tpl_vars['admin_settings_menu_info']; ?>
</p>
<script>
$("document").ready(function(){
	$("#menu_sorter").sortable({
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
        tolerance: 'intersect',
        update: function(){
            $.get('controllers/ajaxMenuController.php?setPosition=1&'+$("#menu_sorter").sortable('serialize'), function(){});
        }
	})
	.disableSelection();	
		
	$("#addDialog").dialog({
		autoOpen:false,
		modal:true,
		width:400,
		height:280,
		buttons: {
			"<?php echo $this->_tpl_vars['admin_settings_menu_save']; ?>
": function(){
				if(saveData("Add"))
					$(this).dialog('close');
			},
			"<?php echo $this->_tpl_vars['admin_settings_menu_close']; ?>
": function(){
				$(this).dialog('close');
			}
		}
	})		
	
	$("#detailDialog").dialog({
		autoOpen:false,
		modal:true,
		width:400,
		height:280,
		buttons: {
			"<?php echo $this->_tpl_vars['admin_settings_menu_save']; ?>
": function(){
				if(saveData("Detail"))
					$(this).dialog('close');
			},
			"<?php echo $this->_tpl_vars['admin_settings_menu_close']; ?>
": function(){
				$(this).dialog('close');
			}
		}
	})
	
	$(".cunity_menu_item").click(function(){		
		$.post("controllers/ajaxMenuController.php",{c:"getDetail",id:$(this).attr('id')},function(data){
			$("#detailDialog")
				.html(data)
				.dialog('open');
		})		
	})
	
	$("#icon").live('keyup',function(){
		val = $("#icon").val().replace('[STYLE]',"<?php echo $this->_tpl_vars['STYLE']; ?>
");
		$("#icon_preview").attr('src','../'+val)
	})
})

function saveData(type){
	var name = $("#name"+type).val();
	var target = $("#target"+type).val();
	var icon = $("#icon"+type).val();	
	$.post("controllers/ajaxMenuController.php",{c:"saveData",name:name,target:target,icon:icon},function(){});
	return true;
}

function addEntry(){	
	$("#addDialog")			
		.dialog('open');
}

function deleteEntry(id){
	apprise('<?php echo $this->_tpl_vars['admin_settings_menu_confirm_delete']; ?>
',{verify:true},function(r){
		if(r){
			$.post("controllers/ajaxMenuController.php",{c:"deleteEntry",id:id},function(){});
			apprise('<?php echo $this->_tpl_vars['admin_settings_menu_delete_success']; ?>
');
			$("#detailDialog").dialog('close');
			$("#menu-"+id).remove();
		}
			
	})	
}
</script>
<style>
#menu_sorter { list-style-type: none; margin: 0; padding: 0; width: 60%; }
#menu_sorter li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 13px; height: 18px; width:300px;cursor:pointer}
#menu_sorter li span { position: absolute; margin-left: -1.3em; }
.ui-state-highlight { height: 18px }
</style>
<button class="jui-button" onclick="addEntry();"><?php echo $this->_tpl_vars['admin_settings_menu_add']; ?>
</button>
<ul style="margin-top:20px" id="menu_sorter"><?php echo $this->_tpl_vars['MENU_ENTRIES']; ?>
</ul>
<div id="addDialog" title="<?php echo $this->_tpl_vars['admin_settings_menu_add']; ?>
">
<div style="background-color:#fff;padding:5px;">
<label for="nameAdd">Name:</label><br/><input type="text" name="name" id="nameAdd" style="width:300px"/><br/>
<label for="targetAdd"><?php echo $this->_tpl_vars['admin_settings_menu_target']; ?>
:</label><br/><input type="text" id="targetAdd" name="target" style="width:300px"/><br/>
<label for="iconAdd" style="vertical-align:middle">Icon:</label>
<div>
	<input type="text" name="icon" id="iconAdd" style="vertical-align:top;width:300px"/><img src="../style/<?php echo $this->_tpl_vars['STYLE']; ?>
/img/menuEntry.png" style="padding: 2px;background-color:#fff;" id="icon_preview"/>
	<br/>
	<small><?php echo $this->_tpl_vars['admin_settings_menu_icon_info']; ?>
</small>
</div>
</div>
<div id="detailDialog" title="Details"></div>