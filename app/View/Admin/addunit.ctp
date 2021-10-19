<?php
//pr($unit);
//pr($codes);
//pr($qklist);
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#OtherNcit').on('change',function() {
            let input = $(this);
            let val = input.val();
            if (val.length < 3) { return false; }
            $.ajax({
                type: 'GET',
                dataType: "json",
                context: document.body,
                url: 'https://www.ebi.ac.uk/ols/api/select?q=' + val + '&ontology=ncit',
                data: {},
                success: function (data) {
                    // send data to database
                    let results = data['response']['docs'];
                    let tags = []
                    $.each(results, function(index,hit) {
                        if (hit.label.lower===val.lower) {
                            input.val(hit.short_form);
                            return false;
                        }
                    });
                }
            });
        });
    });
</script>

<div class="row" style="margin-top: 10px;">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Add Unit</h3>
            </div>
            <div class="panel-body">
                <?php
                $options=['class'=>'form-horizontal','enctype'=>'multipart/form-data','inputDefaults'=>['label'=>false,'selected'=>'empty','div'=>false]];
                echo $this->Form->create('Unit',$options);
                $uid=''; if(isset($unit['id'])) { $uid=$unit['id']; }
                echo $this->Form->input('id',['type'=>'hidden','value'=>$uid]);
                echo $this->Form->input('Qudt.id',['type'=>'hidden','value'=>$qudt['id']]);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div id="unit_name" class="form-group">
                            <label for="UnitName" class="col-md-4 control-label">Name:</label>
                            <div class="col-md-8">
                            <?php
                                $name = ''; if(isset($unit['name'])) { $name=$unit['name']; }
                                echo $this->Form->input('name',['type'=>'text','value'=>$unit['name'],'class'=>'form-control']);
                            ?>
                            </div>
                        </div>
                        <div id="unit_quanitykind_id" class="form-group">
                            <label for="UnitQuantitykindId" class="col-md-4 control-label">Quantity Kind:</label>
                            <div class="col-md-8">
                                <?php
                                $qkid = ''; if(isset($unit['quantitykind_id'])) { $qkid=$unit['quantitykind_id']; }
                                echo $this->Form->input('quantitykind_id',['type'=>'select','options'=>$qklist,'selected'=>$qkid,'empty'=>'Select Quantity Kind','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_unitsystem_id" class="form-group">
                            <label for="UnitUnitsystemId" class="col-md-4 control-label">Unit System:</label>
                            <div class="col-md-8">
                                <?php
                                $usid = ''; if(isset($unit['unitsystem_id'])) { $usid=$unit['unitsystem_id']; }
                                echo $this->Form->input('unitsystem_id',['type'=>'select','options'=>$uslist,'selected'=>$usid,'empty'=>'Select Unit System','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_description" class="form-group">
                            <label for="UnitDescription" class="col-md-4 control-label">Description:</label>
                            <div class="col-md-8">
                                <?php
                                $disc = ''; if(isset($unit['description'])) { $disc=$unit['description']; }
                                echo $this->Form->input('description',['type'=>'text','value'=>$disc,'class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_prefix_id" class="form-group">
                            <label for="UnitPrefixId" class="col-md-4 control-label">Prefix:</label>
                            <div class="col-md-8">
                                <?php
                                $pfid = ''; if(isset($unit['prefix_id'])) { $pfid=$unit['prefix_id']; }
                                echo $this->Form->input('prefix_id',['type'=>'select','options'=>$pflist,'selected'=>$pfid,'empty'=>'Select Prefix','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_factor_id" class="form-group">
                            <label for="UnitFactorId" class="col-md-4 control-label">Factor:</label>
                            <div class="col-md-8">
                                <?php
                                $ftid = ''; if(isset($unit['factor_id'])) { $ftid=$unit['factor_id']; }
                                echo $this->Form->input('factor_id',['type'=>'select','options'=>$ftlist,'selected'=>$ftid,'empty'=>'Select Factor','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_url" class="form-group">
                            <label for="UnitUrl" class="col-md-4 control-label">URL:</label>
                            <div class="col-md-8">
                                <?php
                                $url = ''; if(isset($unit['url'])) { $url=$unit['url']; }
                                echo $this->Form->input('url',['type'=>'text','value'=>$url,'class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_type" class="form-group">
                            <label for="UnitType" class="col-md-4 control-label">Unit Type:</label>
                            <div class="col-md-8">
                                <?php
                                $type = ''; if(isset($unit['type'])) { $type=$unit['type']; }
                                $opts=['Accepted Non-SI'=>'Accepted Non-SI','CGS Base'=>'CGS Base','Other'=>'Other',
                                    'SI Base'=>'SI Base','SI Coherent Derived'=>'SI Coherent Derived',
                                    'SI Derived'=>'SI Derived','US Customary'=>'US Customary'];
                                echo $this->Form->input('type',['type'=>'select','options'=>$opts,'selected'=>$type,'empty'=>'Select Unit Type','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_shortcode" class="form-group">
                            <label for="UnitShortcode" class="col-md-4 control-label">Short Code:</label>
                            <div class="col-md-8">
                                <?php
                                $scode = ''; if(isset($unit['shortcode'])) { $scode=$unit['shortcode']; }
                                echo $this->Form->input('shortcode',['type'=>'text','value'=>$scode,'class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="unit_alt_shortcode" class="form-group">
                            <label for="UnitAltShortcode" class="col-md-4 control-label">Alt Short Code:</label>
                            <div class="col-md-8">
                                <?php
                                $acode = ''; if(isset($unit['alt_shortcode'])) { $scode=$unit['alt_shortcode']; }
                                echo $this->Form->input('alt_shortcode',['type'=>'text','value'=>$acode,'class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="unece_id" class="form-group">
                            <label for="UneceCode" class="col-md-4 control-label">UNECE:</label>
                            <div class="col-md-8">
                                <?php
                                $unid = ''; if(!is_null($codes['unece'])) { $unid=$codes['unece']; }
                                echo $this->Form->input('Unece.code',['type'=>'select','options'=>$unlist,'selected'=>$unid,'empty'=>'Select UNECE Code','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="ucum_codes" class="form-group">
                            <label for="UcumCodes" class="col-md-4 control-label">UCUM Codes:</label>
                            <div class="col-md-8">
                                <?php
                                $uccodes = ''; if(isset($codes['ucum'])) { $uccodes=implode(', ',$codes['ucum']); }
                                echo $this->Form->input('Ucum.codes',['type'=>'text','value'=>$uccodes,'class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="wiki_code" class="form-group">
                            <label for="WikiCode" class="col-md-4 control-label">Wikidata ID:</label>
                            <div class="col-md-8">
                                <?php
                                $wdcode = ''; if(!is_null($codes['wiki'])) { $wdcode=$codes['wiki']; }
                                echo $this->Form->input('Wiki.code',['type'=>'select','options'=>$wikilist,'selected'=>$wdcode,'empty'=>'Select Wikidata Code','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="nerc_code" class="form-group">
                            <label for="NercCode" class="col-md-4 control-label">NERC Code:</label>
                            <div class="col-md-8">
                                <?php
                                $nccode = ''; if(!is_null($codes['nerc'])) { $nccode=$codes['nerc']; }
                                echo $this->Form->input('Nerc.code',['type'=>'select','options'=>$nerclist,'selected'=>$nccode,'empty'=>'Select NERC Code','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="udunit_symbol" class="form-group">
                            <label for="UdunitSymbol" class="col-md-4 control-label">UDUNIT Symbol:</label>
                            <div class="col-md-8">
                                <?php
                                $udcode = ''; if(!is_null($codes['ud'])) { $udcode=$codes['ud']; }
                                echo $this->Form->input('Udunit.symbol',['type'=>'select','options'=>$udlist,'selected'=>$udcode,'empty'=>'Select UDUNIT Code','class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="iec_codes" class="form-group">
                            <label for="IecCodes" class="col-md-4 control-label">IEC Codes:</label>
                            <div class="col-md-8">
                                <?php
                                $ieccodes = ''; if(isset($codes['iec'])) { $ieccodes=implode(', ',$codes['iec']); }
                                echo $this->Form->input('Other.ieccodes',['type'=>'text','value'=>$ieccodes,'class'=>'form-control']);
                                ?>
                            </div>
                        </div>
                        <div id="ncit" class="form-group">
                            <label for="OtherNcit" class="col-md-4 control-label">NCIT Entry:</label>
                            <div class="col-md-8">
                                <?php echo $this->Form->input('Other.ncit',['type'=>'text','value'=>'','class'=>'form-control']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="add" class="form-group">
                    <div id="button" class="col-md-12">
                        <button type="submit" class="btn btn-default pull-right">Add Unit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
