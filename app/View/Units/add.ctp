<?php
//pr($quans);pr($syss);exit;
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.qkind').on('change', function(){
            var qid = $(this).find("option:selected").val();
            $('#unitlist').empty();
            if(qid!="") {
                var url = "https://umis.stuchalk.domains.unf.edu/units/unlist/" + qid;
                $.getJSON(url).done(function(data) {
                    $.each(data[0], function(i,qk) {
                        var badge=$('<span class="badge">' + qk + '</span>');
                        $('#unitlist').append(badge);
                    });
                    $('#units').show();
                });
                // get quantity system ID for quantity
				var qsid=0;
                $.ajax({
                    method: "GET",
                    url: "https://umis.stuchalk.domains.unf.edu/quantities/getfield/" + qid + "/quantitysystem_id",
                    async: false
                }).done(function( value ) {
					qsid=value;
                });
                var url = "https://umis.stuchalk.domains.unf.edu/prefixes/plist/" + qsid;
                $.getJSON(url).done(function(data) {
                    $.each(data[0], function(i,pre) {
                        $('#UnitPrefixId').append($('<option>', {
                            value: i,
							text: pre
							})
						);
                    });
                    $('.prefix').show();
                });
            } else {
                $('#unitlist').empty();
                $('#units').hide();
                $('.prefix').hide();
            }
            return false;
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
				?>
				<div id="quantity_prefix" class="form-group">
					<label for="UnitQuantity" class="col-md-2 control-label">Quantity:</label>
					<div class="col-md-4">
						<?php
						echo $this->Form->input('quantitykind_id',['type'=>'select','options'=>$qkinds,'empty'=>'Select Quantity Kind','class'=>'qkind form-control']);
						?>
					</div>
					<label for="UnitPrefixId" class="prefix col-md-2 control-label" style="display: none;">Prefixes:</label>
					<div class="col-md-4">
						<?php
						echo $this->Form->input('prefix_id',['type'=>'select','options'=>[],'empty'=>'Select Prefix','class'=>'prefix form-control','style'=>'display: none;']);
						?>
					</div>
				</div>
				<div id="units" class="form-group" style="display: none;">
					<label for="UnitUnitlist" class="col-md-2 control-label">Existing units<br/>for this quantity</label>
					<div id="unitlist" class="col-md-10"></div>
				</div>
				<div id="name" class="form-group">
					<label for="UnitName" class="col-md-2 control-label">Name:</label>
					<div class="col-md-5">
						<?php echo $this->Form->input('name', ['type'=>'text','class'=>'form-control','placeholder'=>'Add the unit name']); ?>
					</div>
					<label for="UnitType" class="col-md-2 control-label">Type:</label>
					<div class="col-md-3">
						<?php
						$opts=['SIBase'=>'Base SI','SIDerived'=>'Derived SI','SICoherent'=>'Coherent SI','CGSBase'=>'Base CGS','CGSDerived'=>'Derived CGS'];
						echo $this->Form->input('type', ['type'=>'select','class'=>'form-control','options'=>$opts,'empty'=>'Select Type']);
						?>
					</div>
				</div>
				<div id="description" class="form-group">
					<label for="UnitDescription" class="col-md-2 control-label">Description:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('description', ['type'=>'textarea','class'=>'form-control','rows'=>'4','placeholder'=>'Get this from a reputable website...']); ?>
					</div>
				</div>
				<div id="url" class="form-group">
					<label for="UnitUrl" class="col-md-2 control-label">URL:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('url', ['type'=>'text','class'=>'form-control','placeholder'=>'Definitive resource...']); ?>
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

