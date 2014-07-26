<?php
//SCRIPT INSTALLAJAX.PHP
$warning_script = "Sie k&ouml;nnen dieses Skript nicht direkt ausf&uuml;hren!";
$warning_installed = "Cunity ist bereits installiert. Sie k&ouml;nnen dieses Skript nicht mehr ausf&uuml;hren!";
$warning_missing = "Die Verzeichnisstruktur von Cunity ist besch&auml;digt!";
$warning_db = "Keine Verbindung zur Datenbank!";
$connection_ok = "Datenbank-Verbindung OK!";
$connection_failed = "Keine Verbindung zur Datenbank: ";
$automatic_msg = "Dies ist eine automatische Nachricht der Website:";
$greeting = "Gr&uuml;&szlig;e,";
$status_dir = "Es gab einen Fehler beim &Ouml;ffnen des Verzeichnisses";
$status_db1 = "Datenbank erfolgreich importiert!";
$status_db2 = "Datenbank konnte NICHT importiert werden!";
$status_db3 = "Es gab einen Fehler beim Schreiben der Datenbank!";
$status_db4 = "Datenbankeintrag erfolgreich!";
$status_config1 = "Die Datei config.php ist besch&auml;digt. Bitte reparieren Sie die config.php!";
$status_config2 = "Datei config.php erfolgreich aktualisiert!";
$status_nick = "Der Nickname oder die E-Mail addresse existiert schon. Bitte legen Sie nicht 2 Besitzerkonten an!";
$status_folder1 = "Das Verzeichnis konnte nicht erstellt werden!";
$status_folder2 = "Neues Verzeichnis erstellt:";
$test_email1 = "Dies ist eine Test-E-Mail von dem Cunity, welches Sie gerade erstellen!";
$test_email2 = "Cunity Installation Test-E-Mail";
$test_email3 = "Eine E-Mail wurde gesendet an";
$test_email4 = "Das Abschicken der E-mail an";
$test_email5 = "schlug fehl wegen:";
$status_folder3 = "Auf das Verzeichnis kann zugegriffen werden. Wenn Sie auf &quot;Weiter&quot; klicken, wird der Verzeichnispfad in der Datenbank gespeichert werden!";
$status_folder4 = "Das Verzeichnis existiert nicht. Bitte erstellen Sie zun&auml;chst ein Verzeichnis!";
$status_folder5 = "Das Verzeichnis wurde in die Datenbank geschrieben!";
$status_folder6 = "Das Verzeichnis existiert nicht!";
$status_folder7 = "Das neue Verzeichnis ist nicht beschreibbar. Bitte &auml;ndern Sie die Rechte f&uuml;r:";
$summary1 = "Zusammenfassung der Cunity Installation";
$status_error = "Es gab einen Fehler:";
$status_email1 = "Eine E-Mail wurde gesendet an";
$status_email2 = "Das Abschicken der E-mail an";
$status_email3 = "schlug fehl wegen:";
$config_error1 = "Das Programm hat keine Berechtigung die config.php zu beschreiben obwohl die Zugriffsberechtigung auf 0664 ge&auml;ndert wurde!";
$config_error2 = "Das Programm kann nicht die config.php beschreiben und kann auch nicht die Zugriffsrechte &auml;ndern.";
$config_error3 = "Die Datei config.php existiert nicht. Sie sollte im Installationsverzeichnis liegen. Bitte &uuml;berpr&uuml;fen Sie das Installationsverzeichnis!";

//SCRIPT SECURECUNITY.PHP
$secure_sec1 = 'Ihr Cunity ist bereits gesichert (installajax.php und install-cunity.php gel&ouml;scht)!';
$secure_sec2 = 'Ihr Cunity wurde erfolgreich gesichert (installajax.php und / oder install-cunity.php gel&ouml;scht)!';
$secure_sec3 = 'Ihr Cunity konnte NICHT komplett gesichert werden. Bitte l&ouml;schen Sie installajax.php selbst!';
$secure_sec4 = 'Ihr Cunity konnte NICHT komplett gesichert werden. Bitte l&ouml;schen Sie install-cunity.php selbst!';
$secure_sec5 = 'Ihr Cunity konnte NICHT gesichert werden. Bitte l&ouml;schen Sie install-cunity.php und installajax.php selbst!';
$secure_sec6 = 'Ihre Cunity Verzeichnisse auf Ihrem Linux Sytem konnten nicht komplett gesch&uuml;tzt werden! Bitte nehmen Sie dies manuell vor!';
$secure_sec7 = 'Die Cunity Verzeichnisse, auf die nicht zugegriffen werden darf, wurden mit 0644 gesichert!';
?>