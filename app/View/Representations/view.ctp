<?php
//pr($data);
$rep=$data['Representation'];
$usys=$data['Unitsystem'];
$rsys=$data['Repsystem'];
$unit=$data['Unit'];
$rstr=$data['Strng'];
$encs=$rstr['Encoding'];
?>
<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12 col-md-10 col-md-offset-1">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h2 class="panel-title">UNIT Representation: <?php echo $rstr['string']; ?></h2>
			</div>
			<div class="panel-body">
				<div class="col-md-6">
					<?php
					// representation
					echo "<b>Status</b>: ".$rstr['status']."<br />";
					if(!is_null($rep['url'])) {
						echo "<b>Source</b>: ".$this->Html->link($rep['url'],$rep['url'],['target'=>'_blank','escape'=>false])."<br />";
					}
					if(!is_null($usys['id'])) {
						echo "<b>Unit system:</b> ".$this->Html->link($usys['name'],'/unitsystems/view/'.$usys['id'],['escape'=>false])."<br />";
					}
					if(!is_null($rsys['id'])) {
						echo "<b>Representation system:</b> ".$this->Html->link($rsys['name'],'/repsystems/view/'.$rsys['id'],['escape'=>false])."<br />";
					}
					echo "<b>Unit:</b> ".$this->Html->link($unit['name'],'/units/view/'.$rsys['id'],['escape'=>false])."<br />";
					?>
				</div>
				<div class="col-md-6">
					<h4>Representation Formats</h4>
					<table class="table table-striped">
						<tr>
							<th class="col-md-6">String</th>
							<th class="col-md-6">Format</th>
						</tr>
						<?php
						// formats
						foreach($encs as $enc) {
							echo "<tr>";
							echo "<td>".$enc['string']."</td>";
							echo "<td>".$enc['format']."</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
