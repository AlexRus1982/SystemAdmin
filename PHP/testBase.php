<?php

    $db_host = 'localhost';
    $db_username = 'root';
    $db_password = '';
    $db_name = 'apkshki';
    $db_charset = 'utf8';

	$is_connected = mysqli_connect($db_host, $db_username, $db_password);
	$is_db_selected = $is_connected ? mysqli_select_db($is_connected, $db_name) : FALSE; 

	if (!$is_connected){
        echo 'Не могу соединиться с базой данных !!!!';
    }
        
	if (!$is_db_selected) {
        echo 'Не могу найти базу данных !!!';
    } 


    if ($is_connected AND $is_db_selected){
        $sql = 'SELECT main_image, name, downloads, activity FROM `apkshki_full_base_txt`';
        
        $result = mysqli_query($is_connected, $sql);

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $rowsCount = mysqli_num_rows($result);

        for($i = 0; $i < 20; $i++)
        {
            $row = $rows[$i];
            echo '<div class="bodyOfListApplication__item">';
            echo '  <div class="bodyOfListApplication__item__image"><img src="'.$row['main_image'].'" width="90" height="90"></div>';
            echo '  <div class="bodyOfListApplication__item__name">'.$row['name'].'</div>';
            echo '  <div class="bodyOfListApplication__item__downloads">'.$row['downloads'].'</div>';
            echo '  <div class="bodyOfListApplication__item__activity">';
            echo '      <div class="button__on__off"></div>';
            echo '      <div class="button__on__off__text">'.$row['activity'].'</div>';
            echo '      <div class="button__on__off__turn"></div>';
            echo '  </div>';
            echo '  <div class="bodyOfListApplication__item__delete"></div>';
            echo '</div>';
        }
    }
?>
