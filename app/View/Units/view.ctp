<?php
//pr($log);
//pr($data);//exit;
$unit=$data['Unit'];
$qkind=$data['Quantitykind'];
$qsys=$qkind['Quantitysystem'];
$usystem=$data['Unitsystem'];
$prefix=$data['Prefix'];
$efrom=$data['FromEUnit'];
$eto=$data['ToEUnit'];
$cfrom=$data['FromCUnit'];
$cto=$data['ToCUnit'];
//$reps=$data['Representation'];
//debug($reps);
?>

<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12">
		<div class="panel panel-success">
			<div class="panel-heading">
                <?php echo $this->Html->link('JSON Download','/units/view/'.$unit['id'].'/json',['class'=>'btn btn-xs btn-info pull-right']); ?>
				<h2 class="panel-title">UNIT: <?php echo $unit['name']; ?></h2>
			</div>
			<div class="panel-body">
                <div class="col-md-6">
                    <?php
                    // quantity
                    echo "<b>Unit System</b>: ".$this->Html->link($usystem['name'],'/unitsystems/view/'.$usystem['id'],['escape'=>false])."<br />";
                    echo "<b>Quantity System</b>: ".$this->Html->link($qsys['name'],'/quantitysystems/view/'.$qsys['id'],['escape'=>false])."<br />";
                    echo "<b>Quantity Kind</b>: ".$this->Html->link($qkind['name'],'/quantitykinds/view/'.$qkind['id'],['escape'=>false])."<br />";
                    echo "<b>Unit Type</b>: ".$qkind['type']."<br />";
                    echo "<b>Dimension</b>: ".$qkind['Dimensionvector']['symbol']."<br />";
                    if($data['Prefix']['id']!=null) {
                        $pf=$data['Prefix'];
                        echo "Uses prefix: ".$pf['name']." (".$pf['value'].")"."<br />";
                    }
					if(!empty($data['Nym'])) {
						$nyms=$data['Nym'];
						echo "<b>Synonyms</b>: ";
                        foreach($nyms as $idx=>$nym) {
                            if($idx>0) { echo ", "; }
						    echo $nym['value'];
                        }
					}
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    if(!empty($qkind['Quantity'])) {
                        echo "<b><em>Quantities that are measured in this unit</em></b><br />";
                        foreach($qkind['Quantity'] as $idx=>$q) {
                            echo $this->Html->link($q['name'],'/quantities/view/'.$q['id'],['class'=>'btn btn-xs btn-warning']).'&nbsp';
                        }
                    }
                    ?>
                </div>
                <div class="col-md-12">
                    <h4>Representations</h4>
                    <table class="table table-striped">
                        <tr>
                            <th class="col-md-3">String</th>
                            <th class="col-md-2">Status</th>
                            <th class="col-md-7">Representation System(s)</th>
                        </tr>
                        <?php
                        // representations
                        foreach($reps as $status=>$strings) {
                            ksort($strings);
                            foreach($strings as $string=>$systems) {
                                ksort($systems);
                                //debug($systems);
                                echo "<tr>";
                                if(stristr($string,'<code>')) {
                                    $string=str_replace(['<code>','</code>'],'',$string);
                                    $string=htmlspecialchars($string);
                                }
                                echo "<td>".$string;
                                if(!empty($systems['string']['Encoding'])) {
                                    $rsid=$systems['string']['id'];
                                    echo " (".$this->Html->link('encodings','/encodings/view/'.$rsid,['escape'=>false]).")";
                                }
                                echo "</td>";
                                if(!is_null($systems['string']['reason'])) {
                                    echo "<td title='".$systems['string']['reason']."' class='redtext hover'>".$systems['string']['status']."</td>";
                                } else {
                                    echo "<td>".$systems['string']['status']."</td>";
                                }
                                echo "<td>";
                                ksort($systems['systems']);
                                foreach($systems['systems'] as $sysname=>$sys) {
                                    if(!empty($sys['Unitsystem'])) {
                                        $us=$sys['Unitsystem'];
                                        echo $this->Html->link($us['name']." (".$us['abbrev'].")",'/unitsystems/view/'.$us['id'],['escape'=>false]);
                                    } elseif(!empty($sys['Repsystem'])) {
                                        $rs=$sys['Repsystem'];
                                        echo $this->Html->link($rs['name']." (".$rs['abbrev'].")",'/repsystems/view/'.$rs['id'],['escape'=>false]);
                                    } else {
                                        echo "General (colloquial) usage";
                                    }
                                    if(!is_null($sys['url'])) {
                                        echo " (".$this->Html->link('definition',$sys['url'],['target'=>'_blank','escape'=>false]);
                                        echo "&nbsp;<span class='glyphicon glyphicon-link'></span>)";
                                    }
                                    echo "</br>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </table>
                </div>
                <?php if(!empty($efrom)||!empty($eto)) { ?>
                    <div class="col-md-12">
                        <h4>Equivalent Units</h4>
                        <table class="table table-striped">
                        <tr>
                            <th class="col-md-2">From</th>
                            <th class="col-md-2">Factor</th>
                            <th class="col-md-2">To</th>
                        </tr>
                        <?php
                        // equivalents
                        foreach($efrom as $eq) {
                            //debug($eq);
                            echo "<tr><td>";
                            echo $unit['name'];
                            echo "</td><td>";
                            if(!is_null($eq['factor'])) { echo $eq['factor']; }
                            if(!is_null($eq['prefix_id'])) { echo $eq['Prefix']['value']; }
                            if(!is_null($eq['constant_id'])) { echo $eq['Constant']['value']; }
                            echo "</td><td>";
                            echo $eq['To']['name'];
                            echo "</td></tr>";
                        }
                        foreach($eto as $eq) {
                            //debug($eq);
                            echo "<tr><td>";
							echo $unit['name'];
							echo "</td><td>";
                            if(!is_null($eq['factor'])) { echo "1/".$eq['factor']; }
                            if(!is_null($eq['prefix_id'])) { echo $eq['Prefix']['inverse']; }
                            if(!is_null($eq['constant_id'])) { echo $eq['Constant']['inverse']; }
                            echo "</td><td>";
                            echo $eq['From']['name'];
							echo "</td></tr>";
                        }
                        ?>
                    </table>
                    </div>
                <?php } ?>
				<?php if(!empty($cfrom)||!empty($cto)) { ?>
                    <div class="col-md-12">
                        <h4>Corresponding Units</h4>
                        <table class="table table-striped">
                            <tr>
                                <th class="col-md-2">From</th>
                                <th class="col-md-2">Factor</th>
                                <th class="col-md-2">To</th>
                            </tr>
							<?php
							// correspondents
							foreach($cfrom as $eq) {
								//debug($eq);
								echo "<tr><td>";
								echo $unit['name'];
								echo "</td><td>";
								echo '<span class="glyphicon glyphicon-resize-horizontal"></span> ';
								if(!is_null($eq['factor'])) { echo $eq['factor']; }
								if(!is_null($eq['factoreqn'])) { echo $eq['factoreqn']; }
								if(!is_null($eq['prefix_id'])) { echo $eq['Prefix']['value']; }
								if(!is_null($eq['constant_id'])) { echo $eq['Constant']['value']; }
								echo "</td><td>";
								echo $eq['To']['name'];
								echo "</td></tr>";
							}
							foreach($cto as $eq) {
								//debug($eq);
								echo "<tr><td>";
								echo $unit['name'];
								echo "</td><td>";
								echo '<span class="glyphicon glyphicon-resize-horizontal"></span> ';
								if(!is_null($eq['factor'])) { echo "1/".$eq['factor']; }
								if(!is_null($eq['factoreqn'])) { echo "1/".$eq['factoreqn']; }
								if(!is_null($eq['prefix_id'])) { echo $eq['Prefix']['inverse']; }
								if(!is_null($eq['constant_id'])) { echo $eq['Constant']['inverse']; }
								echo "</td><td>";
								echo $eq['From']['name'];
								echo "</td></tr>";
							}
							?>
                        </table>
                    </div>
				<?php } ?>
            </div>
		</div>
	</div>
</div>
