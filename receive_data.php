<?php

include('include/dbController.php');
$db_handle = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

if (isset($_GET['page_no'])) {
    $page_no = $db_handle->checkValue($_GET['page_no']);
    $sl = $db_handle->checkValue($_GET['sl']);
    $horse_name = $db_handle->checkValue($_GET['horse_name']);
    $win = $db_handle->checkValue($_GET['win']);
    $place = $db_handle->checkValue($_GET['place']);

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


    $query = "SELECT * FROM `racevalue` where page_id='$page_id' and date ='$date' and sl='$sl'";

    $page = $db_handle->runQuery($query);
    $row = $db_handle->numRows($query);

    if($row>0){
        $update=$db_handle->insertQuery("UPDATE `racevalue` SET `win`='$win',`place`='$place',`updated_at`='$inserted_at' WHERE page_id='$page_id' and date ='$date' and sl='$sl'");
        echo 'Update';
    }else{
        $insert = $db_handle->insertQuery("INSERT INTO `racevalue`(`date`, `page_id`, `sl`, `horse_name`, `win`, `place`, `inserted_at`) VALUES ('$date','$page_id','$sl','$horse_name','$win','$place','$inserted_at')");
        echo 'Insert';
    }
}
