<?php
$tplEngine->Template('registration_fields');

    if(isset($_POST['del']))
    {
        $checks = $_POST['check'];
        foreach($checks AS $data){
            $data = explode('_',$data);
            $data=$cunity->getDb()->query_assoc("SELECT def FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE id = '".mysql_real_escape_string($data[1])."' LIMIT 1");
            if($data['def']=="N"){
            	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE id = '".mysql_real_escape_string($data[1])."'");
            	$cunity->getDb()->query("ALTER TABLE  ".$cunity->getConfig("db_prefix")."users_details DROP `".mysql_real_escape_string($data[0])."`");
            }            
        }
        header("location: registration.php?c=fields");
        exit;
    }
    if(isset($_POST['M']))
    {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        $checks = $_POST['check'];
        foreach($checks AS $data)
        {
            $data = explode('_',$data);
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET importance = 'M' WHERE id = '".mysql_real_escape_string($data[1])."'");
        }
        
        header("location: registration.php?c=fields");
        exit;
    }
    if(isset($_POST['O']))
    {
        $checks = $_POST['check'];
        foreach($checks AS $data)
        {
            $data = explode('_',$data);
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET importance = 'O' WHERE id = '".mysql_real_escape_string($data[1])."'");
        }
        header("location: registration.php?c=fields");
        exit;
    }
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."registration_fields ORDER BY id DESC");
    $count = 0;
    $fields = "";
    if(mysql_num_rows($res) == '0')
    {
        $fields = '<tr class="row_0"><td colspan="5" style="text-align: center;">'.$langadmin['admin_registration_fields_fields_no_fields'].'</td></tr>';
    }
    else
    {
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
                                        <img src="style/default/img/status-busy.png" style="cursor: pointer; width: 19px; height: 19px; margin: 2px;" title="'.$langadmin['admin_registration_fields_active'].'" class="activate_field" id="status_'.$data['id'].'_'.$data['name'].'"/>
                                    </td>';
                    }
                    else
                    {
                        $fields .= '<td>
                                        <img src="style/default/img/status.png" style="cursor: pointer; width: 19px; height: 19px; margin: 2px;" title="'.$langadmin['admin_registration_fields_deactive'].'" class="deactivate_field" id="status_'.$data['id'].'_'.$data['name'].'"/>
                                    </td>';
                    }
                }
                else
                {
                    $fields .= '<td></td>';
                }


            }

            if($data['edit'] == 'N')
            {
                $fields .= '<td></td><td>
                                <input type="text" style="width: auto;" value="'.$langadmin['registration_fields_'.$data['name']].'" id="'.$data['id'].'" maxlength="50" class="edit_name name_'.$data['id'].'" disabled="disabled"/>
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
                if($data['edit'] != 'N')
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
                $fields .= '<td></td><td></td>';
            }                
    
            $count++;
        }       
    }

    $tplEngine->Assign('FIELDS', $fields);
    $tplEngine->Assign('AGE',$cunity->getSetting('register_age'));?>