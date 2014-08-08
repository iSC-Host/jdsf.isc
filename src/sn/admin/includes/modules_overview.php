<?php
$tplEngine->Template('modules_overview');

$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."modules");
$modules = "";
while($data = mysql_fetch_assoc($res))
{
    if($data['power'] == 1)
    {
        $modules .= '
            <tr><td>            
                <div class="cell_name" id="'.$data['id'].'">'.$langadmin['admin_modules_'.$data['name']].'</div>
                <label for="'.$data['id'].'_1_'.$data['name'].'">
                <div class="cell_on" id="'.$data['id'].'_on">
                    <input type="radio" name="'.$data['id'].'" checked="checked" id="'.$data['id'].'_1_'.$data['name'].'" style="width: auto;" class="on"/>
                    '.$langadmin['admin_modules_on'].'                
                </div>
                </label>
                <label for="'.$data['id'].'_0_'.$data['name'].'">
                <div class="cell_none" id="'.$data['id'].'_off">
                    <input type="radio" name="'.$data['id'].'" id="'.$data['id'].'_0_'.$data['name'].'" style="width: auto;" class="off"/>
                    '.$langadmin['admin_modules_off'].'
                </div>
                </label>
                <div class="clear"></div>
            </td></tr>';
    }
    else
    {
        $modules .= '        
            <tr><td>
                <div class="cell_name" id="'.$data['id'].'">'.$langadmin['admin_modules_'.$data['name']].'</div>
                <label for="'.$data['id'].'_1_'.$data['name'].'">
                <div class="cell_none" id="'.$data['id'].'_on">
                    <input type="radio" name="'.$data['id'].'" id="'.$data['id'].'_1_'.$data['name'].'" style="width: auto;" class="on"/>
                    '.$langadmin['admin_modules_on'].'
                </div>
                </label>
                <label for="'.$data['id'].'_0_'.$data['name'].'">
                <div class="cell_off" id="'.$data['id'].'_off">
                    <input type="radio" name="'.$data['id'].'" checked="checked" id="'.$data['id'].'_0_'.$data['name'].'" style="width: auto;" class="off"/>
                    '.$langadmin['admin_modules_off'].'
                </div>
                </label>
                <div class="clear"></div></td></tr>';
    }    
}

$tplEngine->Assign('LIST', $modules);
?>