<?php

 /**
 * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively.
 * Will return an array of the form
 * array(
 *   'files' => [],
 *   'dirs'  => [],
 * )
 * @author sreekumar
 * @param string $root
 * @result array
 */
function read_all_files($root = './pack'){
    $files  = array('files'=>array(), 'dirs'=>array());
    $directories  = array();
    $last_letter  = $root[strlen($root)-1];
    $root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;    
    $directories[]  = $root;
 
    while (sizeof($directories)) {
        $dir  = array_pop($directories);
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                  continue;
                }
                $file  = $dir.$file;
                if (is_dir($file)) {
                  $directory_path = $file.DIRECTORY_SEPARATOR;
                  array_push($directories, $directory_path);
                  $files['dirs'][]  = $directory_path;
                }
                elseif (is_file($file)) {
                  $files['files'][]  = $file;
                }
            }
            closedir($handle);
        }
    }
    return $files;
} 
 
//THIS FUNCTION PUTS ALL FILES IN THE DIRECTORY "PACK" INTO ONE BASE64-FILE
function phppack($packfilename){     
    $dir_structure=read_all_files();
    $base64_output = './'.$packfilename; // THIS IS A TEMPORARY OUTPUT FILE, ALREADY BASE64-ENCODED
    $test=fopen($base64_output,w);
    fclose($test);
    for ($i = 0,$b = sizeof($dir_structure['files']);$i < $b;$i++){
        $dir_structure['files'][$i]=str_replace('\\','/',$dir_structure['files'][$i]);             
        $base64_temp = file_get_contents($dir_structure['files'][$i]); //THIS READS THE ARCHIVE INTO A STRING
        $base64_string = chunk_split(base64_encode ($base64_temp)); //THE STRING IS ENCODED TO BASE64 AND THEN SPLIT INTO SHORT LINES             
        $dir_structure['files'][$i]=str_replace('./pack','',$dir_structure['files'][$i]);
        file_put_contents($base64_output, "\r\n***###FILE~~~".$dir_structure['files'][$i]."\r\n***", FILE_APPEND);  //THE BASE 64 STRING IS WRITTEN TO THE OUTPUT FILE
        file_put_contents($base64_output, $base64_string, FILE_APPEND);  //THE BASE 64 STRING IS WRITTEN TO THE OUTPUT FILE                                       
    }
    $tempfile=file_get_contents($packfilename);
    file_put_contents($packfilename, $tempfile);
}
 
 
function phpunpack($packedfile, $destination){
    $path_file=""; // SET TO NOTHING
    chmod ($packedfile,0744);            
    if(!$testpack=fopen($packedfile,"r")) 
        die ('Cannot open the update-archive!');
    else 
        fclose($testpack);
    
    $file_string = (file_get_contents($packedfile)); //THIS READS THE PACK FILE WITH THE BASE64-FILES INTO A STRING
    $file_array = explode("***", $file_string);
    
    //read out the current directory and switch to the destination
    $origin_dir=getcwd();
    chdir($destination);
    
    //Now loop through the exploded string that contains filenames and base64(files)
    $i=0;
    $findstring="###FILE~~~";            
    while ($i < count($file_array))    
    {
        //echo "<br />count: ".count($file_array);
        $file_array[$i]=trim($file_array[$i]);
   
        if($pos=strpos($file_array[$i], $findstring)!==false){  //IF IT READS A FILENAME IT WRITES IT IN VARIABLES        
             //GET THE FILENAME
             $path_file=substr($file_array[$i], $pos+10);
             $filename=basename($path_file);
             $dirname=dirname($path_file);
        }  
        else if ($path_file!=""){  //IF IT READS A FILE
            $tempfile=base64_decode($file_array[$i]);
            if (!file_exists($dirname)) {
               //echo "<br />Dir: ".$dirname." does not exist";
               mkdir($dirname,0777,true);   
            }
            file_put_contents($path_file, $tempfile);
            $path_file="";
        }
        $i++; 
    }
    chdir($origin_dir);
    return true;
}
?>