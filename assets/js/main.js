$(document).ready(function () {
    // Fetch data and populate the table on page load
    function loadTableData() {
        $.ajax({
            url: 'data.php',
            type: 'POST',
            data: { action: 'fetch' },
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
            id: row.find('[data-name="new_win"]').data('id'),
            new_win_1: row.find('[data-name="new_win_1"]').text(),
            new_place_1: row.find('[data-name="new_place_1"]').text(),
            new_win_2: row.find('[data-name="new_win_2"]').text(),
            new_place_2: row.find('[data-name="new_place_2"]').text(),
            new_win_3: row.find('[data-name="new_win_3"]').text(),
            new_place_3: row.find('[data-name="new_place_3"]').text(),
        };

        $.ajax({
            url: 'data.php',
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
                if (response.status != 'success') {
                    console.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax request error:', error);
            }
        });
    });

    // Load table data on page load
    loadTableData();
});
