<?php
//pr($data);
$usys=$data['Unitsystem'];
$qsys=$data['Quantitysystem'];
$units=$data['Unit'];

?>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">Unit System: <?php echo $usys['name']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-6">
                    <?php
                    // representation
                    echo "<b>Description</b>: ".$usys['description']."<br />";
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    echo "<b>Abbreviation</b>: ".$usys['abbrev']."<br />";
                    echo "<b>Quantity System</b>: ".$this->Html->link($qsys['name'],'/quantitysystem/view/'.$qsys['id'],['escape'=>false])."<br />";
                    if(!is_null($usys['url'])) {
                        echo "<b>Source</b>: ".$this->Html->link($usys['url'],$usys['url'],['target'=>'_blank','escape'=>false])."<br />";
                    }
                    ?>
                </div>
                <div class="col-md-12">
                    <h4>Units</h4>
                    <table class="table table-striped">
                        <tr>
                            <th class="col-md-2">Unit</th>
                            <th class="col-md-5">Description</th>
                            <th class="col-md-2">Type</th>
                            <th class="col-md-3">Source</th>
                        </tr>
                        <?php
                        // formats
                        foreach($units as $unit) {
                            echo "<tr>";
                            echo "<td>".$this->Html->link($unit['name'],'/units/view/'.$unit['id'],['escape'=>false])."</td>";
                            echo "<td>".$unit['description']."</td>";
                            echo "<td>".ucfirst($unit['type'])."</td>";
                            echo "<td>".$this->Html->link($unit['url'],$unit['url'],['target'=>'_blank','escape'=>false])."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
