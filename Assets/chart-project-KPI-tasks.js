KB.component('chart-project-KPI-tasks', function (containerElement, options) {

    this.render = function () {
        var columns = [];
        var tsk_planned = [];
        var tsk_completed = [];
        var tsk_percent = [];
        var x_labels = [];

        //Add labels for the legend
        tsk_planned.push('tasks planned');
        tsk_completed.push('tasks completed');
        tsk_percent.push('percentage');

        for (var i = 0; i < options.metrics.length; i++) {
            //columns.push([options.metrics[i].column_title, options.metrics[i].nb_tasks]);
            x_labels.push(options.metrics[i].column_title);
            tsk_planned.push(options.metrics[i].nb_tasks);
            tsk_completed.push(options.metrics[i].nb_tasks_complete);
            tsk_percent.push(options.metrics[i].nb_tasks_percent);
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

        c3.generate({
            bindto: '#KPI-tasks',
            data: {
                //columns: tsk_planned, tsk_completed,
                columns: [
                    tsk_planned, //['hours planned', 3, 4, 5],
                    tsk_completed, //['hours used', 1, 6, 5]
                    tsk_percent
                ],
                axes: {
                    'tasks': 'y',
                    'tasks completed': 'y',
                    'percentage': 'y2'
                },
                type : 'bar',
                types: {
                    'percentage': 'line'
                }
            },
            title: {
                text: 'Tasks - Planned vs. Completed'
            },
            axis: {
                x: {
                    type: 'category',
                    categories: x_labels
                },
                y: {
                    label: '# Tasks',
                },
                y2: {
                    show: true,
                    label: 'Percentage',
                    min: 0,
                    max: 100
                }
            },
            grid: {
                y: {
                    show: true,
                    lines: [
                        {value: 90, axis: 'y2', class: 'line10', text: 'Min Limit', position: 'middle'},
                    ]
                }
            }
        });
    };
});
