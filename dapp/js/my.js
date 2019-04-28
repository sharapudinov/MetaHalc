$(function () {
    var button = $('#calc');
    button.on('click', function (e) {
        e.preventDefault();
        calc($('#total').val(), $('#own').val());
    });
    button.trigger('click');
});

function calc(total, own) {
    $('#wait').show();
    $.getJSON({
        url: 'http://metahalculato.ru/calc/?total=' + total + '&own=' + own,
        context: $('#container')
    }).done(function (result) {
        $('#wait').hide();
        $('table').remove();
        $('#frozen_amount').val(result['FROZEN_AMOUNT']);
        $(this).append(createTable(result['PLACE_MATRIX']));
    });
}

function createTable(tableData) {
    var headerDictionary = {
        PROBABILITY: 'Reward chance (%)',
        AVG_DAILY_PROFIT: 'Average daily profit (MHC)',
        PERIODICITY: 'Average periodicity (days)',
        DAILY_ROI: 'Average daily ROI (%)',
    };
    var table = document.createElement('table');
    var header = document.createElement("tr");
    // get first row to be header
    var headers = ['Place'];

    for (key in tableData[0]) {
        headers.push(headerDictionary[key])
    }

    // create table header
    headers.forEach(function (rowHeader) {
        var th = document.createElement("th");
        th.appendChild(document.createTextNode(rowHeader));
        header.appendChild(th);
    });
    // insert table header
    table.append(header);
    var row = {};
    var cell = {};
    // remove first how - header
    tableData.forEach(function (rowData, index) {
        var placeDictionary = {
            0: 'First',
            1: 'Second',
            2: 'Third',
            3: 'Fourth',
            4: 'Fifth',
            5: 'From 6 to 100',
            6: 'From 101 to 1000',
            7: 'Total'
        };
        row = table.insertRow();
        cell=row.insertCell();
        cell.textContent=placeDictionary[index];
        Object.values(rowData).forEach(function (cellData) {
            cell = row.insertCell();
            cell.textContent = cellData;
        });
    });
    return (table);
}