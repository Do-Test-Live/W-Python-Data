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

                    for (var i = 0; i < rows.length; i++) {
                        html += '<tr>';
                        html += '<td>' + rows[i].horse_name + '</td>';
                        html += '<td>' + rows[i].win + '</td>';
                        html += '<td>' + rows[i].place + '</td>';
                        html += '<td contenteditable="true" data-name="new_win" data-id="' + rows[i].id + '">' + rows[i].new_win + '</td>';
                        html += '<td contenteditable="true" data-name="new_place" data-id="' + rows[i].id + '">' + rows[i].new_place + '</td>';
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
    $(document).on('input', '[data-name="new_win"], [data-name="new_place"]', function () {
        let row = $(this).closest('tr');
        let rowData = {
            id: row.find('[data-name="new_win"]').data('id'),
            new_win: row.find('[data-name="new_win"]').text(),
            new_place: row.find('[data-name="new_place"]').text(),
        };

        $.ajax({
            url: 'data.php',
            type: 'POST',
            data: {
                action: 'edit',
                id: rowData.id,
                new_win: rowData.new_win,
                new_place: rowData.new_place,
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
