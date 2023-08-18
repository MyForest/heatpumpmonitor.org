<?php
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');
?>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .quality-bound {
        background-color: #ddd;
        padding:5px;
    }
    .quality {
        width: 100%;
    }
    .quality td {
        padding: 5px;
        text-align: center;
        border: 1px solid #fff;
        color: #fff;
        font-size: 14px;
    }
</style>

<div id="app" class="bg-light">
    <div style=" background-color:#f0f0f0; padding-top:20px; padding-bottom:10px">
        <div class="container" style="max-width:800px;">
            <button class="btn btn-primary" style="float:right" @click="open_emoncms_dashboard">Open Emoncms Heat Pump Dashboard</button>
            <h3>{{ system.location }}</h3>
        </div>
    </div>

    <div class="container mt-3" style="max-width:800px">


        <div class="card mt-3" v-if="last30.since!=last365.since">
            <h5 class="card-header">Last {{last365.since | formatDays }} days</h5>
            <div class="card-body">
                <div class="row" style="text-align:center">
                    <div class="col">
                        <h5>Electric</h5>
                        <h4>{{ last365.elec_kwh }} kWh</h4>
                    </div>
                    
                    <div class="col">
                        <h5>Heat Output</h5>
                        <h4>{{ last365.heat_kwh }} kWh</h4>
                    </div>
                    
                    <div class="col">
                        <h5>SCOP</h5>
                        <h4>{{ last365.cop }}</h4>            
                    </div>    
                </div>      
            </div>
        </div>
        <div class="card mt-3">
        <h5 class="card-header">Last {{last30.since | formatDays }} days</h5>
            <div class="card-body">
                <div class="row" style="text-align:center">
                    <div class="col">
                        <h5>Electric</h5>
                        <h4>{{ last30.elec_kwh }} kWh</h4>
                    </div>
                    
                    <div class="col">
                        <h5>Heat Output</h5>
                        <h4>{{ last30.heat_kwh }} kWh</h4>
                    </div>
                    
                    <div class="col">
                        <h5>SCOP</h5>
                        <h4>{{ last30.cop }}</h4>            
                    </div>    
                </div>
                <hr>
                <div class="row" style="text-align:center">
                    <div class="col">
                        Stats when running
                    </div>  
                </div>
                <div class="row mt-2" style="text-align:center">
                    <div class="col">
                        <b>Electric</b><br>
                        {{ last30.when_running_elec_kwh }} kWh
                    </div>  
                    <div class="col">
                        <b>Heat</b><br>
                        {{ last30.when_running_heat_kwh }} kWh
                    </div>
                    <div class="col">
                        <b>COP</b><br>
                        {{ last30.when_running_cop }}
                    </div>  
                    <div class="col">
                        <b>FlowT</b><br>
                        {{ last30.when_running_flowT }} °C
                    </div>  
                    <div class="col">
                        <b>OutsideT</b><br>
                        {{ last30.when_running_outsideT }} °C
                    </div> 
                    <div class="col">
                        <b>Carnot</b><br>
                        {{ last30.when_running_carnot_prc }}%
                    </div> 
                </div>      
            </div>
        </div>

        <div class="card mt-3">
            <h5 class="card-header">Monthly data</h5>
            <div class="card-body">
                <div class="input-group mb-3"> 
                <span class="input-group-text">Chart mode</span>
                    <select class="form-control" v-model="chart_yaxis" @change="change_chart_mode">
                        <option v-for="(field,key) in system_stats_monthly" v-if="field.name" :value="key"> {{ field.name }} </option>
                    </select>
                </div>
                <div id="chart"></div>
            </div>
        </div>    

        <div class="card mt-3">
            <h5 class="card-header">Data Quality</h5>
            <div class="card-body">
                <p>100% is full data coverage, 0% is no data</p>
                <div class="quality-bound">
                <table class="quality">
                    <tr>
                        <td></td>
                        <td v-for="month in monthly">
                        {{ month.timestamp | monthName }}
                        </td>
                    </tr>
                    <tr>
                        <td>Elec</td>
                        <td v-for="month in monthly" :style="{ backgroundColor: qualityColor(month.quality_elec) }">
                        {{ month.quality_elec }}
                        </td>
                    </tr>
                    <tr>
                        <td>Heat</td>
                        <td v-for="month in monthly" :style="{ backgroundColor: qualityColor(month.quality_heat) }">
                            {{ month.quality_heat }}
                        </td>
                    </tr>
                    <tr>
                        <td>Flow</td>
                        <td v-for="month in monthly" :style="{ backgroundColor: qualityColor(month.quality_flow) }">
                            {{ month.quality_flow }}
                        </td>
                    </tr>
                    <tr>
                        <td>Return</td>
                        <td v-for="month in monthly" :style="{ backgroundColor: qualityColor(month.quality_return) }">
                            {{ month.quality_return }}
                        </td>
                    </tr>
                    <tr>
                        <td>Outside</td>
                        <td v-for="month in monthly" :style="{ backgroundColor: qualityColor(month.quality_outside) }">
                            {{ month.quality_outside }}
                        </td>
                    </tr>
                </tr>
                </table>
            </div>
            </div>
        </div>   

    </div>

    <div class="container mt-3" style="max-width:800px">
        <div class="row">
            <h4>Form data</h4>
            <p>Information about this system...</p>
            <table class="table">
                <tbody v-for="group,group_name in schema_groups">
                    <tr>
                        <th style="background-color:#f0f0f0;">{{ group_name }}</th>
                        <td style="background-color:#f0f0f0;"></td>
                        <td style="background-color:#f0f0f0;"></td>
                    </tr>
                    <tr v-for="(field,key) in group" v-if="field.editable && key!='share'">
                        <td>
                            <span>{{ field.name }}</span> <span v-if="!field.optional && mode=='edit'" style="color:#aa0000">*</span>
                        </td>
                        <td>
                            <span v-if="field.helper" data-bs-toggle="tooltip" data-bs-placement="top" :title="field.helper">
                                <i class="fas fa-question-circle"></i>
                            </span>
                        </td>
                        <td>
                            <span v-if="field.type=='tinyint(1)'">
                                <input type="checkbox" v-model="system[key]" :disabled="mode=='view'">
                            </span>
                            <span v-if="field.type!='tinyint(1)'">
                                <!-- Edit mode text input -->
                                <div class="input-group" v-if="mode=='edit' && !field.options">
                                    <input class="form-control" type="text" v-model="system[key]">
                                    <span class="input-group-text" v-if="field.unit">{{ field.unit }}</span>
                                </div>
                                <!-- View mode select input -->
                                <select class="form-control" v-if="mode=='edit' && field.options" v-model="system[key]">
                                    <option v-for="option in field.options">{{ option }}</option>
                                </select>
                                <!-- View mode text -->
                                <span v-if="mode=='view'">{{ system[key] }}</span>
                            </span>
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>
    </div>
    <div style=" background-color:#eee; padding-top:20px; padding-bottom:10px" v-if="mode=='edit'">
        <div class="container" style="max-width:800px;">
            <div class="row">
                <div class="col">
                    <p><b>Agree to share this information publicly</b></p>
                </div>
                <div class="col">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="system.share">
                        <label>Yes</label>
                    </div>
                </div>
            </div>
            <div class="row" v-if="admin">
                <div class="col">
                    <p><b>Publish system (Admin only)</b></p>
                </div>
                <div class="col">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="system.published">
                        <label>Yes</label>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary" @click="save">Save</button>
            <button type="button" class="btn btn-light" @click="cancel" style="margin-left:10px">Cancel</button>
            <br><br>

            <div class="alert alert-danger" role="alert" v-if="show_error" v-html="message"></div>
            <div class="alert alert-success" role="alert" v-if="show_success">
                <span v-html="message"></span>
                <button type="button" class="btn btn-light" @click="cancel" v-if="show_success">Back to system list</button>
            </div>
        </div>
    </div>
</div>
<script>
    var schema = <?php echo json_encode($schema); ?>;
    // arrange by group
    var schema_groups = {};
    for (var key in schema) {
        if (schema[key].group) {
            if (!schema_groups[schema[key].group]) {
                schema_groups[schema[key].group] = {};
            }
            schema_groups[schema[key].group][key] = schema[key];
        }
    }

    var app = new Vue({
        el: '#app',
        data: {
            mode: "<?php echo $mode; ?>", // edit, view
            system: <?php echo json_encode($system_data); ?>,
            monthly: [],
            last30: [],
            last365: [],
            schema_groups: schema_groups,

            show_error: false,
            show_success: false,
            message: '',
            admin: <?php echo $admin ? 'true' : 'false'; ?>,

            chart_yaxis: 'cop',
            system_stats_monthly: <?php echo json_encode($system_stats_monthly); ?>
        },
        computed: {
            qualityColor() {
                return function(score) {
                    const hue = (score / 100) * 120; // Map score to hue value (0-120)
                    // if score = 0 grey
                    if (score == 0) return '#ccc';
                    return `hsl(${hue}, 100%, 50%)`; // Convert hue value to HSL color
                }
            }
        },
        filters: {
            monthName: function(timestamp) {
                var date = new Date(timestamp * 1000);
                var month = date.toLocaleString('default', { month: 'short' });
                return month;
            },
            formatDays: function(timestamp) {
                // days ago
                var date = new Date(timestamp * 1000);
                var today = new Date();
                var diff = today - date;
                var days = diff / (1000 * 60 * 60 * 24);
                return Math.round(days);
            }
        },
        methods: {
            save: function() {
                // Send data to server using axios, check response for success
                axios.post('save', {
                        id: this.$data.system.id,
                        data: this.$data.system
                    })
                    .then(function(response) {
                        if (response.data.success) {
                            app.show_success = true;
                            app.show_error = false;
                            app.message = response.data.message;

                            var list_items = "";

                            if (response.data.change_log != undefined) {
                                let change_log = response.data.change_log;
                                // Loop through change log add as list
                                for (var i = 0; i < change_log.length; i++) {
                                    list_items += "<li><b>" + change_log[i]['key'] + "</b> changed from <b>" + change_log[i]['old'] + "</b> to <b>" + change_log[i]['new'] + "</b></li>";
                                }
                            }

                            if (response.data.warning_log != undefined) {
                                let warning_log = response.data.warning_log;
                                // Loop through change log add as list
                                for (var i = 0; i < warning_log.length; i++) {
                                    list_items += "<li>" + warning_log[i]['message'] + "</li>";
                                }
                            }

                            if (list_items) {
                                app.message = "<br><ul>" + list_items + "</ul>";
                            }

                            if (response.data.new_system != undefined && response.data.new_system) {
                                window.location.href = 'edit?id=' + response.data.new_system;
                            }
                        } else {
                            app.show_error = true;
                            app.show_success = false;
                            app.message = response.data.message;

                            if (response.data.error_log != undefined) {
                                let error_log = response.data.error_log;
                                app.message = 'Could not save form data<br><br><ul>';
                                // Loop through change log add as list
                                for (var i = 0; i < error_log.length; i++) {
                                    app.message += "<li><b>" + error_log[i]['key'] + "</b>: " + error_log[i]['message'] + "</li>";
                                }
                                app.message += '</ul>';
                            }
                        }
                    });
            },
            cancel: function() {
                window.location.href = path + 'system/list/public';
            },
            change_chart_mode: function() {
                console.log(app.chart_yaxis);
                draw_chart();
            },
            open_emoncms_dashboard: function() {
                window.open(app.system.url);
            },
        },
    });


    // CHART

    chart_options = {
        colors_style_guidlines: ['#29ABE2'],
        colors: ['#29AAE3'],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        series: [],
        xaxis: {
            categories: [],
            type: 'datetime'
        },
        yaxis: {
            title: {
                text: 'COP'
            }
        }
    };
    chart = new ApexCharts(document.querySelector("#chart"), chart_options);
    chart.render();

    axios.get(path + 'system/monthly?id=' + app.system.id)
        .then(function(response) {
            app.monthly = response.data;
            draw_chart();

        })
        .catch(function(error) {
            console.log(error);
        });

    axios.get(path + 'system/stats/last365?id=' + app.system.id)
        .then(function(response) {
            app.last365 = response.data[app.system.id];
        })
        .catch(function(error) {
            console.log(error);
        });

    axios.get(path + 'system/stats/last30?id=' + app.system.id)
        .then(function(response) {
            app.last30 = response.data[app.system.id];
        })
        .catch(function(error) {
            console.log(error);
        });

    function draw_chart() {
        var x = [];
        var y = [];

        // 12 months of dummy data peak in winter
        for (var i = 0; i < app.monthly.length; i++) {
            x.push(app.monthly[i]['timestamp'] * 1000);
            y.push(app.monthly[i][app.chart_yaxis]);
        }

        chart_options.xaxis.categories = x;
        chart_options.series = [{
            name: app.system_stats_monthly[app.chart_yaxis].name,
            data: y
        }];

        chart.updateOptions(chart_options);
    }
</script>