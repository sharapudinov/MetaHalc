$(function () {
    var navMenuItem = $('#navMenu .navbar-item');

    navMenuItem.on('click', router);
    navMenuItem.on('click', function (e) {
        if ($(".navbar-burger").hasClass('is-active')) {
            $(".navbar-burger").toggleClass("is-active");
            $(".navbar-menu").toggleClass("is-active");
        }
    });

    $(navMenuItem.toArray()[0]).trigger('click');


    // Check for click events on the navbar burger icon
    $(".navbar-burger").on('click', function () {
        // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
        $(".navbar-burger").toggleClass("is-active");
        $(".navbar-menu").toggleClass("is-active");

    });


});

function router(e) {
    e.preventDefault();
    $.ajax(
        'http://metahash.tools/sections/' + this.dataset.target + '/',
        {
            complete: function (result) {
                $('#ajax_result').remove();
                insertAfter(
                    createElementFromHTML(result.responseText),
                    document.querySelector('#navbar')
                );
                var calcButton = $('#calc');
                calcButton.on('click', function (e) {
                    e.preventDefault();
                    calc($('#total').val(), $('#own').val());
                });
                var fetchDelegatorsListButton = $('#fetchDelegatorsList');
                fetchDelegatorsListButton.on('click', function (e) {
                    e.preventDefault();
                    fetchDelegatorList($('#nodeAddress').val());
                });
                var date=new Date();
/*
                date.setDate(date.getDate()-1);
*/
                var options= {
                    type: "date",
                    color: "primary",
                    showHeader: false,
                    showClearButton: false,
                    startDate: date
                }
                var calendars = bulmaCalendar.attach('[type="date"]',options);

                calcButton.trigger('click');


            }
        }
    )
}

function calc(total, own) {
    $('#wait').show();
    $.getJSON({
        url: 'http://metahash.tools/calc/?total=' + total + '&own=' + own,
    }).done(function (result) {
        $('#wait').hide();
        $('table').remove();
        $('#frozen_amount').val(result['FROZEN_AMOUNT']);
        $('#table-container')
            .append(createForgingMatrixTable(result['PLACE_MATRIX']));
        $('table').addClass('table is-fullwidth is-striped is-bordered');

    });
}

function fetchDelegatorList(nodeAddress) {
    $('#fetch-wait').show();
    $.getJSON({
        url: 'http://metahash.tools/calc/node/?address=' + nodeAddress+'&date='+$('#date').val(),
    }).done(function (result) {
        $('#fetch-wait').hide();
        $('#lastRewardValue').val(result['last_reward']);

        $('table').remove();

        $('#table-container')
            .append(createDelegatorMatrixTable(result));
        $('table').addClass('table is-fullwidth is-striped is-bordered');

    });
}

function createForgingMatrixTable(tableData) {
    var headerDictionary = {
        PROBABILITY: 'Reward chance (%)',
        AVG_DAILY_PROFIT: 'Average daily profit (MHC)',
        PERIODICITY: 'Average periodicity (days)',
        DAILY_ROI: 'Average daily ROI (%)',
    };
    var table = document.createElement('table');

    var header = document.createElement("thead");
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
        var collback;
        if (index < tableData.length - 1) {
            row = table.insertRow();
            callback = function (cellData) {
                cell = row.insertCell();
                cell.textContent = cellData;
            }
        } else {
            callback = function (cellData) {
                cell = row.insertCell();
                cell.outerHTML = '<th>' + cellData + '</th>';
            };
            row = table.createTFoot().insertRow();
        }
        cell = row.insertCell();
        cell.outerHTML = '<th>' + placeDictionary[index] + '</th>';


        Object.values(rowData).forEach(callback);
    });
    return (table);
}

function createDelegatorMatrixTable(tableData) {

    var table = document.createElement('table');

    var header = document.createElement("thead");
    // get first row to be header

    var headers = [
        'Delegator address',
        'Delegation ballance',
        'Share amount'
    ];
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
    var delegator_matrix = tableData['delegator_matrix'];

    delegator_matrix.forEach(function (rowData, index) {
        row = table.insertRow();
        cell = row.insertCell();
        cell.textContent = rowData['address'];
        cell = row.insertCell();
        cell.textContent = rowData['delegated'];
        cell = row.insertCell();
        var amount=rowData['delegated'] * $('#share').val() / 100 / tableData['total_sum'] * tableData['last_reward'];
        amount=parseFloat(amount.toFixed(6));
        cell.textContent =amount;
        cell = row.insertCell();
        cell.innerHTML  = '<a href="metapay://pay.metahash.org/?to='+ rowData['address']+'&description=node+reward+share&data=31415926&currency=MHC&value='+amount+'" class="button is-primary">Pay!</a>\n'
    });
    row = table.insertRow();
    cell = row.insertCell();
    cell.outerHTML = '<th>Total delegated</th>'
    cell = row.insertCell();
    cell.outerHTML = '<th>' + tableData['total_sum'] + '</th>';
    cell = row.insertCell();
    cell.outerHTML = '<th>' + tableData['last_reward'] * $('#share').val() / 100 + '</th>';

    row = table.createTFoot().insertRow();


    return (table);

}


function insertAfter(elem, refElem) {
    var parent = refElem.parentNode;
    var next = refElem.nextSibling;
    if (next) {
        return parent.insertBefore(elem, next);
    } else {
        return parent.appendChild(elem);
    }
}

function createElementFromHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();

    // Change this to div.childNodes to support multiple top-level nodes
    return div.firstChild;
}
