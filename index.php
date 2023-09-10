<?php
require_once 'include/dbController.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Create an instance of DBController
$dbController = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

if (isset($_GET['reset'])) {
    $query = "UPDATE racevalue SET new_win_1='0', new_place_1='0',new_win_2='0', new_place_2='0',new_win_3='0', new_place_3='0' WHERE date='$date'";
    $result = $dbController->insertQuery($query);
    ?>
    <script>
        alert('Reset Successful');
        location.href = 'index.php';
    </script>
    <?php
}

if (isset($_GET['reload'])) {
    ?>
    <script>
        alert('Refresh Successful');
        location.href = 'index.php';
    </script>
    <?php
}


// Fetch data from the database
$query = "SELECT * FROM  page as p,racevalue as r WHERE p.id = r.page_id AND p.date = '$date'";
//$query = "SELECT * FROM racevalue as r, page as p WHERE p.id = r.page_id AND p.date = '$date' ORDER BY p.page_no ASC";
$rows = $dbController->runQuery($query);

// Handle CRUD operations
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'fetch') {
    $response = ['status' => 'success', 'data' => $rows];
    echo json_encode($response);
} elseif ($action === 'edit') {
    $id = $dbController->checkValue($_POST['id']);
    $newWin1 = $dbController->checkValue($_POST['new_win_1']);
    $newPlace1 = $dbController->checkValue($_POST['new_place_1']);
    $newWin2 = $dbController->checkValue($_POST['new_win_2']);
    $newPlace2 = $dbController->checkValue($_POST['new_place_2']);
    $newWin3 = $dbController->checkValue($_POST['new_win_3']);
    $newPlace3 = $dbController->checkValue($_POST['new_place_3']);

    $query = "UPDATE racevalue SET new_win_1='$newWin1', new_place_1='$newPlace1',new_win_2='$newWin2', new_place_2='$newPlace2',new_win_3='$newWin3', new_place_3='$newPlace3' WHERE id=$id";
    $result = $dbController->insertQuery($query);

    if ($result === TRUE) {
        $response = ['status' => 'success', 'message' => 'Record updated successfully.'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to update record.'];
    }

    echo json_encode($response);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>HKJC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        /* Add this CSS to make the header sticky */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #f9f9f9; /* Optional: Set the background color of the header */
        }

        /* Add this CSS to remove the transparent border from th elements within the thead */
        thead th {
            border: none;
        }

        /* Optional: Set the height and other styles for the header cells */
        .sticky-header th {
            height: 50px;
            /* Add other styles as needed */
        }
    </style>
</head>
<body>

<?php
$smallestValuesWin = array();

foreach ($rows as $row) {
    $pageNumber = $row['page_no'];
    $value = $row['win'];

    if ($value == 0) {
        continue;
    }

    if (!isset($smallestValuesWin[$pageNumber])) {
        $smallestValuesWin[$pageNumber] = array();
    }

    $smallestValuesWin[$pageNumber][] = $value;
}

foreach ($smallestValuesWin as &$values) {
    sort($values);
}

// Find the smallest values for each page number
foreach ($smallestValuesWin as $pageNumber => &$values) {

    $values = array_values(array_unique($values));

    if (count($values) >= 1) {
        $smallestValuesPerPageWin[$pageNumber]['first'] = $values[0];
    }
    if (count($values) >= 2) {
        $smallestValuesPerPageWin[$pageNumber]['second'] = $values[1];
    }
    if (count($values) >= 3) {
        $smallestValuesPerPageWin[$pageNumber]['third'] = $values[2];
    }

}


$smallestValuesPlace = array();

foreach ($rows as $row) {
    $pageNumber = $row['page_no'];
    $value = $row['place'];

    if ($value == 0) {
        continue;
    }

    if (!isset($smallestValuesPlace[$pageNumber])) {
        $smallestValuesPlace[$pageNumber] = array();
    }
    $smallestValuesPlace[$pageNumber][] = $value;
}

foreach ($smallestValuesPlace as &$values) {
    sort($values);
}

$smallestValuesPerPagePlace = array();

foreach ($smallestValuesPlace as $pageNumber => &$values) {

    $values = array_values(array_unique($values));

    if (count($values) >= 1) {
        $smallestValuesPerPagePlace[$pageNumber]['first'] = $values[0];
    }
    if (count($values) >= 2) {
        $smallestValuesPerPagePlace[$pageNumber]['second'] = $values[1];
    }
    if (count($values) >= 3) {
        $smallestValuesPerPagePlace[$pageNumber]['third'] = $values[2];
    }
}
?>


<div class="container mt-4">
    <div class="row">
        <div class="col-6">
            <div class="mb-5">
                <a href="index.php?reload=1" class="btn btn-success btn-lg">Refresh Page</a>
            </div>
        </div>
        <div class="col-6">
            <div class="text-end mb-5">
                <a href="index.php?reset=1" class="btn btn-primary btn-lg">Reset</a>
            </div>
        </div>
    </div>
    <table id="editableTable" class="table">
        <thead class="sticky-header">
        <tr>
            <th>場次</th>
            <th>馬號</th>
            <th>馬名</th>

            <th>獨贏</th>
            <th>位置</th>

            <th>獨贏</th>
            <th>位置</th>

            <th>獨贏</th>
            <th>位置</th>

            <th class="bg-light">獨贏</th>
            <th class="bg-light">位置</th>
            <th colspan="2"></th>
            <th>(1 vs 3)</th>
            <th>(2 vs 4)</th>
            <th>(3 vs 5)</th>
            <th>(4 vs 6)</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $prevPageNo = null;
        $k = 0;

        foreach ($rows as $row):

            $winClass = '';
            $placeClass = '';

            foreach ($smallestValuesPerPageWin as $pageNumber => $values) {
                if ($row['page_no'] == $pageNumber) {
                    if ($values['first'] == $row['win']) {
                        $winClass = ' class="bg-danger"';
                    } else if ($values['second'] == $row['win']) {
                        $winClass = ' class="bg-success"';
                    } else if ($values['third'] == $row['win']) {
                        $winClass = ' class="bg-warning"';
                    }
                }
            }

            foreach ($smallestValuesPerPagePlace as $pageNumber => $values) {
                if ($row['page_no'] == $pageNumber) {
                    if ($values['first'] == $row['place']) {
                        $placeClass = ' class="bg-danger"';
                    } else if ($values['second'] == $row['place']) {
                        $placeClass = ' class="bg-success"';
                    } else if ($values['third'] == $row['place']) {
                        $placeClass = ' class="bg-warning"';
                    }
                }
            }

            if ($prevPageNo !== null && $prevPageNo !== $row['page_no']) {
                // Add a blank row when the current page number is different from the previous one
                $k = 0;
                ?>
                <tr>
                    <td colspan="14" style="height: 100px"></td>
                </tr>
                <?php
            }

            $k += 1;
            $prevPageNo = $row['page_no'];
            ?>
            <tr>
                <td><?php echo $row['page_no']; ?></td>
                <td><?php echo $k; ?></td>
                <td><?php echo $row['horse_name']; ?></td>
                <td class="col-3-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_win_1']); ?>"
                    contenteditable="true" data-name="new_win_1"
                    data-id="<?php echo $row['id']; ?>"><?php echo $row['new_win_1']; ?></td>
                <td class="col-4-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_place_1']); ?>"
                    contenteditable="true" data-name="new_place_1"
                    data-id="<?php echo $row['id']; ?>"><?php echo $row['new_place_1']; ?></td>
                <td class="col-5-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_win_2']); ?>"
                    contenteditable="true" data-name="new_win_2"
                    data-id="<?php echo $row['id']; ?>"><?php echo $row['new_win_2']; ?></td>
                <td class="col-6-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_place_2']); ?>"
                    contenteditable="true" data-name="new_place_2"
                    data-id="<?php echo $row['id']; ?>"><?php echo $row['new_place_2']; ?></td>
                <td class="col-7-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_win_3']); ?>"
                    contenteditable="true" data-name="new_win_3"
                    data-id="<?php echo $row['id']; ?>"><?php echo $row['new_win_3']; ?></td>
                <td class="col-8-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_place_3']); ?>"
                    contenteditable="true" data-name="new_place_3"
                    data-id="<?php echo $row['id']; ?>"><?php echo $row['new_place_3']; ?></td>
                <td class="bg-light"<?php if($row['win']<$row['new_win_3']) echo " style='background-color:#b88bff !important;'"; ?>><?php echo number_format((float)$row['win'], 1, '.', ''); ?></td>
                <td class="bg-light"<?php if($row['place']<$row['new_place_3']) echo " style='background-color:#b88bff !important;'"; ?>><?php echo number_format((float)$row['place'], 1, '.', ''); ?></td>
                <td colspan="2"></td>
                <?php
                $value_1 = (float)$row['new_win_2'] - (float)$row['new_win_1'];
                ?>
                <td<?php if($value_1<0) echo " class='text-danger'"; ?>>
                    <?php echo number_format($value_1, 1, '.', ''); ?>
                </td>
                <?php
                $value_2 = (float)$row['new_place_2'] - (float)$row['new_place_1'];
                ?>
                <td<?php if($value_2<0) echo " class='text-danger'"; ?>>
                    <?php echo number_format($value_2, 1, '.', ''); ?>
                </td>
                <?php
                $value_3 = (float)$row['new_win_3'] - (float)$row['new_win_2'];
                ?>
                <td<?php if($value_3<0) echo " class='text-danger'"; ?>>
                    <?php echo number_format($value_3, 1, '.', ''); ?>
                </td>
                <?php
                $value_4 = (float)$row['new_place_3'] - (float)$row['new_place_2'];
                ?>
                <td<?php if($value_4<0) echo " class='text-danger'"; ?>>
                    <?php echo number_format($value_4, 1, '.', ''); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Variable to store the scroll position
    let scrollPosition = 0;

    // Function to reload the page and maintain scroll position
    function reloadPage() {
        // Store the current scroll position
        scrollPosition = window.scrollY;

        // Reload the page
        location.href = 'index.php';
    }

    function loadTableData() {
        let prevPageNo=1;
        let html='';
        console.log('Fetching table data...');
        $.ajax({
            url: 'pageReload.php',
            type: 'POST',
            data: { action: 'fetch' },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    for (let i = 0; i < response.data.length; i++) {
                        let currentPageNo = response.data[i].page_no;

                        if (currentPageNo !== prevPageNo) {
                            k = 1; // Reset k to 1 for a new page_no
                            if(i!=0){
                                html += '<tr style="height: 100px"></tr>';
                            }
                            prevPageNo = currentPageNo;
                        } else {
                            k++; // Increment k for the same page_no
                        }

                        html += '<tr>';
                        html += '<td>' + currentPageNo + '</td>';
                        html += '<td>' + k + '</td>';
                        html += '<td>' + response.data[i].horse_name + '</td>';
                        html += '<td contenteditable="true" data-name="new_win_1" data-id="' + response.data[i].id + '" class="col-3-page-' + response.data[i].page_no + '-value-' + response.data[i].new_win_1.replace(/\./g, '-') + '">' + response.data[i].new_win_1 + '</td>';
                        html += '<td contenteditable="true" data-name="new_place_1" data-id="' + response.data[i].id + '" class="col-4-page-' + response.data[i].page_no + '-value-' + response.data[i].new_place_1.replace(/\./g, '-') + '">' + response.data[i].new_place_1 + '</td>';
                        html += '<td contenteditable="true" data-name="new_win_2" data-id="' + response.data[i].id + '" class="col-5-page-' + response.data[i].page_no + '-value-' + response.data[i].new_win_2.replace(/\./g, '-') + '">' + response.data[i].new_win_2 + '</td>';
                        html += '<td contenteditable="true" data-name="new_place_2" data-id="' + response.data[i].id + '" class="col-6-page-' + response.data[i].page_no + '-value-' + response.data[i].new_place_2.replace(/\./g, '-') + '">' + response.data[i].new_place_2 + '</td>';
                        html += '<td contenteditable="true" data-name="new_win_3" data-id="' + response.data[i].id + '" class="col-7-page-' + response.data[i].page_no + '-value-' + response.data[i].new_win_3.replace(/\./g, '-') + '">' + response.data[i].new_win_3 + '</td>';
                        html += '<td contenteditable="true" data-name="new_place_3" data-id="' + response.data[i].id + '" class="col-8-page-' + response.data[i].page_no + '-value-' + response.data[i].new_place_3.replace(/\./g, '-') + '">' + response.data[i].new_place_3 + '</td>';
                        html += '<td style="background-color:' + (parseFloat(response.data[i].win) < parseFloat(response.data[i].new_win_3) ? '#b88bff' : '#f8f9fa') + ';">' + (parseFloat(response.data[i].win).toFixed(1)) + '</td>';
                        html += '<td style="background-color:' + (parseFloat(response.data[i].place) < parseFloat(response.data[i].new_place_3) ? '#b88bff' : '#f8f9fa') + ';">' + (parseFloat(response.data[i].place).toFixed(1)) + '</td>';
                        html += '<td colspan="2"></td>';
                        html += '<td' + (response.data[i].new_win_2 - response.data[i].new_win_1 < 0 ? ' class="text-danger"' : '') + '>' + (parseFloat(response.data[i].new_win_2 - response.data[i].new_win_1).toFixed(1)) + '</td>';
                        html += '<td' + (response.data[i].new_place_2 - response.data[i].new_place_1 < 0 ? ' class="text-danger"' : '') + '>' + (parseFloat(response.data[i].new_place_2 - response.data[i].new_place_1).toFixed(1)) + '</td>';
                        html += '<td' + (response.data[i].new_win_3 - response.data[i].new_win_2 < 0 ? ' class="text-danger"' : '') + '>' + (parseFloat(response.data[i].new_win_3 - response.data[i].new_win_2).toFixed(1)) + '</td>';
                        html += '<td' + (response.data[i].new_place_3 - response.data[i].new_place_2 < 0 ? ' class="text-danger"' : '') + '>' + (parseFloat(response.data[i].new_place_3 - response.data[i].new_place_2).toFixed(1)) + '</td>';
                        html += '</tr>';
                    }

                    $('#editableTable tbody').html(html);

                    console.log('Value Update');
                } else {
                    console.error(response.message);
                }
            }
        });
    }

    $(document).ready(function () {
        // Load table data on page load
        loadTableData();

        // Refresh the data every second
        setInterval(loadTableData, 10000);

        // Update the row data using Ajax on input change
        $(document).on('input', '[data-name="new_win_1"], [data-name="new_place_1"], [data-name="new_win_2"], [data-name="new_place_2"], [data-name="new_win_3"], [data-name="new_place_3"]', function () {
            let row = $(this).closest('tr');
            let rowData = {
                id: row.find('[data-name="new_win_1"]').data('id'),
                new_win_1: row.find('[data-name="new_win_1"]').text(),
                new_place_1: row.find('[data-name="new_place_1"]').text(),
                new_win_2: row.find('[data-name="new_win_2"]').text(),
                new_place_2: row.find('[data-name="new_place_2"]').text(),
                new_win_3: row.find('[data-name="new_win_3"]').text(),
                new_place_3: row.find('[data-name="new_place_3"]').text(),
            };

            console.log(rowData);

            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: {
                    action: 'edit',
                    id: rowData.id,
                    new_win_1: rowData.new_win_1,
                    new_place_1: rowData.new_place_1,
                    new_win_2: rowData.new_win_2,
                    new_place_2: rowData.new_place_2,
                    new_win_3: rowData.new_win_3,
                    new_place_3: rowData.new_place_3,
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        setInterval(function () {
                            reloadPage;
                        }, 3000);
                    } else {
                        setInterval(function () {
                            reloadPage;
                        }, 3000);
                    }
                },
                error: function (xhr, status, error) {
                    setInterval(function () {
                        reloadPage;
                    }, 3000);
                }
            });

        });



        setInterval(function () {
            let uniqueValues = [];
            let combinedValues = [];

            // Iterate through each row in the table body
            $('#editableTable tbody tr').each(function () {
                let arrayValue = parseInt($(this).find('td').eq(0).text(), 10);

                // Check if the arrayValue is a number and not NaN
                if (!isNaN(arrayValue)) {
                    // Check if the arrayValue is already added to uniqueValues array
                    if (!uniqueValues.includes(arrayValue)) {
                        uniqueValues.push(arrayValue);
                        combinedValues.push(Array.from(Array(6), () => ({
                            minimum: Number.MAX_VALUE,
                            secondMinimum: Number.MAX_VALUE,
                            thirdMinimum: Number.MAX_VALUE
                        })));
                    }

                    // Iterate through each column index (3 to 8)
                    for (let columnIndex = 3; columnIndex < 9; columnIndex++) {
                        let cellValue = parseFloat($(this).find('td').eq(columnIndex).text());

                        // Check if the cellValue is a number and not NaN, and not equal to zero
                        if (!isNaN(cellValue) && cellValue !== 0) {
                            let currentValue = combinedValues[combinedValues.length - 1][columnIndex - 3];

                            if (cellValue < currentValue.minimum) {
                                currentValue.thirdMinimum = currentValue.secondMinimum;
                                currentValue.secondMinimum = currentValue.minimum;
                                currentValue.minimum = cellValue;
                            } else if (cellValue < currentValue.secondMinimum && cellValue !== currentValue.minimum) {
                                currentValue.thirdMinimum = currentValue.secondMinimum;
                                currentValue.secondMinimum = cellValue;
                            } else if (cellValue < currentValue.thirdMinimum && cellValue !== currentValue.minimum && cellValue !== currentValue.secondMinimum) {
                                currentValue.thirdMinimum = cellValue;
                            }
                        }
                    }
                }
            });

            // Reset the background color of all cells to white
            $('#editableTable td[0]').css('background-color', '#ffffff');
            $('#editableTable td[1]').css('background-color', '#ffffff');
            $('#editableTable td[2]').css('background-color', '#ffffff');
            $('#editableTable td[3]').css('background-color', '#ffffff');
            $('#editableTable td[4]').css('background-color', '#ffffff');
            $('#editableTable td[5]').css('background-color', '#ffffff');
            $('#editableTable td[6]').css('background-color', '#ffffff');
            $('#editableTable td[7]').css('background-color', '#ffffff');
            $('#editableTable td[8]').css('background-color', '#ffffff');
            $('#editableTable td[11]').css('background-color', '#ffffff');
            $('#editableTable td[12]').css('background-color', '#ffffff');
            $('#editableTable td[13]').css('background-color', '#ffffff');
            $('#editableTable td[14]').css('background-color', '#ffffff');

            // Output the minimum values, second smallest values, and third smallest values for each unique arrayValue
            for (let i = 0; i < uniqueValues.length; i++) {
                let arrayValue = uniqueValues[i];

                for (let columnIndex = 3; columnIndex < 9; columnIndex++) {
                    let minimum = combinedValues[i][columnIndex - 3].minimum.toFixed(1);
                    let secondMinimum = combinedValues[i][columnIndex - 3].secondMinimum.toFixed(1);
                    let thirdMinimum = combinedValues[i][columnIndex - 3].thirdMinimum.toFixed(1);

                    let minimumClassName = `col-${columnIndex}-page-${arrayValue}-value-${minimum.replace(/\./g, '-').replace(/\+/g, '')}`;
                    let secondMinimumClassName = `col-${columnIndex}-page-${arrayValue}-value-${secondMinimum.replace(/\./g, '-').replace(/\+/g, '')}`;
                    let thirdMinimumClassName = `col-${columnIndex}-page-${arrayValue}-value-${thirdMinimum.replace(/\./g, '-').replace(/\+/g, '')}`;

                    let minimumElements = document.querySelectorAll('.' + minimumClassName);
                    let secondMinimumElements = document.querySelectorAll('.' + secondMinimumClassName);
                    let thirdMinimumElements = document.querySelectorAll('.' + thirdMinimumClassName);

                    // Change the background color of the elements
                    minimumElements.forEach((element) => {
                        element.style.backgroundColor = '#dc3545';
                    });

                    secondMinimumElements.forEach((element) => {
                        element.style.backgroundColor = '#198754';
                    });

                    thirdMinimumElements.forEach((element) => {
                        element.style.backgroundColor = '#ffc107';
                    });
                }
            }

            // Output the minimum values, second smallest values, and third smallest values for each unique arrayValue
            for (let i = 0; i < uniqueValues.length; i++) {
                let arrayValue = uniqueValues[i];

                let minimumValues = combinedValues[i].map(value => value.minimum.toFixed(1)).join(", ");
                let secondMinimumValues = combinedValues[i].map(value => value.secondMinimum.toFixed(1)).join(", ");
                let thirdMinimumValues = combinedValues[i].map(value => value.thirdMinimum.toFixed(1)).join(", ");
            }
        }, 1000); // 1000 milliseconds = 1 second
        scrollToStoredPosition();
    });

    // Function to scroll back to the stored position after page reload
    function scrollToStoredPosition() {
        window.scrollTo(0, scrollPosition);
    }

    $(document).on('click', '[contenteditable="true"]', function () {
        $(this).text('');
    });
</script>
</body>
</html>
