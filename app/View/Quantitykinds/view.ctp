<?php
$qkind=$data['Quantitykind'];
$qsys=$data['Quantitysystem'];
$qnts=$data['Quantity'];
$dim=$data['Dimensionvector'];
$nyms=$data['Nym'];
//pr($nyms);
?>
<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12 col-md-10 col-md-offset-1">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h2 class="panel-title">QUANTITY KIND: <?php echo $qkind['name']; ?></h2>
			</div>
			<div class="panel-body">
				<div class="col-md-5">
					<?php
					// quantity
                    echo "<b>Description</b>: ".$qkind['description']."<br />";
                    echo "<b>Quantity System</b>: ".$this->Html->link($qsys['name'],'/quantitysystems/view/'.$qsys['id'],['escape'=>false])."<br />";
                    echo "<b>Symbol</b>: ".$qkind['symbol']."<br />";
                    echo "<b>Unit Type</b>: ".$qkind['type']."<br />";
					echo "<b>Dimension</b>: ".$dim['symbol']."<br />";
                    if(!empty($nyms)) {
                        echo "<b>Synonyms</b>: ";
                        foreach($nyms as $idx=>$nym) {
                            if($idx>0) { echo ", "; }
                            echo $nym['value'];
                        }
                    }?>
				</div>
				<div class="col-md-7">
					<?php
					if(!empty($qnts)) {
						echo "<b><em>Quantities derived from this kind</em></b><br />";
						foreach($qnts as $idx=>$q) {
							echo $this->Html->link($q['name'],'/quantities/view/'.$q['id'],['class'=>'btn btn-xs btn-warning']).'&nbsp';
						}
					}
					?>
				</div>
            </div>
        </div>
    </div>
</div>
