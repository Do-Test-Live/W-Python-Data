<?php
include('include/dbController.php');
$db_handle = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

if (isset($_GET['page_id'])) {

    $query = "SELECT * FROM `page` where id={$_GET['page_id']}";
    $page = $db_handle->runQuery($query);
    $row = $db_handle->numRows($query);
    for ($i = 0; $i < $row; $i++) {
        ?>
        <h4>
            頁碼 <?php echo $page[$i]['page_no']; ?>
        </h4>
        <div class="table-responsive">
            <table class="table table-success table-striped table-bordered table-hover">
                <thead>
                <tr class="text-center">
                    <th colspan="2">
                        連贏
                    </th>
                    <th colspan="2">
                        位置Q
                    </th>
                    <th colspan="2">
                        連贏及位置Q
                    </th>
                    <th colspan="2">

                    </th>
                    <th colspan="2">
                        連贏
                    </th>
                    <th colspan="2">
                        位置Q
                    </th>
                    <th colspan="2">
                        連贏及位置Q
                    </th>
                </tr>
                <tr>
                    <th scope="col">獨贏</th>
                    <th scope="col">位置</th>
                    <th scope="col">獨贏</th>
                    <th scope="col">位置</th>
                    <th scope="col">獨贏</th>
                    <th scope="col">位置</th>
                    <th scope="col">#</th>
                    <th scope="col">馬名</th>
                    <th scope="col">獨贏</th>
                    <th scope="col">位置</th>
                    <th scope="col">獨贏</th>
                    <th scope="col">位置</th>
                    <th scope="col">獨贏</th>
                    <th scope="col">位置</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $page_id = $page[$i]['id'];

                $data_query = "SELECT * FROM `racevalue` where page_id='$page_id' and date ='$date' order by sl asc";

                $data = $db_handle->runQuery($data_query);
                $row_count = $db_handle->numRows($data_query);
                for ($j = 0; $j < $row_count; $j++) {
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th scope="row"><?php echo $data[$j]['sl']; ?></th>
                        <td><?php echo $data[$j]['horse_name']; ?></td>
                        <td><?php echo $data[$j]['win']; ?></td>
                        <td><?php echo $data[$j]['place']; ?></td>
                        <td><?php echo $data[$j]['win']; ?></td>
                        <td><?php echo $data[$j]['place']; ?></td>
                        <td><?php echo $data[$j]['win']; ?></td>
                        <td><?php echo $data[$j]['place']; ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
