<?php
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');
?>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>

<div id="app" class="bg-light">
    <div style=" background-color:#f0f0f0; padding-top:20px; padding-bottom:10px">
        <div class="container" style="max-width:800px;">
            <h3>Heat Pump Monitoring Submission</h3>
            <p>If you have a heat pump and publish stats via emoncms, submit your details here.</p>
        </div>
    </div>

    <div class="container" style="max-width:800px">
        <br>
        <div class="row">
            <div class="col">
                <p><b>Vague Location</b><br>Roughly where the heat pump is installed, to nearest city or county.</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.location" @change="update">
                </div>
            </div>
        </div>
        <div class="row">
            <p><b>Installer</b><br>Optional. If you are not the installer we recommend asking the installer if they are happy with their name being displayed. Self install is also an option..</p>
            <div class="col">
                <p><b>Name</b></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.installer_name" @change="update">
                </div>
            </div>
            <div class="col">
                <p><b>Website</b></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.installer_url" @change="update">
                </div>
            </div>
        </div>

        <hr>
        <h4>About Your Heating System</h4>

        <div class="row">
            <div class="col">
                <p><b>Heat Pump Make / Model</b></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.hp_model" @change="update">
                </div>
            </div>
            <div class="col">
                <p><b>Heat Pump Type</b></p>
                <div class="input-group mb-3">
                    <select class="form-control" @change="update" v-model="system.hp_type">
                        <option>Air Source</option>
                        <option>Ground Source</option>
                        <option>Water Source</option>
                        <option>Air-to-Air</option>
                        <option>Other</option>
                    </select>
                </div>

            </div>
        </div>
        <div class="row">

            <div class="col">
                <p><b>Refrigerant type</b><br>(e.g R410a, R32, R290)</p>
                <div class="input-group mb-3">
                    <select class="form-control" @change="update" v-model="system.refrigerant">
                        <option value="R290">R290 (Propane)</option>
                        <option value="R32">R32</option>
                        <option value="CO2">CO2</option>
                        <option value="R410A">R410a</option>
                        <option value="R134A">R134a</option>
                        <option value="R407C">R407c</option>
                    </select>
                </div>
            </div>

            <div class="col">
                <p><b>Heat Output</b><br>Maximum rated heat output</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.hp_output" @change="update">
                    <span class="input-group-text">kW</span>
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Does the system include any of the following hydraulic separation:</b></p>

            <div class="col">
                <select class="form-control" @change="update" v-model="system.hydraulic_separation">
                    <option>None</option>
                    <option>Low loss header</option>
                    <option>Buffer</option>
                    <option>Plate heat exchanger</option>
                    <option>Don't know</option>
                </select>
            </div>
        </div>
        <br>


        <div class="row">
            <p><b>Type of heat emitters installed</b></p>

            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.new_radiators" @click="update">
                    <label class="form-check-label">
                        New radiators
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.old_radiators" @click="update">
                    <label class="form-check-label">
                        Existing radiators
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.fan_coil_radiators" @click="update">
                    <label class="form-check-label">
                        Fan-coil radiators
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.UFH" @click="update">
                    <label class="form-check-label">
                        Underfloor heating
                    </label>
                </div>
                <br>
            </div>
        </div>

        <hr>
        <h4>System Control</h4>
    
        <div class="row">
            <p><b>Weather compensation</b></p>
            <div class="col">
                <p>Flow temperature of heat emitters at design temperature (e.g 45C at -3C outside)</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.flow_temp" @change="update">
                    <span class="input-group-text">°C</span>
                </div>
            </div>
            <div class="col">
                <p>Typical flow temperature of heat emitters in January (e.g 35C at 6C outside)</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.flow_temp_typical" @change="update">
                    <span class="input-group-text">°C</span>
                </div>
            </div>
            <div class="col">
                <p>Curve setting<br>(if known e.g 0.6)<br><br></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="system.wc_curve" @change="update">
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Zones</b></p>
            <div class="col">
                <p>Number of zones</p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.zone_number" @change="update">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10+</option>
                    </select>
                </div>
            </div>
            <div class="col-9" v-if="system.zone_number!=1">
                <p>How are the zones controlled?</p>
                <div class="input-group mb-3">
                <input type="text" class="form-control" v-model="system.zone_notes" @change="update">
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Space heating control settings</b></p>
            <div class="col">
                <p>Select type that best describes your system</p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.space_heat_control_type" @change="update">
                        <option value="pure_wc">Pure weather compensation, no room influence</option>
                        <option value="wc_room_min">Weather compensation with a little room influence</option>
                        <option value="wc_room_max">Weather compensation with significant room influence</option>
                        <option value="room_only">Room influence only (e.g Auto adapt)</option>
                        <option value="manual_flow">Manual flow temperature control</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <p>Feel free to add further details...</p>
                <div class="input-group mb-3">
                <input type="text" class="form-control" v-model="system.space_heat_control_notes" @change="update">
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Water heating control settings</b></p>
            <div class="col-8">
                <p>Select control settings that best describes your system</p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.dhw_control_type" @change="update">
                        <option value="schedule1">Daily scheduled heat up of tank</option>
                        <option value="schedule2">Twice daily scheduled heat up of tank</option>
                        <option value="topup5">Automatic top up of tank if temperature drops by 3-6C</option>
                        <option value="topup10">Automatic top up of tank if temperature drops by 6-10C</option>
                        array('Daily scheduled heat up of tank', 'Twice daily scheduled heat up of tank', 'Automatic top up of tank if temperature drops by 3-6C', 'Automatic top up of tank if temperature drops by 6-10C', 'Manual control of tank temperature');
                    </select>
                </div>
            </div>
            <div class="col-4">
                <p>Target water temperature</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.dhw_target_temperature" @change="update">
                    <span class="input-group-text">°C</span>
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Legionella protection settings</b></p>
            <div class="col-8">
                <p>Legionella cycle frequency</p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.legionella_frequency" @change="update">
                        <option>Daily</option>
                        <option>Weekly</option>
                        <option>Fornightly</option>
                        <option>Monthly</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <p>Target water temperature</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.legionella_target_temperature" @change="update">
                    <span class="input-group-text">°C</span>
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Anti-freeze protection</b></p>

            <div class="col">
                <div class="input-group mb-3">
                    <select class="form-control" @change="update" v-model="system.freeze">
                        <option>Glycol/water mixture</option>
                        <option>Anti-freeze valves</option>
                        <option>Central heat pump water circulation</option>
                    </select>
                </div>
            </div>
        </div>

        <p><b>Additional notes</b></p>
        <div class="input-group mb-3">
            <input type="text" class="form-control" v-model="system.notes" @change="update" placeholder="Any additional notes about your system...">
        </div>

        <hr>
        <h4>About Your Property</h4>
        <br>

        <div class="row">
            <div class="col">
                <p><b>Type of property</b></p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.property">
                        <option>Detached</option>
                        <option>Semi-detached</option>
                        <option>End-terrace</option>
                        <option>Mid-terrace</option>
                        <option>Flat / appartment</option>
                        <option>Bungalow</option>
                        <option>Office building</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <p><b>Age of property</b></p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.age">
                        <option>2012 or newer</option>
                        <option>1983 to 2011</option>
                        <option>1940 to 1982</option>
                        <option>1900 to 1939</option>
                        <option>Pre-1900</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p><b>Floor area</b></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.floor_area" @change="update">
                    <span class="input-group-text">m2</span>
                </div>
            </div>
            <div class="col">
                <p><b>Level of Insulation</b></p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.insulation">
                        <option>Passivhaus</option>
                        <option>Fully insulated walls, floors and loft</option>
                        <option>Some insulation in walls and loft</option>
                        <option>Cavity wall, plus some loft insulation</option>
                        <option>Solid walls</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p><b>Annual heating demand</b><br>For example, as given on the EPC for the property</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.heat_demand" @change="update">
                    <span class="input-group-text">kWh</span>
                </div>
            </div>
            <div class="col">
                <p><b>Heat loss at design temperature</b><br>Usually available on heat pump quote</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.heat_loss" @change="update">
                    <span class="input-group-text">kW @ -3°C</span>
                </div>
            </div>
        </div>

        <hr>
        <h4>Electricity tariff, generation & storage</h4>

        <div class="row">
            <div class="col">
                <p>Current electricity tariff</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.electricity_tariff" @change="update">
                </div>
            </div>
            <div class="col">
                <p>Tariff type</p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.electricity_tariff_type">
                        <option>Fixed rate</option>
                        <option>On peak & Off peak</option>
                        <option>Variable half hourly</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>Average unit rate paid for all electricity<br>(including e.g off peak EV charging)</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.electric_unit_rate_all" @change="update">
                    <span class="input-group-text">p/kWh</span>
                </div>
            </div>
            <div class="col">
                <p>Average unit rate paid for heat-pump consumption component only if known</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.electric_unit_rate_hp" @change="update">
                    <span class="input-group-text">p/kWh</span>
                </div>
            </div>
        </div>  
        <div class="row">
            <div class="col">
                <p>Annual solar PV generation (if applicable)</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.electric_unitrate_all" @change="update">
                    <span class="input-group-text">kWh</span>
                </div>
            </div>
            <div class="col">
                <p>Self-consumption </p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model.number="system.electric_unitrate_hp" @change="update">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>

        <hr>
        <h4>Monitoring information</h4>
        <br>

        <div class="row">
            <div class="col">
                <p><b>Electric meter</b></p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.electric_meter">
                        <option>OpenEnergyMonitor EmonPi v1, EmonTx3 or earlier</option>
                        <option>OpenEnergyMonitor EmonPi v2, EmonTx4 or newer</option>
                        <option>SDM120 Modbus/MBUS Single Phase (class 1)</option>
                        <option>SDM220 Modbus/MBUS Single Phase (class 1)</option>
                        <option>SDM630 Modbus/MBUS Three Phase (class 1)</option>
                        <option>Other Modbus/MBUS meter (class 1)</option>
                        <option>Other pulse output meter (class 1)</option>
                        <option>Heat pump integration</option>
                        <option>Other electricity meter</option>
                        
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p><b>Heat meter</b></p>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="system.heat_meter">
                        <option>Sontex heat meter (class 2)</option>
                        <option>Kamstrup heat meter (class 2)</option>
                        <option>Sharky heat meter (class 2)</option>
                        <option>Qalcosonic heat meter (class 2)</option>
                        <option>SensoStar heat meter (class 2)</option>
                        <option>Itron heat meter (class 2)</option>
                        <option>Danfoss Sono heat meter (class 2)</option>
                        <option>Ista Ultego heat meter (class 2)</option>
                        <option>Sika or Grundfos VFS flow meter</option>
                        <option>Heat pump integration</option>
                        <option>Other heat meter</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <p><b>Metering includes</b></p>

            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.metering_inc_boost" @click="update">
                    <label class="form-check-label">
                        DHW Immersion heater or other booster heater
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.metering_inc_central_heating_pumps" @click="update">
                    <label class="form-check-label">
                        Central heating pump
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.metering_inc_brine_pumps" @click="update">
                    <label class="form-check-label">
                        Ground source brine pump
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="system.metering_inc_controls" @click="update">
                    <label class="form-check-label">
                        Indoor controller or other controls
                    </label>
                </div>
                <br>
            </div>
        </div><br>
        <p><b>Additional notes</b></p>
        <div class="input-group mb-3">
            <input type="text" class="form-control" v-model="system.metering_notes" @change="update" placeholder="Any additional notes about system metering...">
        </div>
        <br>

        <p><b>URL of public MyHeatPump app</b><br>
            Requires an account on emoncms.org, or a self-hosted instance of emoncms</p>
        <div class="input-group mb-3">
            <input type="text" class="form-control" v-model="system.url" @change="update">
            <button class="btn btn-warning" type="button" @click="test_url">Load stats</button>
        </div>

        <pre>{{ stats }}</pre>
        <br>

    </div>
    <div style=" background-color:#eee; padding-top:20px; padding-bottom:10px">
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
            <div class="alert alert-success" role="alert" v-if="show_success" >
                <span v-html="message"></span> 
                <button type="button" class="btn btn-light" @click="cancel" v-if="show_success">Back to system list</button>
            </div>

            
        </div>
    </div>
</div>

<script>
    var referrer = document.referrer;
    referrer = referrer.substr(referrer.lastIndexOf('/') + 1);
    console.log(referrer);

    var app = new Vue({
        el: '#app',
        data: {
            system: <?php echo json_encode($system_data); ?>,
            show_error: false,
            show_success: false,
            message: '',
            stats: '',
            admin: <?php echo $admin; ?>
        },
        methods: {
            update: function() {

            },
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

                            if (response.data.change_log!=undefined) {
                                let change_log = response.data.change_log;
                                // Loop through change log add as list
                                for (var i = 0; i < change_log.length; i++) {
                                    list_items += "<li><b>"+change_log[i]['key']+"</b> changed from <b>"+change_log[i]['old']+"</b> to <b>"+change_log[i]['new']+"</b></li>";
                                }
                            }

                            if (response.data.warning_log!=undefined) {
                                let warning_log = response.data.warning_log;
                                // Loop through change log add as list
                                for (var i = 0; i < warning_log.length; i++) {
                                    list_items += "<li>"+warning_log[i]['message']+"</li>";
                                }
                            }

                            if (list_items) {
                                app.message = "<br><ul>"+list_items+"</ul>";
                            }

                            if (response.data.new_system!=undefined && response.data.new_system) {
                                window.location.href = 'edit?id='+response.data.new_system;
                            }
                        } else {
                            app.show_error = true;
                            app.show_success = false;
                            app.message = response.data.message;

                            if (response.data.error_log!=undefined) {
                                let error_log = response.data.error_log;
                                app.message = 'Could not save form data<br><br><ul>';
                                // Loop through change log add as list
                                for (var i = 0; i < error_log.length; i++) {
                                    app.message += "<li><b>"+error_log[i]['key']+"</b>: "+error_log[i]['message']+"</li>";
                                }
                                app.message += '</ul>';
                            }
                        }
                    });
            },
            cancel: function() {
                if (referrer=='list') {
                    window.location.href = 'list';
                } else if (referrer=='admin') {
                    window.location.href = 'admin';
                } else {
                    window.location.href = 'list';
                }
            },
            test_url: function() {
                axios.post('loadstats', {
                        url: this.$data.system.url,
                        systemid: this.$data.system.id
                    })
                    .then(function(response) {
                        if (response.data) {
                            app.stats = JSON.stringify(response.data, null, 2);
                        }
                    });
            }
        },
        filters: {
            toFixed: function(val, dp) {
                if (isNaN(val)) {
                    return val;
                } else {
                    return val.toFixed(dp)
                }
            }
        }
    });
</script>