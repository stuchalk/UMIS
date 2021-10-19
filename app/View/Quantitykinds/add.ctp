<script type="text/javascript">
    $(document).ready(function() {
        $('.quantity').on('change', function(){
            var kindid = $(this).find("option:selected").val();
            var url = "https://umis.stuchalk.domains.unf.edu/quantitykinds/qklist/" + kindid;
            if(kindid!="") {
                $.getJSON(url).done(function(data) {
                    $.each(data[0], function(i,qk) {
                        var badge=$('<span class="badge">' + qk + '</span>');
                        $('#kindlist').append(badge);
                    });
                    $('#kinds').show();
                });
            } else {
                $('#kindlist').empty();
                $('#kinds').hide();
            }
            return false;
        });
    });
</script>

<div class="row" style="margin-top: 10px;">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Add Quantity Kind</h3>
			</div>
			<div class="panel-body">
				<?php
				$options=['class'=>'form-horizontal','enctype'=>'multipart/form-data','inputDefaults'=>['label'=>false,'selected'=>'empty','div'=>false]];
				echo $this->Form->create('Quantitykind',$options);
				?>
				<div id="quantity_domain" class="form-group">
					<label for="QuantitykindQuantity" class="col-md-2 control-label">Quantity:</label>
					<div class="col-md-4">
						<?php
						echo $this->Form->input('quantity_id',['type'=>'select','options'=>$quans,'empty'=>'Select Quantity','class'=>'quantity form-control']);
						?>
					</div>
					<label for="QuantitykindDomain" class="col-md-2 control-label">Domain:</label>
					<div class="col-md-4">
						<?php
						echo $this->Form->input('domain_id',['type'=>'select','options'=>$domains,'empty'=>'Select Domain','class'=>'form-control']);
						?>
					</div>
				</div>
                <div id="kinds" class="form-group" style="display: none;">
                    <label for="QuantitykindName" class="col-md-2 control-label">Quantity kinds<br/>in the database</label>
                    <div id="kindlist" class="col-md-10"></div>
                </div>
                <div id="name" class="form-group">
					<label for="QuantitykindName" class="col-md-2 control-label">Name:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('name', ['type'=>'text','class'=>'form-control','placeholder'=>'Add the quantity kind name']); ?>
					</div>
				</div>
				<div id="description" class="form-group">
					<label for="QuantitykindDescription" class="col-md-2 control-label">Description:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('description', ['type'=>'textarea','class'=>'form-control','rows'=>'4','placeholder'=>'Get this from a reputable website...']); ?>
					</div>
				</div>
				<div id="symbols" class="form-group">
					<label for="QuantitykindSymbol" class="col-md-2 control-label">Symbol:</label>
					<div class="col-md-4">
						<?php echo $this->Form->input('symbol', ['type'=>'text','class'=>'form-control','placeholder'=>'Symbol as string, html, etc.']); ?>
					</div>
					<label for="QuantitykindLatexsymbol" class="col-md-2 control-label">Symbol (Latex):</label>
					<div class="col-md-4">
						<?php echo $this->Form->input('latexsymbol', ['type'=>'text','class'=>'form-control','placeholder'=>'Symbol in latex']); ?>
					</div>
				</div>
				<div id="url" class="form-group">
					<label for="QuanitykindUrl" class="col-md-2 control-label">URL:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('url', ['type'=>'text','class'=>'form-control','placeholder'=>'Definitive resource...']); ?>
					</div>
				</div>
				<div id="add" class="form-group">
					<div id="button" class="col-md-12">
						<button type="submit" class="btn btn-default pull-right">Add Quantity Kind</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
