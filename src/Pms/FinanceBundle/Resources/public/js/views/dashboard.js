var DashboardView = easejs.Class('DashboardView').extend( View,
{
    'protected _pageElements': [
        ['aps'          , '#addPartialScope'],
        ['lt'           , '#lockTransaction'],
        ['lt-icon'      , '#lockTransaction span.fa'],
        ['mainScope'    , '#scope'],
        ['mainValue'    , '#value'],
        ['qs'           , '.quickScope'],
        ['dp'           , '.datepicker'],
        ['sel2'         , '.select2'],
        ['psRowsTarget' , '#partialScopes'],
        ['psCellTmpl'   , '#partialScopeCell'],
        // source and destination accounts selects and swap button
        ['sa'           , '#swapAccounts'],
        ['accDestination', '#destinationAccount'],
        ['accSource'     , '#sourceAccount']
    ],

    'private _scopeOptions': null,
    'public counter': 0,

    'protected _onDocumentReady': function() {
        this.initTypeahead('mainScope');
        this.initDatepicker('dp');
        this.initSelect('sel2');
        this._onClick('qs', this.__self._clickedAddPartialScope, this);
        this._onClick('aps', this.__self._clickedAddPartialScope, this);
        this._onClick('sa', this.__self._clickedSwapAccounts, this);
        this._onClick('lt', this.__self._clickedLockTransaction, this);

    },
    'public initTypeahead': function(elementName, notCached) {
        var element = null;

        if (this._scopeOptions == null) {
            this._scopeOptions = [];
            for (var i in this.get('scopes')) {
                this._scopeOptions.push(this.get('scopes')[i].name);
            }
        }

        if (notCached == true) {
            element = $(elementName);
        } else {
            element = this.getFirst(elementName);
        }
        element.typeahead(Config.$('typeahead'), { source: Tools.getAutocomplete(this._scopeOptions) });
    },
    'public initDatepicker': function(elementName) {
        this.getFirst(elementName).datepicker(Config.$('datepicker'));
    },
    'public initSelect': function(elementName) {
        this.getFirst(elementName).select2(Config.$('select2'));
    },
    'public lockTransaction': function() {
        var mainval, mainscope, parsedVal, errors;
        if (this.get('lt-icon').hasClass('fa-lock')) {
            return true; // already locked
        }

        mainval = this.get('mainValue');
        mainscope = this.get('mainScope');
        parsedVal = parseFloat(mainval.val());
        errors = [];

        if (isNaN(parsedVal)) {
            errors.push('Value not defined or incorrect');
            mainval.val('');
        }
        if (mainscope.val().length < 3) {
            errors.push('Scope missing or too short');
        }

        if(errors.length == 0) {
            mainval.val(parsedVal);
            mainval.prop('disabled', true);
            mainscope.prop('disabled', true);
            this.get('lt-icon').removeClass('fa-unlock');
            this.get('lt-icon').addClass('fa-lock');
            return true;
        } else {
            alert('Please correct the following: ' + errors.join('; '));
            return false;
        }
    },
    'public unlockTransaction': function() {
        var mainval, mainscope;
        if (this.get('lt-icon').hasClass('fa-unlock')) {
            return true; // already unlocked
        }

        mainval = this.get('mainValue');
        mainscope = this.get('mainScope');

        mainval.prop('disabled', false);
        mainscope.prop('disabled', false);
        this.get('lt-icon').removeClass('fa-lock');
        this.get('lt-icon').addClass('fa-unlock');
        // Would return false if transaction could not be
        // unlocked, for which there are no cases yet
        return true;
    },
    'private static _clickedAddPartialScope': function(event) {
        var clicked = $(event.target),
            __this = event.data,
            partialScopeId = 'ps-' + ++__this.counter,
            clickedText = '',
            template, newPartialScopeRow, lockResult = true;

        if (__this.get('lt-icon').hasClass('fa-unlock')) {
            lockResult = __this.lockTransaction();
        }
        if (!lockResult) return;

        if ( clicked.attr('id') != __this.getFirst('aps').attr('id') )  {
            clickedText = clicked.text();
        }

        template = __this.getFirst('psCellTmpl');
        template = template.html();

        Mustache.parse(template);
        newPartialScopeRow = Mustache.render(template, {
            psId: partialScopeId,
            clickedScope: clickedText
        });

        __this.get('psRowsTarget').append(newPartialScopeRow);
        __this.initTypeahead('#' + partialScopeId + ' .partialScopeName', true);

        $('#' + partialScopeId + ' .closePartialScope').click(function() {
            $('#' + partialScopeId).remove();
        });
    },
    'private static _clickedSwapAccounts': function(event) {
        var __this = event.data, temp;

        temp = __this.getFirst('accDestination').val();
        __this.getFirst('accDestination').val(__this.getFirst('accSource').val());
        __this.getFirst('accSource').val(temp);

        __this.getFirst('accDestination').trigger('change');
        __this.getFirst('accSource').trigger('change');
    },
    'private static _clickedLockTransaction': function(event) {
        var __this = event.data;
        if (__this.get('lt-icon').hasClass('fa-unlock')) {
            __this.lockTransaction();
        } else {
            // TODO: Should remove all partial scopes, but will do validation instead...
            __this.unlockTransaction();
        }
    }
} );

