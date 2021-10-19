<h2>Units of Measure (by Quantity Kind)</h2>
<div class="row">
<?php foreach($data as $quantity=>$units) { ?>
	<div class="col-sm-4">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $quantity; ?></h3>
			</div>
			<div class="list-group">
				<?php
				foreach($units as $id=>$unit) {
					echo "<li class='list-group-item'>".$this->Html->link($unit,'/units/view/'.$id)."</li>";
				}
				?>
			</div>
		</div>
	</div>
    <?php } ?>
</div>