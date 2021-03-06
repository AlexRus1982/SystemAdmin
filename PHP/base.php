<?php

class BaseConnection
{
    private $db_host        = 'localhost';
    private $db_username    = 'root';
    private $db_password    = '';
    private $db_name        = 'apkshki';
    private $db_work        = 'apkshki_full_base_txt2';
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
    public function getListApp(/*$pageNumber, $pageFilter*/) {
        $json = array(
            'maxRecords'        => 0,
            'filteredRecords'   => 0,
            'html'              => ''
        );

        $pageNumber = $_GET['pageNumber'];
        $sql = 'SELECT id, main_image, name, downloads, activity FROM `' . $this->db_work . '`';
        $result = $this->getQuery($sql);
        $rowsCount = mysqli_num_rows($result);
        $json['maxRecords'] = $rowsCount;
        
        $pageFilter = $_GET['pageFilter'];
        if ($pageFilter){
            if ($pageFilter == 'games'){
                $sql .= ' WHERE category LIKE "????????>%"';
            }
            else if ($pageFilter == 'apps'){
                $sql .= ' WHERE category LIKE "????????????????????>%"';
            }
            else if ($pageFilter == 'popular'){
                $sql .= ' ORDER BY downloads DESC';
            }
            else if (strlen($pageFilter) > 0 and $pageFilter != 'all'){
                $sql .= ' WHERE category = "' . $pageFilter . '"';
            }
            $result = $this->getQuery($sql);
            $rowsCount = mysqli_num_rows($result);

            $json['filteredRecords'] = $rowsCount;
        }
    
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $html = '';
    
        for ($i = ($pageNumber - 1) * 20; ($i < $pageNumber * 20 && $i < $rowsCount); $i++) {
            $row = $rows[$i];
            $html .= '<div class="bodyOfListApplication__item" id="' . $row['id']  .'">';
            $html .= '  <div class="bodyOfListApplication__item__image"><img src="' . $row['main_image'] . '" width="90" height="90"></div>';
            $html .= '  <div class="bodyOfListApplication__item__name">' . $row['name'] . '</div>';
            $html .= '  <div class="bodyOfListApplication__item__downloads">' . $row['downloads'] . '</div>';
            $html .= '  <div class="bodyOfListApplication__item__activity">';
            $html .= '      <div class="button__on__off"></div>';
            $html .= '      <div class="button__on__off__text">ON OFF</div>';
            $html .= '      <div class="button__on__off__turn ' . (($row['activity'] == '1') ? 'button__on__off__turn_on' : 'button__on__off__turn_off') . '"></div>';
            $html .= '  </div>';
            $html .= '  <div class="bodyOfListApplication__item__delete"></div>';
            $html .= '</div>';
        }

        $json['html'] = $html;

        echo json_encode($json);
    }

    public function setActivityApp(/*$appId, $appActivity*/) {
        $appId = $_GET['appId'];
        $appActivity = $_GET['appActivity'];

        $sql = 'UPDATE `' . $this->db_work . '` SET activity = ' . $appActivity . ' WHERE id = ' . $appId;

        $result = $this->getQuery($sql);
    }

    public function deleteApp(/*$appId*/) {
        $appId = $_GET['appId'];

        $sql = 'DELETE FROM `' . $this->db_work . '` WHERE id = ' . $appId;

        $result = $this->getQuery($sql);
    }

    public function getAppInfo(/*$appId*/) {
        $appId = $_GET['appId'];

        $sql = 'SELECT * FROM `' . $this->db_work . '` WHERE id = ' . $appId;

        $result = $this->getQuery($sql);

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        echo json_encode($rows[0]);
    }


    public function getGameList() {
        $sql = 'SELECT category FROM `apkshki_game_category`';

        $result = $this->getQuery($sql);

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $rowsCount = mysqli_num_rows($result);
    
        $resultList = array();
        for ($i = 0; $i < $rowsCount; $i++) {
            $row = $rows[$i];
            $resultList[$i] = $row['category'];
        }

        echo json_encode($resultList);
    }

    public function getAppList() {
        $sql = 'SELECT category FROM `apkshki_app_category`';

        $result = $this->getQuery($sql);

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $rowsCount = mysqli_num_rows($result);
    
        $resultList = array();
        for ($i = 0; $i < $rowsCount; $i++) {
            $row = $rows[$i];
            $resultList[$i] = $row['category'];
        }

        echo json_encode($resultList);
    }


}

$method = $_GET['method'];

$baseConnection = new BaseConnection('localhost', 'root', '',  'apkshki');
if ($baseConnection->connectBase()) {
    $baseConnection->$method();
    $baseConnection->closeBase();
}