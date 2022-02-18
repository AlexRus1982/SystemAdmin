<?php

class BaseConnection
{
    private $db_host        = 'localhost';
    private $db_username    = 'root';
    private $db_password    = '';
    private $db_name        = 'apkshki';
    private $db_charset     = 'utf8';
    private $is_connected   = null;
    
    public function __construct($db_host, $db_username, $db_password, $db_name, $db_charset = 'utf8') {
        $this->db_host      = $db_host;
        $this->db_username  = $db_username;
        $this->db_password  = $db_password;
        $this->db_name      = $db_name;
        $this->db_charset   = $db_charset;
    }

    function __destruct() {
        if (!$this->connectBase()) {
            $this->closeBase();
        }
    }

    public function connectBase() {

        $this->is_connected = mysqli_connect($this->db_host, $this->db_username, $this->db_password);
        $is_db_selected     = $this->is_connected ? mysqli_select_db($this->is_connected, $this->db_name) : false;
        return ($this->is_connected and $is_db_selected);
    }

    public function closeBase() {
        return mysqli_close($this->is_connected);
    }

    public function getQuery($sqlQuery) {
        return mysqli_query($this->is_connected, $sqlQuery);
    }

    //--------------------------- utils section -----------------------------------------------------
    // pageNumber start from 1
    public function getListApp(/*$pageNumber*/) {
        $pageNumber = $_GET['pageNumber'];

        $sql = 'SELECT id, main_image, name, downloads, activity FROM `apkshki_full_base_txt2`';

        $result = $this->getQuery($sql);
    
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $rowsCount = mysqli_num_rows($result);
    
        for ($i = ($pageNumber - 1) * 20; $i < $pageNumber * 20; $i++) {
            $row = $rows[$i];
            echo '<div class="bodyOfListApplication__item" id="' . $row['id']  .'">';
            echo '  <div class="bodyOfListApplication__item__image"><img src="' . $row['main_image'] . '" width="90" height="90"></div>';
            echo '  <div class="bodyOfListApplication__item__name">' . $row['name'] . '</div>';
            echo '  <div class="bodyOfListApplication__item__downloads">' . $row['downloads'] . '</div>';
            echo '  <div class="bodyOfListApplication__item__activity">';
            echo '      <div class="button__on__off"></div>';
            echo '      <div class="button__on__off__text">ON OFF</div>';
            echo '      <div class="button__on__off__turn ' . (($row['activity'] == '1') ? 'button__on__off__turn_on' : 'button__on__off__turn_off') . '"></div>';
            echo '  </div>';
            echo '  <div class="bodyOfListApplication__item__delete"></div>';
            echo '</div>';
        }        
    }

    public function setActivityApp(/*$appId, $appActivity*/) {
        $appId = $_GET['appId'];
        $appActivity = $_GET['appActivity'];

        $sql = 'UPDATE `apkshki_full_base_txt2` SET activity = ' . $appActivity . ' WHERE id = ' . $appId;

        $result = $this->getQuery($sql);
    }

    public function deleteApp(/*$appId*/) {
        $appId = $_GET['appId'];

        $sql = 'DELETE FROM `apkshki_full_base_txt2` WHERE id = ' . $appId;

        $result = $this->getQuery($sql);
    }


}

$method = $_GET['method'];

$baseConnection = new BaseConnection('localhost', 'root', '',  'apkshki');
if ($baseConnection->connectBase()) {
    $baseConnection->$method();
    $baseConnection->closeBase();
}