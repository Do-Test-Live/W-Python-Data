<?php
require_once 'include/dbController.php';

// Create an instance of DBController
$dbController = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

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
    $result = $dbController->runQuery($query);

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
    <table id="editableTable" class="table">
        <thead>
        <tr>
            <th colspan="8"></th>
            <th colspan="2">連贏</th>
            <th colspan="2">位置Q</th>
            <th colspan="2">連贏及位置Q</th>
        </tr>
        <tr>
            <th>馬名</th>
            <th>獨贏</th>
            <th>位置</th>
            <th>獨贏</th>
            <th>位置</th>
            <th>獨贏</th>
            <th>場次</th>
            <th>馬名</th>
            <th>獨贏</th>
            <th>位置</th>
            <th>獨贏</th>
            <th>位置</th>
            <th>獨贏</th>
            <th>位置</th>
        </tr>
        </thead>
        <tbody>
        <?php
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

            ?>
            <tr>
                <td class="col-0-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_win_1']); ?>" contenteditable="true" data-name="new_win_1" data-id="<?php echo $row['id']; ?>"><?php echo $row['new_win_1']; ?></td>
                <td class="col-1-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_place_1']); ?>" contenteditable="true" data-name="new_place_1" data-id="<?php echo $row['id']; ?>"><?php echo $row['new_place_1']; ?></td>
                <td class="col-2-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_win_2']); ?>" contenteditable="true" data-name="new_win_2" data-id="<?php echo $row['id']; ?>"><?php echo $row['new_win_2']; ?></td>
                <td class="col-3-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_place_2']); ?>" contenteditable="true" data-name="new_place_2" data-id="<?php echo $row['id']; ?>"><?php echo $row['new_place_2']; ?></td>
                <td class="col-4-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_win_3']); ?>" contenteditable="true" data-name="new_win_3" data-id="<?php echo $row['id']; ?>"><?php echo $row['new_win_3']; ?></td>
                <td class="col-5-page-<?php echo $row['page_no']; ?>-value-<?php echo str_replace(".", "-", $row['new_place_3']); ?>" contenteditable="true" data-name="new_place_3" data-id="<?php echo $row['id']; ?>"><?php echo $row['new_place_3']; ?></td>
                <td><?php echo $row['page_no']; ?></td>
                <td><?php echo $row['horse_name']; ?></td>
                <td<?php echo $winClass; ?>><?php echo number_format((float)$row['win'], 1, '.', ''); ?></td>
                <td<?php echo $placeClass; ?>><?php echo number_format((float)$row['place'], 1, '.', ''); ?></td>
                <td<?php echo $winClass; ?>><?php echo number_format((float)$row['win'], 1, '.', ''); ?></td>
                <td<?php echo $placeClass; ?>><?php echo number_format((float)$row['place'], 1, '.', ''); ?></td>
                <td<?php echo $winClass; ?>><?php echo number_format((float)$row['win'], 1, '.', ''); ?></td>
                <td<?php echo $placeClass; ?>><?php echo number_format((float)$row['place'], 1, '.', ''); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script src="assets/vendor/jQuery/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // Fetch data and populate the table on page load
        function loadTableData() {
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: {action: 'fetch'},
                dataType: 'json',
                success: function (response) {
                    if (response.status == 'success') {
                        let rows = response.data;
                        let html = '';

                        for (let i = 0; i < rows.length; i++) {
                            html += '<tr>';
                            html += '<td contenteditable="true" data-name="new_win_1" data-id="' + rows[i].id + '">' + rows[i].new_win_1 + '</td>';
                            html += '<td contenteditable="true" data-name="new_place_1" data-id="' + rows[i].id + '">' + rows[i].new_place_1 + '</td>';
                            html += '<td contenteditable="true" data-name="new_win_2" data-id="' + rows[i].id + '">' + rows[i].new_win_2 + '</td>';
                            html += '<td contenteditable="true" data-name="new_place_2" data-id="' + rows[i].id + '">' + rows[i].new_place_2 + '</td>';
                            html += '<td contenteditable="true" data-name="new_win_3" data-id="' + rows[i].id + '">' + rows[i].new_win_3 + '</td>';
                            html += '<td contenteditable="true" data-name="new_place_3" data-id="' + rows[i].id + '">' + rows[i].new_place_3 + '</td>';
                            html += '<td>' + rows[i].page_no + '</td>';
                            html += '<td>' + rows[i].horse_name + '</td>';
                            html += '<td>' + rows[i].win + '</td>';
                            html += '<td>' + rows[i].place + '</td>';
                            html += '<td>' + rows[i].win + '</td>';
                            html += '<td>' + rows[i].place + '</td>';
                            html += '<td>' + rows[i].win + '</td>';
                            html += '<td>' + rows[i].place + '</td>';
                            html += '</tr>';
                        }

                        $('#editableTable tbody').html(html);
                    } else {
                        console.error(response.message);
                    }
                },
            });
        }

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
                        console.log(response.message);
                    } else {
                        console.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Ajax request error:', error);
                    console.log(error);
                }
            });
        });


        // Load table data on page load
        loadTableData();
    });


    $(document).ready(function () {
        setInterval(function () {
            let uniqueValues = [];
            let minimumValues = {};
            let minimumValuesSecond = {};
            let minimumValuesThird = {};

            // Iterate through each row in the table body
            $('#editableTable tbody tr').each(function () {
                let arrayValue = parseInt($(this).find('td').eq(6).text(), 10);

                // Check if the array value is a number and not NaN
                if (!isNaN(arrayValue)) {
                    // Check if the array value is already added to uniqueValues array
                    if (!uniqueValues.includes(arrayValue)) {
                        uniqueValues.push(arrayValue);
                        minimumValues[arrayValue] = [];
                        minimumValuesSecond[arrayValue] = [];
                        minimumValuesThird[arrayValue] = [];

                        // Iterate through each column index (0 to 5)
                        for (let columnIndex = 0; columnIndex < 6; columnIndex++) {
                            let cellValue = parseFloat($(this).find('td').eq(columnIndex).text());

                            // Check if the cell value is a number and not NaN, and not equal to zero
                            if (!isNaN(cellValue) && cellValue !== 0) {
                                minimumValues[arrayValue].push(cellValue);
                                minimumValuesSecond[arrayValue].push(cellValue);
                                minimumValuesThird[arrayValue].push(cellValue);
                            }
                        }
                    } else {
                        // Iterate through each column index (0 to 5)
                        for (let columnIndex = 0; columnIndex < 6; columnIndex++) {
                            let cellValue = parseFloat($(this).find('td').eq(columnIndex).text());

                            // Check if the cell value is a number and not NaN, and not equal to zero
                            if (!isNaN(cellValue) && cellValue !== 0) {
                                // Check if the cell value is smaller than the existing minimum value for the same array value and column index
                                if (cellValue < minimumValues[arrayValue][columnIndex]) {
                                    minimumValuesThird[arrayValue][columnIndex] = minimumValuesSecond[arrayValue][columnIndex];
                                    minimumValuesSecond[arrayValue][columnIndex] = minimumValues[arrayValue][columnIndex];
                                    minimumValues[arrayValue][columnIndex] = cellValue;
                                } else if (cellValue < minimumValuesSecond[arrayValue][columnIndex] && cellValue !== minimumValues[arrayValue][columnIndex]) {
                                    minimumValuesThird[arrayValue][columnIndex] = minimumValuesSecond[arrayValue][columnIndex];
                                    minimumValuesSecond[arrayValue][columnIndex] = cellValue;
                                } else if (cellValue < minimumValuesThird[arrayValue][columnIndex] && cellValue !== minimumValues[arrayValue][columnIndex] && cellValue !== minimumValuesSecond[arrayValue][columnIndex]) {
                                    minimumValuesThird[arrayValue][columnIndex] = cellValue;
                                } else if (minimumValuesThird[arrayValue][columnIndex] === minimumValuesSecond[arrayValue][columnIndex] && minimumValuesSecond[arrayValue][columnIndex] === minimumValues[arrayValue][columnIndex]) {
                                    // Update the third smallest value if it is the same as the second and first smallest values
                                    minimumValuesThird[arrayValue][columnIndex] = cellValue;
                                }
                            }
                        }
                    }
                }
            });

            // Reset the background color of all cells to white
            $('#editableTable td').css('background-color', '#ffffff');

            // Output the minimum values for each unique array value
            for (let value in minimumValues) {
                for (let columnIndex = 0; columnIndex < 6; columnIndex++) {
                    let className = `col-${columnIndex}-page-${value}-value-${parseFloat(minimumValues[value][columnIndex]).toFixed(2)}`.replace(/\./g, '-');

                    let elements = document.querySelectorAll('.' + className);

                    // Change the background color of the elements
                    elements.forEach((element) => {
                        element.style.backgroundColor = '#dc3545';
                    });
                }
            }

            // Output the second smallest values for each unique array value
            for (let value in minimumValuesSecond) {
                for (let columnIndex = 0; columnIndex < 6; columnIndex++) {
                    let className = `col-${columnIndex}-page-${value}-value-${parseFloat(minimumValuesSecond[value][columnIndex]).toFixed(2)}`.replace(/\./g, '-');

                    let elements = document.querySelectorAll('.' + className);

                    // Change the background color of the elements
                    elements.forEach((element) => {
                        element.style.backgroundColor = '#198754';
                    });
                }
            }

            // Output the third smallest values for each unique array value
            for (let value in minimumValuesThird) {
                for (let columnIndex = 0; columnIndex < 6; columnIndex++) {
                    let className = `col-${columnIndex}-page-${value}-value-${parseFloat(minimumValuesThird[value][columnIndex]).toFixed(2)}`.replace(/\./g, '-');

                    let elements = document.querySelectorAll('.' + className);

                    // Change the background color of the elements
                    elements.forEach((element) => {
                        element.style.backgroundColor = '#ffc107';
                    });
                }
            }

            // Output the minimum values for each unique array value
            for (let value in minimumValues) {
                console.log(`Minimum values for ${value}: ${minimumValues[value].join(", ")}`);
            }

            // Output the second smallest values for each unique array value
            for (let value in minimumValuesSecond) {
                console.log(`Second smallest values for ${value}: ${minimumValuesSecond[value].join(", ")}`);
            }

            // Output the third smallest values for each unique array value
            for (let value in minimumValuesThird) {
                console.log(`Third smallest values for ${value}: ${minimumValuesThird[value].join(", ")}`);
            }
        }, 1000); // 1000 milliseconds = 1 second
    });




</script>

</body>
</html>
