<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:41
         compiled from file:style/default/templates/registration_general.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_registration_general_general']; ?>
</h3>
<div>
    <p><?php echo $this->_tpl_vars['admin_registration_general_min_age']; ?>
</p>
    <form action="registration.php?c=general" method="POST">
        <label for="age"><?php echo $this->_tpl_vars['admin_registration_age']; ?>
</label>
        <input type="number" size="2" id="age" name="age" style="width: 40px;" value="<?php echo $this->_tpl_vars['AGE']; ?>
"/>
    
</div>
<div>
    
        <p><?php echo $this->_tpl_vars['admin_registration_general_info']; ?>
</p>
        <table border="0" id="reg_table"> 
            <tr>
                <th colspan="2">
                    <b><?php echo $this->_tpl_vars['admin_registration_general_everybody']; ?>
</b>
                </th>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="reg_method" value="everybody" style="width: auto;" id="everybody"<?php echo $this->_tpl_vars['EVERY']; ?>
/>
                </td>
                <td>
                    <label for="everybody"><?php echo $this->_tpl_vars['admin_registration_general_everybody_info']; ?>
</label>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <b><?php echo $this->_tpl_vars['admin_registration_general_activate']; ?>
</b>
                </th>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="reg_method" value="activate" style="width: auto;" id="activate"<?php echo $this->_tpl_vars['ACTIVATE']; ?>
/>
                </td>
                <td>
                    <label for="activate"><?php echo $this->_tpl_vars['admin_registration_general_activate_info']; ?>
</label>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <b><?php echo $this->_tpl_vars['admin_registration_general_code']; ?>
</b>
                </th>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="reg_method" value="code" style="width: auto;" id="code"<?php echo $this->_tpl_vars['CODE']; ?>
/>
                </td>
                <td>
                    <label for="code"><?php echo $this->_tpl_vars['admin_registration_general_code_info']; ?>
</label>
                </td>
            </tr>        
        </table>
        <p></p>
        <input type="submit" name="send" value="<?php echo $this->_tpl_vars['admin_registration_save']; ?>
" class="jui-button"/>
    </form>
</div>