"use strict";
var BalanceModule = easejs.Class('BalanceModule').extend( Module,
{
    'protected _pageElements': [],
    'protected accountId': null,

    __construct: function( accountId ) {
        if (typeof accountId === "undefined" || isNaN(parseInt(accountId))) {
            console.log('Balance module incorrectly initialized; will not render!')
        }

        this._pageElements = [
            ['table', '#balanceForAccount%account%'],
            ['loader', '#formLoadingForAccount%account%']
        ];

        this.accountId = parseInt(accountId);
        for (var i in this._pageElements) {
            var elem = this._pageElements[i];
            elem[1] = elem[1].replace('%account%', this.accountId);
            this._pageElements[i] = elem;
        }
    },


    'public onDocumentReady': function() {
        console.log('LOADING MODULE: Balance with accountId=' + this.accountId);
        this.update('table', 'loader');
    },

    'public update': function(content, loader) {
        var httpRequest, payload = JSON.stringify({account: this.accountId});

        this.get(content).hide();
        this.get(loader).show();

        httpRequest = $.ajax( {
            url: Routing.generate('pms_finance_balance_data'),
            type: 'POST',
            data: payload,
            dataType: 'json',
            context: this,
            success: function(response, status) {
                var dt, api, rows = [];
                if (response.err) {
                    console.log("MODULE::Balance::Balance failed to load for account " + this.accountId);
                }
                console.log("MODULE::Balance::Loaded OK for account " + this.accountId);

                // todo - configuration
                dt = this.get('table').dataTable( {
                    paging: false,
                    ordering: false,
                    info: false,
                    searching: false
                } );

                api = dt.api(true);
                // todo - modules & memory management!!
                // todo - tidy up things here, and make sure everything is generic
                $.each(response.val.lines, function(key, rowObj) {
                    console.log(rowObj);
                    var valueText, scopesText;

                    valueText = parseInt(rowObj.isIncome) ? '+' : '-';
                    valueText += rowObj.value + ' ' + response.val.account.currency;

                    console.log(rowObj);
                    scopesText = (rowObj.scopes).join(', ');

                    rows.push([rowObj.date, valueText, scopesText, rowObj.balance]);
                } );

                api.rows.add(rows).draw();





                this.get(loader).hide();
                this.get(content).show();

            },
            error: function() {

            }
        } );

//        this.get('table').dataTable( {
//            ajax: {
//                url: Routing.generate('pms_finance_balance_data'),
//                type: 'POST',
//                data: payload,
//                dataType: 'json',
//                success: function(response, status) {
//                    console.log(response);
//                },
//                dataSrc: function(response) {
//                    console.log(response);
//                }
//            }
//        } );


    }
} );
