<?php
/*
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
*/

session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();

ini_set('session.use_cookies', true);
set_include_path($_SESSION['cunity_trunk_folder'].'/classes');

require_once 'Cunity.class.php';
require_once 'Cunity_Connector.class.php';

$cunity = new Cunity(true);
$cunity->getTemplateEngine()->setController(true);
$cunity->getSaver()->login_required(true);

$connector = new Cunity_Connector($cunity);

$loggeduser = $_SESSION['userid'];

$lang = $cunity->getLang();

error_reporting($cunity->getConfig("error_reporting"));

require '../includes/functions.php';

    if(isset($_GET['do']) && $_GET['do'] == 'add'){
        $name = $_GET['name'];
        $imp = $_GET['imp'];
        $type = $_GET['type'];
        $value_0 = $_GET['value_0'];
        $value_1 = $_GET['value_1'];
        $value_2 = $_GET['value_2'];
        $value_3 = $_GET['value_3'];
        $value_4 = $_GET['value_4'];
        $q = "INSERT INTO ".$cunity->getConfig("db_prefix")."registration_fields (name,type,value_0,value_1,value_2,value_3,value_4,importance,def,active,edit, cat) VALUES ('".mysql_real_escape_string($name)."','".mysql_real_escape_string($type)."','".mysql_real_escape_string($value_0)."','".mysql_real_escape_string($value_1)."','".mysql_real_escape_string($value_2)."','".mysql_real_escape_string($value_3)."','".mysql_real_escape_string($value_4)."','".mysql_real_escape_string($imp)."','N','Y','Y', 'extra')";
        $cunity->getDb()->query($q);
        $cunity->getDb()->query("ALTER TABLE  ".$cunity->getConfig("db_prefix")."users_details ADD  ".mysql_real_escape_string($name)." VARCHAR(100) NOT NULL");

        $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE name LIKE '".mysql_real_escape_string($name)."' LIMIT 1");
        while($data = mysql_fetch_assoc($res))
        {
            $fields .= '<tr class="row_'.$count % 2 .'">';

            if($data['def'] == 'N')
            {
                $fields .= '<td>
                                <img src="style/default/img/cross_big.png" style="cursor: pointer; width: 19px; height: 19px; margin: 2px;" title="'.$langadmin['admin_registration_fields_delete'].'" class="del_field" id="'.$data['name'].'_'.$data['id'].'"/>
                            </td>';
            }
            else
            {
                if($data['edit'] == 'Y')
                {
                    if($data['active'] == 'N')
                    {
                        $fields .= '<td>
                                        <img src="style/default/img/status-busy.png" style="cursor: pointer; width: 19px; height: 19px; margin: 2px;" title="'.$langadmin['admin_registration_fields_active'].'" class="activate_field" id="status_'.$data['id'].'"/>
                                    </td>';
                    }
                    else
                    {
                        $fields .= '<td>
                                        <img src="style/default/img/status.png" style="cursor: pointer; width: 19px; height: 19px; margin: 2px;" title="'.$langadmin['admin_registration_fields_deactive'].'" class="deactivate_field" id="status_'.$data['id'].'"/>
                                    </td>';
                    }
                }
                else
                {
                    $fields .= '<td></td>';
                }


            }

            if($data['def'] == 'Y')
            {
                $fields .= '<td>
                            <input type="checkbox" style="width: auto;" name="check[]" value="'.$data['name'].'_'.$data['id'].'" disabled="disabled"/>
                        </td>';
                $fields .= '<td>
                                <input type="text" style="width: auto;" value="{-$'.registration_fields_.$data['name'].'}" id="'.$data['id'].'" maxlength="50" class="edit_name name_'.$data['id'].'" disabled="disabled"/>
                            </td>';
            }
            else
            {
                $fields .= '<td>
                                <input type="checkbox" style="width: auto;" name="check[]" value="'.$data['name'].'_'.$data['id'].'"/>
                            </td>';
                $fields .= '<td>
                                <input type="text" style="width: auto;" value="'.$data['name'].'" id="'.$data['id'].'" maxlength="50" class="edit_name"/>
                            </td>';
            }
            if($data['type'] == 'C')
            {
                $fields .= '<td>'.$langadmin['admin_registration_fields_checkbox'].'</td>';
            }
            elseif($data['type'] == 'R')
            {
                $fields .= '<td>'.$langadmin['admin_registration_fields_radio'].'</td>';
            }
            elseif($data['type'] == 'T')
            {
                $fields .= '<td>'.$langadmin['admin_registration_fields_text'].'</td>';
            }
            elseif($data['type'] == 'S')
            {
                $fields .= '<td>'.$langadmin['admin_registration_fields_selection'].'</td>';
            }

            $fields .= '<td>';
            if($data['type'] == 'C' || $data['type'] == 'R' || $data['type'] == 'S')
            {
                for($count = 0; $count <= 4; $count++)
                {
                    $fields .='<input type="text" value="'.$data['value_'.$count].'" id="value_'.$count.'#'.$data['id'].'" class="edit_value"/><br />';
                }
            }
            else
            {
                if($data['edit'] == 'N')
                {
                    $fields .= '<input type="text" value="'.$data['value_0'].'" id="value_0#'.$data['id'].'" class="edit_value" disabled="disabled"/>';
                }
                else
                {
                    $fields .= '<input type="text" value="'.$data['value_0'].'" id="value_0#'.$data['id'].'" class="edit_value"/>';
                }
            }
            $fields .= '</td>';

            if($data['edit'] == 'Y')
            {
                if($data['importance'] == 'M')
                {
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="M" id="M_'.$data['id'].'" checked="checked" style="width: auto;" class="edit_imp"/>
                                    <label for="M_'.$data['id'].'">'.$langadmin['admin_registration_fields_mandatory'].'</label>
                                </td>';
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="O" id="O_'.$data['id'].'" style="width: auto;" class="edit_imp"/>
                                    <label for="O_'.$data['id'].'">'.$langadmin['admin_registration_fields_optional'].'</label>
                                </td>';
                }
                else
                {
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="M" id="M_'.$data['id'].'" style="width: auto;" class="edit_imp"/>
                                    <label for="M_'.$data['id'].'">'.$langadmin['admin_registration_fields_mandatory'].'</label>
                                </td>';
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="O" id="O_'.$data['id'].'" checked="checked" style="width: auto;" class="edit_imp"/>
                                    <label for="O_'.$data['id'].'">'.$langadmin['admin_registration_fields_optional'].'</label>
                                </td>';
                }
            }
            else
            {
                if($data['importance'] == 'M')
                {
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="M" id="M_'.$data['id'].'" checked="checked" style="width: auto;" class="edit_imp" disabled="disabled"/>
                                    <label for="M_'.$data['id'].'">'.$langadmin['admin_registration_fields_mandatory'].'</label>
                                </td>';
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="O" id="O_'.$data['id'].'" style="width: auto;" class="edit_imp" disabled="disabled"/>
                                    <label for="O_'.$data['id'].'">'.$langadmin['admin_registration_fields_optional'].'</label>
                                </td>';
                }
                else
                {
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="M" id="M_'.$data['id'].'" style="width: auto;" class="edit_imp" disabled="disabled"/>
                                    <label for="M_'.$data['id'].'">'.$langadmin['admin_registration_fields_mandatory'].'</label>
                                </td>';
                    $fields .= '<td>
                                    <input type="radio" name="importance_'.$data['id'].'" value="O" id="O_'.$data['id'].'" checked="checked" style="width: auto;" class="edit_imp" disabled="disabled"/>
                                    <label for="O_'.$data['id'].'">'.$langadmin['admin_registration_fields_optional'].'</label>
                                </td>';
                }
            }

            $count++;
        }
        echo $fields;
        exit;
    }
    if($_GET['do'] == 'edit_name')
    {
        $newName = $_GET['name'];
        $id = $_GET['id'];
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET name = '".mysql_real_escape_string($newName)."' WHERE id = '".mysql_real_escape_string($id)."'");
        echo $name;
        exit;
    }
    if($_GET['do'] == 'edit_value')
    {                                                             
        $newValue = $_GET['name'];
        $data = $_GET['data'];
        $data = explode('#', $data);
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET ".mysql_real_escape_string($data[0])." = '".mysql_real_escape_string($newValue)."' WHERE id = '".mysql_real_escape_string($data[1])."'");
        echo $name;
        exit;                  
    }
    if($_GET['do'] == 'edit_imp')
    {
        $id = $_GET['id'];
        $idInfo = explode('_', $id);
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET importance = '".mysql_real_escape_string($idInfo[0])."' WHERE id = '".mysql_real_escape_string($idInfo[1])."'");
        exit;
    }
    if($_GET['do'] == 'del_field')
    {
        $data = $_GET['data'];
        $data = explode('_', $data);
        $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE id = '".mysql_real_escape_string($data[1])."'");
        $cunity->getDb()->query("ALTER TABLE  ".$cunity->getConfig("db_prefix")."users_details DROP `".mysql_real_escape_string($data[0])."`");
        exit;
    }
    if($_GET['do'] == 'activate_field')
    {
        $id = $_GET['id'];
        $id_array = explode('_',$id);
        $id = $id_array[1];
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET active = 'Y' WHERE id = '".mysql_real_escape_string($id)."'");
        exit;
    }
    if($_GET['do'] == 'deactivate_field'){
        $id = $_GET['id'];
        $id_array = explode('_',$id);
        $id = $id_array[1];
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET active = 'N' WHERE id = '".mysql_real_escape_string($id)."'");
        if($cunity->getSetting("user_name")=="full_name"&&$id_array[2]=="firstname"||$id_array[2]=="lastname")
        	$cunity->updateSetting("user_name","nickname");
        exit;
    }
    if($_GET['do'] == 'change_age')
    {
        $age = $_GET['age'];
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($age)."' WHERE name = 'register_age'");
        exit;
    }

?>