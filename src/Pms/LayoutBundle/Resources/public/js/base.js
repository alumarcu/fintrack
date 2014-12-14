"use strict";
var

    Config = easejs.Class( 'Config',
    {
        'public static typeahead': {
            minLength: 1,
            highlight: true
        },
        'public static datepicker': {
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy',
            weekStart: 1
        },
        'public static select2': {

        }
    } ),

    View  = easejs.AbstractClass( 'View',
    {
        // TODO: Rename to ViewElement which will be a building block for views!
        'abstract protected _onDocumentReady': [],
        'protected _properties': {},
        'protected _pageElements': [],

        'public set': function(key, value) {
            this._properties[key] = value;
        },
        'public get': function(key) {
            return this._properties[key];
        },
        'public getFirst': function(key) {
            if ($.isArray(this._properties[key])) {
                return this._properties[key][0]
            }
            return this._properties[key];
        },
        'public init': function() {
            this._cacheElements(this._pageElements);
            $(document).ready(this._onDocumentReady());
        },
        'protected _cacheElements': function( elementsList ) {
            for (var i in elementsList) {
                var elem = elementsList[i];
                this._properties[elem[0]] = $(elem[1]);
            }
        },
        'protected _onClick': function(elemKey, callback, context) {
            if (typeof context == 'undefined') {
                this._properties[elemKey].click(callback);
            } else {
                this._properties[elemKey].click(context, callback);
            }
        }
    } ),

    Tools = easejs.Class( 'Tools',
    {
        /**
         * Autocomplete method used with Typeahead.JS
         * @param terms
         * @returns {findMatches}
         */
        'public static getAutocomplete': function(terms) {
            return function findMatches(query, callback) {
                var matches, substrRegex;
                matches = [];
                substrRegex = new RegExp(query, 'i');
                $.each(terms, function(i, term) {
                    if (substrRegex.test(term)) {
                        matches.push({ value: term });
                    }
                });
                callback(matches)
            };
        }
    } );
