<?php
class Cunity_MySQL_Sync {

    private $cunity = null;
    private $mysql = null;
    private $currentTables = array();
    private $mustTables = array();

    public function Cunity_MySQL_Sync(Cunity $cunity){
        $this->cunity = $cunity;
        $this->mysql = $this->cunity->getDb();
    }
    
    public function sync($filename){
        if(file_exists($filename)){
            $file = file_get_contents($filename);
            $this->createTemporaryTables($file);
            $this->loadTables();
            $this->syncTables();
            $this->syncFields();
        }
    }
    
    private function syncFields(){
        foreach($this->currentTables AS $table => $entries){
            $columnsMust=$this->mysql->query("SHOW COLUMNS FROM cunityupdatetemp_".$table);
            $columnsCurrent=$this->mysql->query("SHOW COLUMNS FROM ".$this->cunity->getConfig("db_prefix").$table);

            while($ct=mysql_fetch_assoc($columnsCurrent))
                $currentColumns[$ct['Field']] = $ct['Type'].','.$ct['Null'].','.$ct['Key'].',DEFAULT '.$ct['Default'].','.$ct['Extra'];

            while($mt=mysql_fetch_assoc($columnsMust))
                $mustColumns[$mt['Field']] = $mt['Type'].','.$mt['Null'].','.$mt['Key'].',DEFAULT '.$mt['Default'].','.$mt['Extra'];

            $differencesAdd=array_diff_key($mustColumns,$currentColumns);

            foreach($differencesAdd AS $field => $type)
                if($this->createNewField($table,$field,$type))
                    $currentColumns[$field] = $type;

            $differencesRemove=array_diff_key($currentColumns,$mustColumns);

            foreach($differencesRemove AS $field => $type)
                if($this->removeField($table,$field))
                    unset($currentColumns[$field]);

            if(count($currentColumns)!=count($mustColumns))
                die("Error Updating structure in '".$this->cunity->getConfig("db_prefix").$table."'");

            $structureDiff=array_diff($mustColumns,$currentColumns);

            foreach($structureDiff AS $field => $values)
                $this->alterTableField($table,$field,$values);

            if($entries < $mustTables[$table]){
                $res = $this->mysql->query("INSERT IGNORE INTO ".$this->cunity->getConfig("db_prefix").$table." SELECT * FROM cunityupdatetemp_".$table);
                if(!$res)
                    die("Error Updating entries in '".$this->cunity->getConfig("db_prefix").$table."'");
            }
        }
    }
    
    private function syncTables(){
        $differencesAdd=array_diff_key($mustTables,$currentTables);
        foreach($differencesAdd AS $table => $entries)
            if($this->createNewTable($table, $entries))
                $this->currentTables[$table] = $entries;

        $differencesRemove=array_diff_key($currentTables,$mustTables);

        foreach($differencesRemove AS $table => $entries)
            if($this->removeTable($table, $entries))
                unset($this->currentTables[$table]);

        if(count($this->currentTables)!=count($this->mustTables))
            die("Error Updating tables in '".$this->cunity->getConfig("db_name")."'");
            
        foreach($this->mustTables AS $tablename=>$entries){
            $res = $this->mysql->query("DROP TABLE cunityupdatetemp_".$tablename);
            if(!$res)
                die("Error-Drop cunityupdatetemp Tables! Please delete all tables with cunityupdatetemp_ prefix!");
        }
    }
    
    private function loadTables(){
        $crTables=$this->mysql->query("SHOW TABLES FROM ".$this->cunity->getConfig("db_name")." WHERE Tables_in_".$this->cunity->getConfig("db_name")." LIKE '".substr($cunity->getConfig("db_prefix"),0,-1)."\_%'");
        $mtTables=$this->mysql->query("SHOW TABLES FROM ".$this->cunity->getConfig("db_name")." WHERE Tables_in_".$this->cunity->getConfig("db_name")." LIKE 'cunityupdatetemp\_%'");
        if(mysql_num_rows($mtTables)==0)
            die("Error! Update-tables missing!");
        while($ct=mysql_fetch_row($crTables)){
            $cra=explode('_',$ct[0],2);
            $r =$this->mysql->query("SELECT * FROM ".$ct[0]);
            $this->currentTables[$cra[1]] = mysql_num_rows($r);
        }

        while($mt=mysql_fetch_row($mtTables)){
            $mta=explode('_',$mt[0],2);
            $r1 =$this->mysql->query("SELECT * FROM ".$mt[0]);
            $this->mustTables[$mta[1]] = mysql_num_rows($r1);
        }
    }
    
    private function createTemporaryTables($dumpString){
        $sql_commands = explode(';',$dumpString);
        for($i=0;$i<count($sql_commands)-1; $i++){
            $sql_commands[$i]=str_replace('cunity_', 'cunityupdatetemp_', $sql_commands[$i]);
            $this->mysql->query($sql_commands[$i]) or die(mysql_error());
        }
    }
    
    private function createNewTable($tableName, $entries){
        $res = $this->mysql->query("CREATE TABLE ".$this->cunity->getConfig("db_prefix").$tableName." LIKE cunityupdatetemp_".$tableName) or die(mysql_error());
        if($entries>0&&$res)
            $res = $this->mysql->query("INSERT INTO ".$this->cunity->getConfig("db_prefix").$tableName." SELECT * FROM cunityupdatetemp_".$tableName) or die(mysql_error());
        return $res;
    }

    private function removeTable($tableName, $entries){
        if($entries>0) return true;
        if(strpos($tableName,'view_')===false)
            return $this->mysql->query("DROP TABLE ".$this->cunity->getConfig("db_prefix").$tableName);
        else
            return $this->mysql->query("DROP VIEW ".$this->cunity->getConfig("db_prefix").$tableName);
    }

    private function createNewField($table,array $data){
        $q = "ALTER TABLE ".$this->cunity->getConfig("db_prefix").$table." ADD `".$data['name']."` ".strtoupper($data['type']);
        return $this->mysql->query($q);

    }

    private function removeField($table,$name){
        if($table == 'users_details') return true;
        return $this->mysql->query("ALTER TABLE ".$this->cunity->getConfig("db_prefix").$table." DROP `".$name."`");
    }

    private function alterTableField($table,$field,$values){
        $values = str_replace(',',' ',$values);
        $res = $this->mysql->query("ALTER TABLE ".$this->cunity->getConfig("db_prefix").$table." MODIFY ".$field." ".$values);
    }
    
}
 ?>