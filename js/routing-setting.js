;(function ( GFRoutingSetting, $, undefined ) {

    "use strict";

    // Create the defaults once
    var pluginName = "gfRoutingSetting",
        defaults = {
            prefix: "",
            allowMultiple: true,
            imagesURL: "",
            operatorStrings: {"is":"is","isnot":"isNot", ">":"greaterThan", "<":"lessThan", "contains":"contains", "starts_with":"startsWith", "ends_with":"endsWith"},
            items: [ {
                target: '',
                fieldId: '0',
                operator: 'is',
                value: '',
                type: '',
            } ],
            callbacks: {
                addNewTarget: function() { },
                header: function() { return '<thead><tr><th>Assign To</th><th colspan="3">Condition</th></tr></thead>';}
            }
        };

    // The plugin constructor
    function Plugin( element, options ) {
        this.element = element;
        this.$element = $(element);

        this.options = $.extend( true, {}, defaults, options) ;

        this.prefix = options.prefix;
        this.settings = options.settings;
        this.accounts = options.accounts;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function() {

            var t = this;

            var routingsMarkup, headerMarkup;
            headerMarkup = this.getHeaderMarkup();
            routingsMarkup = '<table class="gform-routings">{0}<tbody class="repeater">{1}</tbody></table>'.format(headerMarkup, this.getNewRoutingRow());

            var $routings = $(routingsMarkup);
            $routings.find('.repeater').repeater({

                    limit: 0,
                    items: this.options.items,
                    addButtonMarkup: '<img class="gform-add" src="{0}/images/add.png" />'.format(gf_vars.baseUrl),
                    removeButtonMarkup: '<img class="gform-remove" src="{0}/images/remove.png" />'.format(gf_vars.baseUrl),
                    callbacks: {
                        save: function( obj, data ) {
                            $('#' + t.options.fieldId).val( $.toJSON( data ) );
                        },
                        beforeAdd: function( obj, $elem, item){
                            var $target = $elem.find('.gform-routing-target');
                            $target.val(item.target);

                            var $field = $elem.find('.gform-routing-field').first();
                            $field.value = item.fieldId;
                            t.changeField($field);

                            var $operator = $elem.find('.gform-routing-operator').first();

                            $operator.value = item.operator;

                            t.changeOperator($operator);

                            var $value = $elem.find('.gform-routing-value');
                            $value.val(item.value);

                        },
                    }
                })
                .on('change', '.gform-routing-field', function(e){
                    t.changeField(this);
                })
                .on('click', '.gform-no-filters', function(e){
                    var $this = $(this);
                    var $row = $this.find('.gform-routing');
                    if($row.length == 0){
                        t.addNewRouting(this);
                    }
                    $this.remove();
                    e.preventDefault();
                })
                .on('change', '.gform-routing-operator', function(){
                    t.changeOperator(this);
                });

            this.$element.append($routings);

            /*
            if (typeof filters == 'undefined' || filters.length == 0){
                t.displayNoRoutingsMessage();
                return;
            }

            t.$element.find(".gform-routing-field").each(function (i) {
                var fieldId = filters[i].field;
                $(this).val(fieldId);
                t.changeField(this);
            });
            t.$element.find(".gform-routing-operator").each(function (i) {
                var operator = filters[i].operator;
                $(this).val(operator);
                t.changeOperator(this, this.value);
            });

            t.$element.find(".gform-routing-value").each(function (i) {
                var value = filters[i].value;
                $(this).val(value);
                $(this).change();
            });

            var i;

            for (i = 0; i < filters.length; i++) {
                t.$element.find(".gform-routings").append(this.getNewRoutingRow(filters[i].routeId));
            }
            */
        },

        getHeaderMarkup: function() {

            var header = this.options.callbacks.header( this, '' );
            return header;
        },

        getNewRoutingRow: function () {
            var r = [];

            r.push( '<td>{0}</td>'.format( this.getRoutingTarget() ) );
            r.push( '<td>{0}</td>'.format( this.getRoutingFields() ) );
            r.push( '<td>{0}</td>'.format( this.getRoutingOperators( this.options.settings[0] ) ) );
            r.push( '<td>{0}</td>'.format( this.getRoutingValues() ) );
            r.push( '<td>{buttons}</td>' );

            return '<tr class="gform-routing-row">{0}</tr>'.format( r.join('') );
        },

        getRoutingTarget: function () {
            var target = '<input type="text" class="gform-routing-target target_{i}">';
            target = this.options.callbacks.addNewTarget( this, target );
            return target;
        },

        getRoutingFields: function () {
            var i, j, key, val, label, groupLabel, options, numRows,
                select = [],
                settings = this.settings;
            select.push('<select class="gform-routing-field fieldId_{i}" >');
            for (i = 0; i < settings.length; i++) {
                key = settings[i].key;
                if (settings[i].group) {
                    groupLabel = settings[i].text;
                    numRows = settings[i].filters.length;
                    options = [];
                    for (j = 0; j < numRows; j++) {
                        label = settings[i].filters[j].text;
                        val = settings[i].filters[j].key;
                        options.push('<option value="{0}">{1}</option>'.format(val, label));
                    }
                    select.push('<optgroup label="{0}">{1}</optgroup>'.format(groupLabel, options.join('')));
                } else {
                    label = settings[i].text;
                    select.push('<option value="{0}">{1}</option>'.format(key, label));
                }

            }
            select.push("</select>");
            select.push('<input type="hidden" class="gform-filter-type" name="type_{i}" value="" >');
            return select.join('');
        },

        changeOperator: function  (operatorSelect) {
            var $select = $(operatorSelect),
                $buttons = $select.closest('tr').find('.repeater-buttons');
            var index = $buttons.find('.add-item ').data('index');
            var $fieldSelect = $select.closest('tr').find('.gform-routing-field');
            var filter = this.getFilter($fieldSelect.value);
            if (filter) {
                $select.closest('tr').find(".gform-routing-value").replaceWith(this.getRoutingValues(filter, operatorSelect.value, index));
            }
        },

        changeField: function  (fieldSelect) {
            var filter = this.getFilter(fieldSelect.value);
            if (filter) {
                var $select = $(fieldSelect),
                    $buttons = $select.closest('tr').find('.repeater-buttons');
                var index = $buttons.find('.add-item ').data('index');
                $select.closest('tr').find(".gform-routing-value").replaceWith(this.getRoutingValues(filter, null, index));
                $select.closest('tr').find(".gform-filter-type").val(filter.type).change();
                var $newOperators = $(this.getRoutingOperators(filter, index));
                $select.closest('tr').find(".gform-routing-operator").replaceWith($newOperators);
                $select.closest('tr').find(".gform-routing-operator").change();
            }
        },

        getRoutingOperators: function (filter, index) {
            if ( typeof index == 'undefined' || index === null ){
                index = '{i}';
            }
            var i, operator,
                operatorStrings = this.options.operatorStrings,
                str = '<select class="gform-routing-operator operator_{0}">'.format(index);

            if (filter) {
                for (i = 0; i < filter.operators.length; i++) {
                    operator = filter.operators[i];
                    str += '<option value="{0}">{1}</option>'.format(operator, gf_vars[operatorStrings[operator]] );
                }
            }
            str += "</select>";
            return str;
        },

        getRoutingValues: function  (filter, selectedOperator, index) {
            var i, val, text, str, options = "";

            if ( typeof index == 'undefined' || index === null ){
                index = '{i}';
            }

            if ( filter && filter.values && selectedOperator != 'contains' ) {
                for (i = 0; i < filter.values.length; i++) {
                    val = filter.values[i].value;
                    text = filter.values[i].text;
                    options += '<option value="{0}">{1}</option>'.format(val, text);
                }
                str = '<select class="gform-routing-value value_{0}">{1}</select>'.format(index, options);
            } else {
                str = '<input type="text" value="" class="gform-routing-value value_{0}" />'.format(index);
            }

            /**
             * Filter the markup rendered for the Conditional Routing's value select.
             *
             * @param str    str              The markup to be rendered.
             * @param object filter           An object containing the properties of the current condition.
             * @param str    selectedOperator The operator that is selected for the current condition.
             * @param int    index            The index of the current condition.
             *
             * @since x.x.x
             */
            return gform.applyFilters( 'gravityflow_routing_values_markup', str, filter, selectedOperator, index );
        },

        getFilter: function  (key) {
            var settings = this.settings;
            if (!key)
                return;
            for (var i = 0; i < settings.length; i++) {
                if (key == settings[i].key)
                    return settings[i];
                if (settings[i].group) {
                    for (var j = 0; j < settings[i].filters.length; j++) {
                        if (key == settings[i].filters[j].key)
                            return settings[i].filters[j];
                    }
                }

            }
        },

        selected: function (selected, current){
            return selected == current ? 'selected="selected"' : "";
        },

    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Plugin( this, options ));
            }
        });
    };

    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

})( window.GFRoutingSetting = window.GFRoutingSetting || {}, jQuery );

