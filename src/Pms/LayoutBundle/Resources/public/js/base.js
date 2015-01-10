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

    Module  = easejs.AbstractClass( 'Module',
        {
            'abstract public onDocumentReady': [],
            'abstract public update': ['content', 'loader'],
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

            'public cacheElements': function() {
                for (var i in this._pageElements) {
                    var elem = this._pageElements[i];
                    this._properties[elem[0]] = $(elem[1]);
                }
            },
            'protected _onClick': function(elemKey, callback, context) {
                if (typeof context == 'undefined') {
                    this._properties[elemKey].off('click').click(callback);
                } else {
                    this._properties[elemKey].off('click').click(context, callback);
                }
            }
        } ),

    Page = easejs.Class( 'Page',
        {
            'protected _modules': [],

            'public attachModule': function( moduleInst ) {
                moduleInst.cacheElements();
                this._modules.push( moduleInst );
            },
            'public onDocumentReady': function() {
                for (var i in this._modules) {
                    this._modules[i].onDocumentReady();
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
