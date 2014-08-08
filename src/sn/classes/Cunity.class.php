<?php

require_once 'Cunity_Friends.class.php';

require_once 'Cunity_Database.class.php';

require_once 'Cunity_Notifications.class.php';

require_once 'Cunity_Security.class.php';

require_once 'Cunity_Mailer.class.php';

require_once 'Cunity_Template_Engine.class.php';

class Cunity {

    private $configData = array();
    private $settings = array();
    private $lang = array();
    private $modules = array();
    private $active = array();
    private $db = null;
    private $notifier = null;
    private $mailer = null;
    private $friender = null;
    private $saver = null;
    private $tplEngine = null;
    private $admin = false;

    public function Cunity($admin = false) {
        
        date_default_timezone_set(@date_default_timezone_get());
        
        $this->admin = $admin;
        $this->readConfigData();
        $this->saver = new Cunity_Security($this);
        $this->saver->checkInstallation();
        $this->db = new Cunity_Database($this->getConfig());        
        $this->readCunitySettings();
        $this->readLanguage();
        $this->checkModules();
        $this->mailer = new Cunity_Mailer($this);
        $this->notifier = new Cunity_Notifications($this);
        $this->friender = new Cunity_Friends($this);
        $this->tplEngine = new Cunity_Template_Engine($this);
    }

    public function getDb() {
        return $this->db;
    }

    public function getFriender() {
        return $this->friender;
    }

    public function getNotifier() {
        return $this->notifier;
    }

    public function getSaver() {
        return $this->saver;
    }

    public function getLang($key = "") {
        return ($key == "") ? $this->lang : $this->lang[$key];
    }

    public function getMailer() {
        return $this->mailer;
    }

    public function getTemplateEngine() {
        return $this->tplEngine;
    }

    public function getModule($modulename) {
        return $this->modules[$modulename];
    }

    public function getConfig($name = "") {
        if ($name == "")
            return $this->configData;
        else
            return $this->configData[$name];
    }

    public function getActiveModules() {
        return $this->modules;
    }

    public function getSetting($name = "") {
        if ($name == "")
            return $this->settings;
        else
            return $this->settings[$name];
    }

    public function getCurrentFile() {
        $finfo = pathinfo($_SERVER['PHP_SELF']);
        return $finfo['filename'];
    }

    public function isCurrentModule($name) {
        return $this->active['ACTIVE_' . strtoupper($name)];
    }

    public function isAdmin() {
        return $this->admin;
    }

    public function getUserData($userid) {
        $res = $this->db->query("SELECT u.*,d.* FROM " . $this->configData['db_prefix'] . "users AS u, " . $this->configData['db_prefix'] . "users_details AS d WHERE u.userid = '" . $userid . "' AND u.userid=d.userid");
        $data = mysql_fetch_assoc($res);
        return $data;
    }

    public function getCunityUrl($cunityId) {
        $res = $this->db->query("SELECT cunityUrl FROM " . $this->configData['db_prefix'] . "connected_cunities WHERE cunityId = " . (int) $cunityId);
        $data = mysql_fetch_assoc($res);
        return $data['cunityUrl'];
    }

    public function getcunityId() {
        $result = $this->db->query("SELECT `value` FROM " . $this->configData["db_prefix"] . "open".$this->configData["db_prefix"]."settings WHERE setting = 'cunityId'");
        if (mysql_num_rows($result) == 0)
            return 0;
        $data = mysql_fetch_assoc($result);
        return $data['value'];
    }

    private function readLanguage($language = "") {
        if ($this->saver->login() && $language == "") {
            $res = $this->db->query("SELECT lang FROM " . $this->getConfig("db_prefix") . "users WHERE userid = '" . $_SESSION['userid'] . "'");
            $d = mysql_fetch_assoc($res);
            if ($d['lang'] != NULL) {
                $_SESSION['language'] = $d['lang'];
            } else {
                $_SESSION['language'] = $this->getSetting('language');
            }
        } elseif (!isset($_SESSION['language']) && $language == "") {
            $_SESSION['language'] = $this->getSetting('language');
        } elseif ($language != "") {
            $_SESSION['language'] = $language;
        }
        if ($this->admin)
            $path = $_SESSION['cunity_trunk_folder'] . '/admin';
        else
            $path = $_SESSION['cunity_trunk_folder'];
        $langinfo = simplexml_load_file($path . '/languages/' . $_SESSION['language'] . '/lang_info.xml');

        $_SESSION['date']['php']['date'] = $this->getXMLValueViaAttribute($langinfo, 'date', 'name', 'php');
        $_SESSION['date']['php']['time'] = $this->getXMLValueViaAttribute($langinfo, 'time', 'name', 'php');
        $_SESSION['date']['php']['date_time'] = $this->getXMLValueViaAttribute($langinfo, 'datetime', 'name', 'php');
        $_SESSION['date']['mysql']['date'] = $this->getXMLValueViaAttribute($langinfo, 'date', 'name', 'mysql');
        $_SESSION['date']['mysql']['time'] = $this->getXMLValueViaAttribute($langinfo, 'time', 'name', 'mysql');
        $_SESSION['date']['mysql']['date_time'] = $this->getXMLValueViaAttribute($langinfo, 'datetime', 'name', 'mysql');
        $_SESSION['date']['js']['date'] = $this->getXMLValueViaAttribute($langinfo, 'date', 'name', 'js');

        require $path . '/languages/' . $_SESSION['language'] . '/' . $langinfo->children()->file;

        $this->lang = $lang;
    }

    private function readConfigData() {
        require($_SESSION['cunity_trunk_folder'] . '/config.php');
        if (!isset($cunityConfig))
            $cunityConfig = array("db_host" => $db_host, "db_user" => $db_user, "db_pass" => $db_pass, "db_name" => $db_name, "db_prefix" => $db_prefix, "smtp_port" => $smtp_port, "smtp_host" => $smtp_host, "smtp_username" => $smtp_username, "smtp_password" => $smtp_password, "smtp_method" => $smtp_method, "smtp_auth" => $smtp_auth, "smtp_sender_address" => $smtp_sender_address, "smtp_sender_name" => $smtp_sender_name, "email_header" => $email_header, "email_footer" => $email_footer, "cunity_installed" => $cunity_installed, "error_reporting" => $error_reporting);
        $this->configData = $cunityConfig;
        return true;
    }

    public function getCunityMainMenu() {
        $menu = "";
        $res = $this->db->query("SELECT * FROM " . $this->configData["db_prefix"] . "menu ORDER BY menu_position");
        while ($data = mysql_fetch_assoc($res)) {
            $name = ($data['def'] == 1) ? $this->lang['menu_' . $data['name']] : $data['name'];
            $class = (($this->getCurrentFile() . ".php") == $data['target']) ? "active" : "";
            $data['icon'] = str_replace('[STYLE]', $_SESSION['style'], $data['icon']);
            $menu .= $this->tplEngine->createTemplate('menuEntry', array(
                "ICON" => $data['icon'],
                "NAME" => $name,
                "TARGET" => $data['target'],
                "ID" => $data['name'],
                "CLASS" => $class
            ));
        }
        return $menu;
    }

    private function readCunitySettings() {
        $this->settings = array();
        $data = array();
        $res = $this->db->query("SELECT * FROM " . $this->configData["db_prefix"] . "settings");
        while ($rdata = mysql_fetch_assoc($res)) {
            $data[$rdata["name"]] = htmlspecialchars_decode($rdata["value"]);
        }
        $this->settings = $data;
        return true;
    }

    public function refreshConfigData() {
        $this->readConfigData();
    }

    public function updateSetting($name, $value) {
        return $this->db->query("UPDATE " . $this->configData["db_prefix"] . "settings SET `value`='" . $value . "' WHERE name = '" . $name . "'");
    }

    public function setLang($lang) {
        $this->readLanguage($lang);
    }

    public function refreshSettings() {
        $this->readCunitySettings();
    }

    public function checkModules() {
        $res = $this->db->query("SELECT * FROM " . $this->configData["db_prefix"] . "modules");
        $active = array();
        while ($data = mysql_fetch_assoc($res)) {
            if ($data['power'] == 1 && $this->saver->login()) {
                $this->modules[$data['name']] = true;
                define($data['name'],true);
            } else {
                $this->modules[$data['name']] = false;
                define($data['name'],false);
            }

            if ($data['name'] == $this->getCurrentFile())
                $active['ACTIVE_' . strtoupper($data['name'])] = 'class="active"';
            else
                $active['ACTIVE_' . strtoupper($data['name'])] = 'class=""';
        }
        if ($this->getCurrentFile() == 'profile' && isset($_GET['user']) && ($_GET['user'] == "" || $_GET['user'] == $_SESSION['userhash']))
            $active['ACTIVE_PROFILE'] = 'class="active"';
        else
            $active['ACTIVE_PROFILE'] = 'class=""';
        $this->active = $active;
    }

    public function returnJson(array $data) {
        return json_encode($data);
    }

    function getXMLValueViaAttribute($xml, $elName, $attr, $key) {
        foreach ($xml->children() AS $a) {
            if ($a->getName() == $elName && $a[$attr] == $key) {
                return (String) $a;
            }
        }
        return false;
    }

}

?>