(function (Gravity_Flow_Reports, $) {

    "use strict";

    var stepVars;

    $(document).ready(function () {
        if (typeof gravityflowFilterVars === 'undefined') {
            return;
        }

        stepVars = gravityflowFilterVars.config;
        var selectedVars = gravityflowFilterVars.selected;

        var formId = selectedVars.formId;

        $('#gravityflow-reports-category').toggle(formId ? true : false);

        if ( formId ) {
            var category = selectedVars.category;
            if ( category == 'step' ) {
                $('#gravityflow-reports-steps').html(getStepOptions(formId));
                var stepId = selectedVars.stepId;
                $('#gravityflow-reports-steps').val(stepId);
                $('#gravityflow-reports-steps').show();

                if ( stepId ) {
                    var assigneeVars = stepVars[formId][stepId].assignees;

                    $('#gravityflow-reports-assignees').html(getAssigneeOptions( assigneeVars ) );

                    $('#gravityflow-reports-assignees').val(selectedVars.assignee);
                    $('#gravityflow-reports-assignees').show();
                }
            }
        }

        $('#gravityflow-reports-filter form').on('submit', function(e){
            if (! $('body').hasClass('wp-admin')) {
                e.preventDefault();

                var reportWrapper = $(this).parents('.gravityflow_workflow_reports');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'gravityflow_render_reports',
                        nonce: $('#gravityflow-reports-nonce').val(),
                        args: $('#gravityflow-reports-args').val(),
                        data: $(this).serialize()
                    },
                    success: function (response) {
                        if(response.status === 'error'){
                            alert(response.message);
                        } else {
                            reportWrapper.find('#gravityflow-reports').html(response);
                            Gravity_Flow_Reports.drawCharts();
                        }
                    }
                });
            }
        });

        $('#gravityflow-form-drop-down').change(function(){
            $('#gravityflow-reports-category').toggle( this.value ? true : false);

            // Reset all the other dropdowns.
            if (this.value) {
                $('#gravityflow-reports-category').val('month');
            }
            $('#gravityflow-reports-steps').hide();
            $('#gravityflow-reports-assignees').hide();
        });
        $('#gravityflow-reports-category').change(function(){
            var formId = $('#gravityflow-form-drop-down').val();
            if ( this.value == 'step' ) {
                $('#gravityflow-reports-steps').html(getStepOptions(formId));
                $('#gravityflow-reports-steps').show();
            } else {
                $('#gravityflow-reports-assignees').hide();
                $('#gravityflow-reports-steps').hide();
            }
        });
        $('#gravityflow-reports-steps').change( function(){
            if ( this.value ) {
                var formId = $('#gravityflow-form-drop-down').val();
                var assigneeVars = stepVars[formId][this.value].assignees;
                $('#gravityflow-reports-assignees').html(getAssigneeOptions( assigneeVars ) );
                $('#gravityflow-reports-assignees').show();
            } else {
                $('#gravityflow-reports-assignees').hide();
            }
        });


    });

    function getStepOptions( formId ){
        var m = [];
        m.push( '<option value="">{0}</option>'.format( 'All Steps' ) );
        var steps = stepVars[formId];
        $.each( steps, function ( i, step ){
            m.push( '<option value="{0}">{1}</option>'.format(step.id, step.name ) );
        });
        return m.join('');
    }

    function getAssigneeOptions( assigneeVars ){
        var m = [];
        m.push( '<option value="">{0}</option>'.format( 'All Assignees' ) );
        for(var i=0; i < assigneeVars.length; i++) {
            m.push( '<option value="{0}">{1}</option>'.format(assigneeVars[i].key, assigneeVars[i].name ) );
        }
        return m.join('');
    }

    Gravity_Flow_Reports.drawCharts = function() {

        $('.gravityflow_chart').each(function () {
            var $this = $(this);
            var dataTable = $this.data('table');

            if (typeof dataTable !== 'undefined') {
                var data = google.visualization.arrayToDataTable(dataTable);

                var options = $this.data('options');

                var chartType = $this.data('type');

                var chart = new google.charts[chartType]( this );

                chart.draw(data, options);
            }
        })
    }

    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };

}(window.Gravity_Flow_Reports = window.Gravity_Flow_Reports || {}, jQuery));


google.load("visualization", "1.1", {packages:["bar"]});
google.setOnLoadCallback(Gravity_Flow_Reports.drawCharts);