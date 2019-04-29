$(function () {
    var navMenuItem=$('#navMenu .navbar-item');

    navMenuItem.on('click',router);
    navMenuItem.on('click',function (e) {
        if( $(".navbar-burger").hasClass('is-active')){
            $(".navbar-burger").toggleClass("is-active");
            $(".navbar-menu").toggleClass("is-active");
        }
    });

    $(navMenuItem.toArray()[0]).trigger('click');


        // Check for click events on the navbar burger icon
        $(".navbar-burger").on('click',function() {
            // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
            $(".navbar-burger").toggleClass("is-active");
            $(".navbar-menu").toggleClass("is-active");

        });


});

function router(e) {
    e.preventDefault();
    $.ajax(
        'http://metahalculato.ru/sections/'+this.dataset.target+'/',
        {
            complete : function (result) {
                $('#ajax_result').remove();
                insertAfter(
                    createElementFromHTML(result.responseText),
                    document.querySelector('#navbar')
                );
                var button = $('#calc');
                button.on('click', function (e) {
                    e.preventDefault();
                    calc($('#total').val(), $('#own').val());
                });
                button.trigger('click');

            }
        }
    )
}
function calc(total, own) {
    $('#wait').show();
    $.getJSON({
        url: 'http://metahalculato.ru/calc/?total=' + total + '&own=' + own,
    }).done(function (result) {
        $('#wait').hide();
        $('table').remove();
        $('#frozen_amount').val(result['FROZEN_AMOUNT']);
        $('#table-container')
            .append(createTable(result['PLACE_MATRIX']));
        $('table').addClass('table is-fullwidth is-striped is-bordered');

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
        if(index < tableData.length-1){
          row=  table.insertRow();
            callback = function (cellData) {
                cell = row.insertCell();
                cell.textContent = cellData;
            }
        } else {
            callback = function (cellData) {
                cell = row.insertCell();
                cell.outerHTML = '<th>'+cellData+'</th>';
            };
            row=table.createTFoot().insertRow();
        }
        cell = row.insertCell();
        cell.outerHTML = '<th>' + placeDictionary[index] + '</th>';


        Object.values(rowData).forEach(callback);
    });
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