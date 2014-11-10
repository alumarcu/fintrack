"use strict";
var View = function(viewName) {
    this.props = {};
    this.set = function(key, value) {
        this.props[key] = value;
    };
    this.get = function(key) {
        return this.props[key];
    };

    this.view = viewName;
    this.init = function() {
        $(document).ready(viewHandlers[viewName](this));
    }

};

var viewHandlers = {

    dashboard: function(view) {


        var swapAccounts = $('#transaction_swapAccounts'),
            addPartialScope = $('#transaction_addPartialScope'),
            lockTransaction = $('#transaction_lockTransaction'),
            viewScopes = view.get('scopes'),
            scopes = [];

        for (var i in viewScopes) {
            scopes.push(viewScopes[i].name);
        }

        $('.typeahead_scopes').typeahead({
            minLength: 1,
            highlight: true
        },
        {
            name: 'scopes',
            source: substringMatcher(scopes),
        });

        $('.select2').select2();

        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy',
            weekStart: 1
        });

        console.log(Routing.generate('pms_finance_dummy'));

        swapAccounts.click(function() {
            console.log('Swapping accounts');
        });

        addPartialScope.click(function() {
            console.log('Adding partial scope');
        });

        lockTransaction.click(function() {
            console.log('Locking/unlocking transaction');
        });
    },

    importer: function(view) {
        var importerContentTextarea = $('#importerContent'),
            importerDataHidden = $('#importerData'),
            importerCsvButton = $('#importerCsvButton'),
            importerForm = $('#dataImporter');

        importerCsvButton.click(function(e) {
            e.preventDefault();
            var content = importerContentTextarea.val(),
                csvLines = content.split('\n'),
                lineNum, line, data = [];

            for(lineNum in csvLines) {
                line = csvLines[lineNum].split(',');
                data.push({
                    date: line[0],
                    expense: parseFloat(line[1]),
                    income: parseFloat(line[2]),
                    account: parseInt(line[3])
                })
            }
            //importerContentTextarea.val('');
            importerDataHidden.val(JSON.stringify(data));
            importerForm.submit();
        });
    }
}


var substringMatcher = function(strs) {
    return function findMatches(q, cb) {

        var matches, substrRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
                // the typeahead jQuery plugin expects suggestions to a
                // JavaScript object, refer to typeahead docs for more info
                matches.push({ value: str });
            }
        });
        cb(matches)
    };
};

/*
date,expense,income
10-05-2014,0,1200,1
11-05-2014,250,0,1
 */