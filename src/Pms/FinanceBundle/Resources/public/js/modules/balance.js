"use strict";
var BalanceModule = easejs.Class('BalanceModule').extend( Module,
{
    'protected _pageElements': [

    ],

    'protected accountId': null,

    __construct: function( accountId ) {

        if (typeof accountId === "undefined" || isNaN(parseInt(accountId))) {
            console.log('Balance module incorrectly initialized; will not render!')
        }
        this.accountId = parseInt(accountId);

    },


    'public onDocumentReady': function() {
        console.log('LOADING MODULE: Balance');
        console.log(this.accountId);
        $('#table_id').DataTable(); // TODO - Read the docs and implement this!
        // TODO: Only one instance of this widget? With a select to change the account?

    },

    'public update': function(content, loader) {

    }
} );
