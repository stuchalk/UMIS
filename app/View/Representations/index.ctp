<?php //pr($data); ?>
<h2>Units Representations (by Unit)</h2>
<div class="row">
	<?php foreach($data as $unit=>$reps) { ?>
		<div class="col-sm-3">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $unit; ?></h3>
				</div>
				<div class="list-group" style="max-height: 122px;overflow-y: scroll;">
					<?php
					foreach($reps as $id=>$rep) {
						echo "<li class='list-group-item'>".$this->Html->link($rep,'/representations/view/'.$id,['escape'=>false])."</li>";
					}
					?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>