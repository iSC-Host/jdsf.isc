<?php /* Smarty version 2.6.26, created on 2014-02-07 15:21:37
         compiled from file:style/newcunity/templates/overall_footer.tpl.html */ ?>
<!--
########################################################################################
## CUNITY(R) V1.0beta - An open source social network / "your private social network" ##
########################################################################################
##  Copyright (C) 2011 Smart In Media GmbH & Co. KG                                   ##
## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
##  http://www.cunity.net                                                             ##
##                                                                                    ##
########################################################################################

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or any later version.

1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

	You should have received a copy of the GNU Affero General Public License
    along with this program (under the folder LICENSE).
	If not, see <http://www.gnu.org/licenses/>.

   If your software can interact with users remotely through a computer network,
   you have to make sure that it provides a way for users to get its source.
   For example, if your program is a web application, its interface could display
   a "Source" link that leads users to an archive of the code. There are many ways
   you could offer source, and different solutions will be better for different programs;
   see section 13 of the GNU Affero General Public License for the specific requirements.

   #####################################################################################
   -->

<!-- Copyright Start -->
<div class="clear"></div>
<script language="javascript" type="text/javascript">
function contact(){
    $("document").ready(function(){
        var name = 'na';
        var mail = 'na';
        var msg =  'na';
        $("#contact_name").live('change', function(){
            name = $("#contact_name").val();

        });

        $("#contact_email").live('change', function(){
            mail = $("#contact_email").val();

        });

        $("#contact_message").live('change', function(){
            msg = $("#contact_message").val();

        });

        $.post('controllers/ajaxContactController.php?c=getForm',function(data){
                apprise(data, {verify:true,'textYes':'<?php echo $this->_tpl_vars['pages_contact_send']; ?>
', 'textNo':'<?php echo $this->_tpl_vars['pages_contact_cancel']; ?>
'},function(b){
                    if(b){
                        $.post('controllers/ajaxContactController.php?c=sendContact&name='+name+'&mail='+mail+'&msg='+msg,function(data){
                                apprise(data);
                            }
                        )
                    }
                });
            }
        )
    })
}
</script>
<div class="copyright" style="position: relative">
<p>Powered by <a href="http://www.cunity.net">CUNITY&reg;</a> - &copy; 2011 by Smart In Media</p>
<p style="position: absolute; right:10px;top:0;">
    <a href="pages.php?id=privacy"><?php echo $this->_tpl_vars['menu_privacy']; ?>
</a> |
    <a href="pages.php?id=terms"><?php echo $this->_tpl_vars['menu_terms']; ?>
</a> |
    <a href="pages.php?id=imprint"><?php echo $this->_tpl_vars['menu_imprint']; ?>
</a> |
    <a href="javascript: contact();"><?php echo $this->_tpl_vars['menu_contact']; ?>
</a>
</p>
</div>
</body>
</html>