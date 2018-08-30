KB.component('chart-project-KPI-hours', function (containerElement, options) {

    this.render = function () {
        var columns = [];
        var hrs_planned = [];
        var hrs_used = [];
        var hrs_percent = [];
        var x_labels = [];

        //Add labels for the legend
        hrs_planned.push('hours planned');
        hrs_used.push('hours used');
        hrs_percent.push('percentage');

        for (var i = 0; i < options.metrics.length; i++) {
            //columns.push([options.metrics[i].column_title, options.metrics[i].nb_tasks]);
            x_labels.push(options.metrics[i].column_title);
            hrs_planned.push(options.metrics[i].nb_hrs_plan);
            hrs_used.push(options.metrics[i].nb_hrs_used);
            hrs_percent.push(options.metrics[i].nb_hrs_percent);
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

        c3.generate({
            bindto: '#KPI-hours',
            data: {
                //columns: hrs_planned, hrs_used,
                columns: [
                    hrs_planned, //['hours planned', 3, 4, 5],
                    hrs_used, //['hours used', 1, 6, 5]
                    hrs_percent
                ],
                axes: {
                    'hours planned': 'y',
                    'hours used': 'y',
                    'percentage': 'y2'
                },
                type : 'bar',
                types: {
                    'percentage': 'line'
                }
            },
            title: {
                text: 'Hours - Planned vs. Used'
            },
            axis: {
                x: {
                    type: 'category',
                    categories: x_labels
                },
                y: {
                    label: 'Hours',
                },
                y2: {
                    show: true,
                    label: 'Percentage',
                    min: 0,
                    max: 140
                }
            },
            grid: {
                y: {
                    show: true,
                    lines: [
                        {value: 80, axis: 'y2', class: 'line10', text: 'Min Limit', position: 'middle'},
                        {value: 120, axis: 'y2', class: 'line10', text: 'Max Limit', position: 'middle'},
                    ]
                }
            }
        });
    };
});
