<?php
if(isset($_POST['save']))
{  
    $resTerms = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."pages WHERE slug = 'terms' LIMIT 1"));                
    $resImprint = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."pages WHERE slug = 'imprint' LIMIT 1"));
    $resPrivacy = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."pages WHERE slug = 'privacy' LIMIT 1"));
    if($resTerms['COUNT(*)'] == 1)
	   $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."pages SET title='".mysql_real_escape_string($_POST['terms_title'])."', text='".mysql_real_escape_string(htmlentities($_POST['terms']))."' WHERE slug='terms'");
	else
	   $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."pages (slug, title, text) VALUES ('terms', '".mysql_real_escape_string($_POST['terms_title'])."', '".mysql_real_escape_string(htmlentities($_POST['terms']))."')");
    if($resImprint['COUNT(*)'] == 1)
	   $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."pages SET title='".mysql_real_escape_string($_POST['imprint_title'])."', text='".mysql_real_escape_string(htmlentities($_POST['imprint']))."' WHERE slug='imprint'");
	else
	   $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."pages (slug, title, text) VALUES ('imprint', '".mysql_real_escape_string($_POST['imprint_title'])."', '".mysql_real_escape_string(htmlentities($_POST['imprint']))."')");
    if($resPrivacy['COUNT(*)'] == 1)
	   $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."pages SET title='".mysql_real_escape_string($_POST['privacy_title'])."', text='".mysql_real_escape_string(htmlentities($_POST['privacy']))."' WHERE slug='privacy'");
    else
       $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."pages (slug, title, text) VALUES ('privacy', '".mysql_real_escape_string($_POST['privacy_title'])."', '".mysql_real_escape_string(htmlentities($_POST['privacy']))."')");
}

$tplEngine->Template('settings_pages');

$res = $cunity->getDb()->query("SELECT title, text FROM ".$cunity->getConfig("db_prefix")."pages WHERE slug='terms'");
$row = mysql_fetch_assoc($res);
$tplEngine->Assign('terms', $row['text']);
$tplEngine->Assign('terms_title', $row['title']);

$res = $cunity->getDb()->query("SELECT title, text FROM ".$cunity->getConfig("db_prefix")."pages WHERE slug='imprint'");
$row = mysql_fetch_assoc($res);
$tplEngine->Assign('imprint', $row['text']);
$tplEngine->Assign('imprint_title', $row['title']);

$res = $cunity->getDb()->query("SELECT title, text FROM ".$cunity->getConfig("db_prefix")."pages WHERE slug='privacy'");
$row = mysql_fetch_assoc($res);
$tplEngine->Assign('privacy', $row['text']);
$tplEngine->Assign('privacy_title', $row['title']);
?>