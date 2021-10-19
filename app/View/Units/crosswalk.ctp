<script type="text/javascript">
    $(document).ready(function() {
        $('#UnitSys1').on('change', function() {
            let sys1 = $("#UnitSys1");let sys2 = $("#UnitSys2");
            let sid1 = sys1.find("option:selected").val();
            let sid2 = sys2.find("option:selected").val();
            let url = "https://umis.stuchalk.domains.unf.edu/repsystems/units/";
            let units1 = null, units2 = null, submit = $("#getcw");
            if (sid1 !== "") {
                $.getJSON(url + sid1).done(function (data) { units1 = data; });
            }
            if (sid2 !== "") {
                $.getJSON(url + sid2).done(function (data) { units2 = data; });
            }
            setTimeout(function () {
                submit.hide();
                if (sid1 !== "") {
                    sys2.find("option").prop('disabled',false);
                    sys2.find("option[value='" + sid1 + "']").prop('disabled',true);
                    if (sid2 === "") {
                        // add entries for system 1 (there is no system 2)
                        addsys1(units1);
                    } else {
                        // add entries to match those already in the table for system 2
                        $('.extra').remove();
                        let rows=$('.trow');
                        $.each(rows, function() {
                            let uid=$(this).attr("data-uid");
                            let equiv="No equivalent";
                            $.each(units1, function(i, ur) {
                                let [u, r] = ur.split(':')
                                if(i === uid) {
                                    equiv=u + " - " + r;
                                    let from=$(".trow[data-uid='" + i + "'] td[class='from']");
                                    from.html(equiv);
                                    delete units1[i];
                                    return false;
                                }
                            });
                        });
                        // if there are any units that have not been matched display them
                        addsys1(units1,true);
                        submit.show();
                    }
                } else {
                    // clear system 1 units but leave system 2
                    $('.trow').remove();
                    if (sid2 !== "") {
                        // add entries for system 2 (there is no system 1)
                        addsys2(units2);
                        sys2.find("option").prop('disabled',false);
                    }
                }
            }, 500); // 1/2 second delay after async getjson(s)
            return false;
        });
        $('#UnitSys2').on('change', function() {
            let sys1 = $("#UnitSys1");let sys2 = $("#UnitSys2");
            let sid1 = sys1.find("option:selected").val();
            let sid2 = sys2.find("option:selected").val();
            let url = "https://umis.stuchalk.domains.unf.edu/repsystems/units/";
            let units1 = null, units2 = null, submit = $("#getcw");
            if (sid1 !== "") {
                $.getJSON(url + sid1).done(function (data) { units1 = data; });
            }
            if (sid2 !== "") {
                $.getJSON(url + sid2).done(function (data) { units2 = data; });
            }
            setTimeout(function () {
                submit.hide();
                if (sid2 !== "") {
                    sys1.find("option").prop('disabled',false);
                    sys1.find("option[value='" + sid2 + "']").prop('disabled',true);
                    if (sid1 === "") {
                        // add entries for system 1 (there is no system 2)
                        addsys2(units2);
                    } else {
                        // add entries to match those already in the table for system 2
                        $('.extra').remove();
                        let rows=$('.trow');
                        $.each(rows, function() {
                            let uid=$(this).attr("data-uid");
                            let equiv="No equivalent";
                            $.each(units2, function(i,ur) {
                                let [u, r] = ur.split(':')
                                if(i === uid) {
                                    equiv=u + " - " + r;
                                    let from=$(".trow[data-uid='" + i + "'] td[class='to']");
                                    from.html(equiv);
                                    delete units2[i];
                                    return false;
                                }
                            });
                        });
                        // if there are any units that have not been matched display them
                        addsys2(units2,true);
                        submit.show();
                    }
                } else {
                    // clear system 2 units but leave system 1
                    $('.trow').remove();
                    if (sid1 !== "") {
                        // add entries for system 1 (there is no system 2)
                        addsys1(units1);
                        sys1.find("option").prop('disabled',false);
                    }
                }
            }, 500); // 1/2 second delay after async getjson(s)
            return false;
        });
        
        function addsys1(units,extra=false) {
            let tbody = $("#tbody");
            let trow = "";
            $.each(units, function (i, ur) {
                let [u,r] = ur.split(':')
                if(extra) {
                    trow = "<tr class='trow extra' data-uid='" + i + "'><td id='from" + u + "' class='from'>" + u + " - " + r + "</td><td id='to" + u + "' class='to'>" + u + " - no equivalent</td></tr>";
                } else {
                    trow = "<tr class='trow' data-uid='" + i + "'><td id='from" + u + "' class='from'>" + u + " - " + r + "</td><td id='to" + u + "' class='to'></td></tr>";
                }
                tbody.append(trow);
            });
        }

        function addsys2(units,extra=false) {
            let tbody = $("#tbody");
            let trow = "";
            $.each(units, function (i, ur) {
                let [u,r] = ur.split(':')
                if(extra) {
                    trow = "<tr class='trow extra' data-uid='" + i + "'><td id='from" + u + "' class='from'>" + u + " - no equivalent</td><td id='to" + u + "' class='to'>" + u + " - " + r + "</td></tr>";
                } else {
                    trow = "<tr class='trow' data-uid='" + i + "'><td id='from" + u + "' class='from'></td><td id='to" + u + "' class='to'>" + u + " - " + r + "</td></tr>";
                }
                tbody.append(trow);
            });
        }
    });
</script>

<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12 col-md-10 col-md-offset-1">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h2 class="panel-title">UNIT SYSTEM CROSSWALK</h2>
			</div>
			<div class="panel-body">
				<div class="col-xs-12">
					Pick the two unit systems that you would like to crosswalk bewteen from the dropdown menus.
					Then click the export button to download the crosswalked units.
				</div>
				<div class="col-xs-12">
					<?php
					$options=['class'=>'form-horizontal','enctype'=>'multipart/form-data','inputDefaults'=>['label'=>false,'selected'=>'empty','div'=>false]];
					echo $this->Form->create('Unit',$options);
					?>
                    <table id="table" class="table table-striped">
                    <thead>
                        <tr>
                            <th id="syshdr1" class="col-xs-6">
                                <?php
                                echo $this->Form->input('sys1',['type'=>'select','options'=>$data,'class'=>'col-xs-12','label'=>false,'empty'=>'Select System 1','style'=>'font-size: 12px;']);
                                ?>
                            </th>
                            <th id="syshdr2" class="col-xs-6">
                                <?php
                                echo $this->Form->input('sys2',['type'=>'select','options'=>$data,'class'=>'col-xs-12','label'=>false,'empty'=>'Select System 2','style'=>'font-size: 12px;']);
                                ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tbody">

                    </tbody>
                    </table>
                </div>
                <div id="getcw" class="col-xs-12" style="display: none;">
                    <button type="submit" class="btn btn-default pull-right">Get Crosswalk</button>
                </div>
			</div>
		</div>
	</div>
</div>
<?php //pr($data); ?>

