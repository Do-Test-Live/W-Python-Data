<?php

include('include/dbController.php');
$db_handle = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

if (isset($_GET['page_no'])) {
    $page_no = $db_handle->checkValue($_GET['page_no']);

    $query = "SELECT * FROM `page` where page_no>='$page_no' and date ='$date'";

    $page = $db_handle->runQuery($query);
    $row = $db_handle->numRows($query);

    for($i=0;$i<$row;$i++){

        $page_id=$row[$i]['id'];

        $delete = $db_handle->insertQuery("DELETE FROM `page` WHERE id='$page_id'");

        $delete = $db_handle->insertQuery("DELETE FROM `racevalue` WHERE date ='$date' and page_id='$page_id' and sl>='$rows'");
        echo 'Delete';
    }
}

