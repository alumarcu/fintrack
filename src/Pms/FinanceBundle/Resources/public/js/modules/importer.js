"use strict"
var importer = function(view) {
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