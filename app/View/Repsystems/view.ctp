<?php
//pr($data);exit;
$rsystem=$data['Repsystem'];
$reps=$data['Representation'];
$domain=$data['Domain'];
?>

<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12 col-md-10 col-lg-10 col-md-offset-1">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h2 class="panel-title">Representation System: <?php echo $rsystem['title']; ?></h2>
			</div>
			<div class="panel-body">
				<div class="col-md-12">
					<?php
					// quantity
					if(!empty($current)) { ?>
                        <a href="/umis/repsystems/view/<?php echo $current['id']; ?>" class="btn btn-info btn-sm pull-right">See current version of this system</a>
						<?php }
					echo "<b>Description</b>: ".$rsystem['description']."<br />";
					echo "<b>System Type</b>: ".ucfirst($rsystem['type'])."<br />";
					if(!empty($domain['id'])) {
						echo "<b>Domain</b>: ".$this->Html->link($domain['title'],'/domains/view/'.$domain['id'],['target'=>'_blank'])."<br />";
					}
					echo "<b>Source</b>: ".$this->Html->link($rsystem['url'],$rsystem['url'],['target'=>'_blank'])."<br />";
					if(!is_null($rsystem['version'])) {
						echo "<b>Version</b>: ".$rsystem['version']."<br />";
					}
					$perm="https://umis.stuchalk.domains.unf.edu/repsystems/view/".$rsystem['abbrev'];
                    echo "<b>Permalink</b>: ".$this->Html->link($perm,$perm)."<br />";
                    ?>
                </div>
				<?php //pr($reps); ?>
				<div class="col-md-12">
					<h4>Representations</h4>
					<table class="table table-striped">
						<tr>
							<th class="col-md-3">Unit</th>
							<th class="col-md-4">Representation</th>
							<th class="col-md-5">Status</th>
						</tr>
						<?php
						// representations
						foreach($reps as $rep) {
						    $str=$rep['Strng'];
                            if(stristr($str['string'],'<code>')) {
                                $str['string']=str_replace(['<code>','</code>'],'',$str['string']);
                                $str['string']=htmlspecialchars($str['string']);
                            }
                            echo "<tr>";
							if(!empty($rep['Unit'])) {
								$u=$rep['Unit'];
								echo "<td>".$this->Html->link($u['name'],'/units/view/'.$u['id'],['escape'=>false])."</td>";
								
							}
							if(!is_null($rep['url'])) {
								echo "<td>".$this->Html->link($str['string'],$rep['url'],['target'=>'_blank','escape'=>false]);
								echo "&nbsp;<span class='glyphicon glyphicon-link'></span>";
								echo "</td>";
							} else {
								echo "<td>".$str['string']."</td>";
							}
							echo "<td>".$str['status']."</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

