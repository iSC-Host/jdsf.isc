<?php
    if($_SESSION['language'] == 'en'){
        $english = 'selected="selected"';
        $german = "";
    }else{
        $german = 'selected="selected"';
        $english = "";
    }
	$tplEngine->Template('overall_footer');
	$tplEngine->Assign('LANG', $_SESSION['language']);
	$tplEngine->Assign('ENGLISH', $english);
    $tplEngine->Assign('GERMAN', $german);
	$tplEngine->Assign('VERSION', $cunity->getSetting('version'));
	$tplEngine->Assign('JSCRIPT_BOTTOM', '<script type="text/javascript" src="includes/javascript/jscript_bottom.js.php"></script>');
	$tplEngine->show();
	unset($tplEngine);
	unset($db);
?>