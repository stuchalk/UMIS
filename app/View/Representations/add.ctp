<?php //debug($rsyss); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#RepresentationString').on('change',function() {
            let input = $(this);
            let val = input.val();
            if (val.length < 3) { return false; }
            let repsys = $('#RepresentationRepsystemId').val();
            if (repsys === '009') {
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
            }
        });
    });
</script>

<div class="row" style="margin-top: 10px;">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Add Representation</h3>
			</div>
			<div class="panel-body">
				<?php
				$options=['class'=>'form-horizontal','enctype'=>'multipart/form-data','inputDefaults'=>['label'=>false,'selected'=>'empty','div'=>false]];
				echo $this->Form->create('Representation',$options);
				?>
				<div id="unit_quantity" class="form-group">
                    <label for="UnitUnitId" class="col-md-2 control-label">Unit:</label>
                    <div class="col-md-3">
                        <?php
                        echo $this->Form->input('unit_id',['type'=>'select','options'=>$units,'empty'=>'Select Unit','class'=>'form-control']);
                        ?>
                    </div>
                    <div class="col-md-1">
                        <?php echo " -OR- "; ?>
                    </div>
                    <label for="UnitQuantityId" class="col-md-2 control-label">Quantity:</label>
					<div class="col-md-3">
						<?php
						echo $this->Form->input('quantity_id',['type'=>'select','options'=>$qunts,'empty'=>'Select Quantity','class'=>'form-control']);
						?>
					</div>
                </div>
                <div id="unitsys_repsys" class="form-group">
                    <label for="UnitUnitsystemId" class="col-md-2 control-label">Unit System:</label>
                    <div class="col-md-3">
                        <?php
                        echo $this->Form->input('unitsystem_id',['type'=>'select','options'=>$usyss,'empty'=>'Select Unit System','class'=>'form-control']);
                        ?>
                    </div>
                    <div class="col-md-1">
                        <?php echo " -OR- "; ?>
                    </div>
                    <label for="UnitQuantityId" class="col-md-2 control-label">Rep. System:</label>
                    <div class="col-md-3">
                        <?php
                        echo $this->Form->input('repsystem_id',['type'=>'select','options'=>$rsyss,'empty'=>'Select Rep. System','class'=>'form-control']);
                        ?>
                    </div>
                </div>
                <div id="string" class="form-group">
					<label for="UnitString" class="col-md-2 control-label">String:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('string', ['type'=>'text','class'=>'form-control','placeholder'=>'Type the unit representation string']); ?>
					</div>
				</div>
				<div id="meta" class="form-group">
                    <div class="col-md-2 col-md-offset-2">
                        <?php
                        $opts=['ascii'=>'String (ascii)','utf8'=>'String (UTF-8)','html'=>'HTML','latex'=>'LaTeX','unknown'=>'Unknown'];
                        echo $this->Form->input('format', ['type'=>'select','class'=>'form-control','options'=>$opts,'empty'=>'Select format']);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        $opts=['current'=>'Current','legacy'=>'Legacy','discouraged'=>'Discouraged','unknown'=>'Unknown'];
                        echo $this->Form->input('status', ['type'=>'select','class'=>'form-control','options'=>$opts,'empty'=>'Select status']);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        $opts=['yes'=>'Yes','no'=>'No','undecided'=>'Undecided'];
                        echo $this->Form->input('acceptable', ['type'=>'select','class'=>'form-control','options'=>$opts,'empty'=>'Acceptable?']);
                        ?>
                    </div>
                </div>
                <div id="reason" class="form-group">
                    <label for="UnitReason" class="col-md-2 control-label">Reason:</label>
                    <div class="col-md-10">
                        <?php echo $this->Form->input('reason', ['type'=>'text','class'=>'form-control','placeholder'=>'Enter the reason why the string is not acceptable']); ?>
                    </div>
                </div>
                <div id="add" class="form-group">
					<div id="button" class="col-md-12">
						<button type="submit" class="btn btn-default pull-right">Add Representation</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

