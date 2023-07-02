<?php

include('include/dbController.php');
$db_handle = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

if (isset($_GET['page_no'])) {
    $page_no = $db_handle->checkValue($_GET['page_no']);
    $rows = $db_handle->checkValue($_GET['rows']);

    $query = "SELECT * FROM `page` where page_no='$page_no' and date ='$date'";

    $page = $db_handle->runQuery($query);
    $row = $db_handle->numRows($query);

    $inserted_at = date("Y-m-d H:i:s");

    $page_id=0;
    if($row>0){
        $page_id=$page[0]['id'];
    }else{
        $insert = $db_handle->insertQuery("INSERT INTO `page`(`date`, `page_no`, `inserted_at`) VALUES ('$date','$page_no','$inserted_at')");

        $page = $db_handle->runQuery("select * from page order by id desc limit 1");

        $page_id=$page[0]['id'];
    }

    $delete = $db_handle->insertQuery("DELETE FROM `page` WHERE id='$page_id'");

    $delete = $db_handle->insertQuery("DELETE FROM `racevalue` WHERE date ='$date' and page_id='$page_id' and sl>='$rows'");
    echo 'Delete';
}
