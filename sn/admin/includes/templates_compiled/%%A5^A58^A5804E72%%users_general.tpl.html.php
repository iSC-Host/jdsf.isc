<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:28
         compiled from file:style/default/templates/users_general.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_users_user']; ?>
</h3>
<script language="javascript" type="text/javascript">
$("document").ready(function(){
    $("#yes").change(function(){
        $("#no_cell").removeClass();
        $("#no_cell").addClass("cell_none");
        $("#yes_cell").removeClass();
        $("#yes_cell").addClass("cell_on");
    })

    $("#no").change(function(){
        $("#yes_cell").removeClass();
        $("#yes_cell").addClass("cell_none");
        $("#no_cell").removeClass();
        $("#no_cell").addClass("cell_off");
    })
    
    $("#real").change(function(){
        $("#nick_cell").removeClass();
        $("#nick_cell").addClass("cell_none");
        $("#real_cell").removeClass();
        $("#real_cell").addClass("cell_on");
    })

    $("#nick").change(function(){
        $("#real_cell").removeClass();
        $("#real_cell").addClass("cell_none");
        $("#nick_cell").removeClass();
        $("#nick_cell").addClass("cell_on");
    })
})
</script>
<div id="change_success" style="display: none;">
    <div id="change_success_inner">
        <img src="style/default/img/check.png"/><?php echo $this->_tpl_vars['admin_users_saved_successfully']; ?>

    </div>
</div>
<form action="users.php?c=general" method="POST">
    <span><?php echo $this->_tpl_vars['admin_users_def_space']; ?>
</span><br />
    <label for="space" style="font-weight: bold; font-size: 13px;"><?php echo $this->_tpl_vars['admin_users_space']; ?>
</label>
    <input type="number" id="space" name="space" style="width: 60px;" value="<?php echo $this->_tpl_vars['SPACE']; ?>
"/> <span style="font-size: 13px;">(MB)</span>
    <table border="0" id="reg_table">
        <tr>
            <th>
                <?php echo $this->_tpl_vars['admin_users_notify_info']; ?>

            </th>
            <td width="100px">&nbsp;</td>
            <th>
                <?php echo $this->_tpl_vars['admin_users_real_name_info']; ?>

            </th>
        </tr>
        <tr>
            <td>
                <label for="yes">
                    <div class="cell_<?php echo $this->_tpl_vars['YES_POWER']; ?>
" style="width: 200px;" id="yes_cell">
                    <input type="radio" name="notify_new_user" value="yes" style="width: auto;" <?php echo $this->_tpl_vars['YES']; ?>
 id="yes"/>
                    <?php echo $this->_tpl_vars['admin_users_yes']; ?>

                    </div>
                </label>
            </td>
            <td>&nbsp;</td>
            <td>
                <label for="real">
                    <div class="cell_<?php echo $this->_tpl_vars['REALNAME']; ?>
" style="width: 200px;" id="real_cell">
                    <input type="radio" name="user_name" value="full_name" style="width: auto;" <?php echo $this->_tpl_vars['REAL']; ?>
 id="real"/>
                    <?php echo $this->_tpl_vars['admin_users_real_names']; ?>

                    </div>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="no">
                    <div class="cell_<?php echo $this->_tpl_vars['NO_POWER']; ?>
" style="width: 200px;" id="no_cell">            
                    <input type="radio" name="notify_new_user" value="no" style="width: auto;" <?php echo $this->_tpl_vars['NO']; ?>
 id="no"/>
                    <?php echo $this->_tpl_vars['admin_users_no']; ?>

                    </div>
                </label>
            </td>
            <td>&nbsp;</td>
            <td>
                <label for="nick">
                    <div class="cell_<?php echo $this->_tpl_vars['NICKNAME']; ?>
" style="width: 200px;" id="nick_cell">
                    <input type="radio" name="user_name" value="nickname" style="width: auto;" <?php echo $this->_tpl_vars['NICK']; ?>
 id="nick"/>
                    <?php echo $this->_tpl_vars['admin_users_nicknames']; ?>

                    </div>
                </label>
            </td>
        </tr>

    </table>
    <br />
    <input type="submit" value="<?php echo $this->_tpl_vars['admin_users_save']; ?>
" class="jui-button" name="send"/>
</form>