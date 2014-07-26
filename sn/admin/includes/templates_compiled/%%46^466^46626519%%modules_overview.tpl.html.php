<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:17
         compiled from file:style/default/templates/modules_overview.tpl.html */ ?>
<script language="javascript" type="text/javascript">
$("document").ready(function(){
    $(".on").change(function(){
        $(this).parent().removeClass();
        $(this).parent().addClass("cell_on");
        $(this).parent().addClass("cell_on");
        $("#"+$(this).attr("name")+"_off").addClass("cell_none");
        $.ajax({
            type: "POST",
            url: "includes/ajaxModules.php?p=on",
            data: "id="+$(this).attr('id'),
            success: function(data)
            {
                $("#change_success").fadeIn();
                $("#change_success").delay(200);
                $("#change_success").fadeOut();
            }
        });
    });
    
    $(".off").change(function(){
        var id = $(this).attr('id');        
        $.ajax({
            type: "POST",
            url: "includes/ajaxModules.php?p=off",
            data: "id="+id,
            success: function(data)
            {
                if(data!="")
                {
                    apprise(data,{verify:true}, function(r){
                    if(r)
                    {
                        $.ajax({
                            type: "POST",
                            url: "includes/ajaxModules.php?p=off&v=1",
                            data: "id="+id,
                            success: function(data)
                            {
                                $(this).parent().removeClass();
                                $(this).parent().addClass("cell_off");
                                $("#"+$(this).attr("name")+"_on").addClass("cell_none");
                                $("#change_success").fadeIn();
                                $("#change_success").delay(200);
                                $("#change_success").fadeOut();
                            }
                        });
                    }
                    else
                    {
                        ids = id.split('_');
                        $("#"+ids[0]+"_1_"+ids[2]).attr('checked', 'checked');
                        $(this).removeAttr('checked');
                    }
                    });
                }
                else
                {
                    $(this).parent().removeClass();
                    $(this).parent().addClass("cell_off");
                    $("#"+$(this).attr("name")+"_on").addClass("cell_none");
                    $("#change_success").fadeIn();
                    $("#change_success").delay(200);
                    $("#change_success").fadeOut();
                }
            }
        });
    })
    
    $(".cell_name").click(function(){
        if($("#"+$(this).attr('id')+"_1").attr("checked") == true)
        {
            $("#"+$(this).attr('id')+"_0").attr("checked","checked");
            $("#"+$(this).attr('id')+"_off").removeClass();
            $("#"+$(this).attr('id')+"_off").addClass("cell_off");
            $("#"+$(this).attr("id")+"_on").removeClass();
            $("#"+$(this).attr("id")+"_on").addClass("cell_none");
            $.ajax({
                type: "POST",
                url: "includes/ajaxModules.php?p=off",
                data: "id="+$(this).attr('id'),
                success: function(data)
                {
                    $("#change_success").fadeIn();
                    $("#change_success").delay(200);
                    $("#change_success").fadeOut();
                }
            });
        }
        else
        {
            $("#"+$(this).attr('id')+"_1").attr("checked","checked");
            $("#"+$(this).attr('id')+"_on").removeClass();
            $("#"+$(this).attr('id')+"_on").addClass("cell_on");
            $("#"+$(this).attr("id")+"_off").removeClass();
            $("#"+$(this).attr("id")+"_off").addClass("cell_none");
            $.ajax({
                type: "POST",
                url: "includes/ajaxModules.php?p=on",
                data: "id="+$(this).attr('id'),
                success: function(data)
                {
                    $("#change_success").fadeIn();
                    $("#change_success").delay(200);
                    $("#change_success").fadeOut();
                }
            });
        }
    })
})

</script>
<div id="change_success" style="display: none;">
    <div id="change_success_inner">
        <img src="style/default/img/check.png"/><?php echo $this->_tpl_vars['modules_saved_changes']; ?>

    </div>
</div>
<h3><?php echo $this->_tpl_vars['modules_modules']; ?>
</h3>
<table border="0">
<?php echo $this->_tpl_vars['LIST']; ?>

</table>