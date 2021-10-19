<?php
//pr($data);
$qsys=$data['Quantitysystem'];
$dims=$data['Dimension'];

?>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-12 col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">Quantity System: <?php echo $qsys['name']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-6">
                    <?php
                    // representation
                    echo "<b>Description</b>: ".$qsys['description']."<br />";
                    echo "<b>Abbreviation</b>: ".$qsys['abbrev']."<br />";
                    if(!is_null($qsys['url'])) {
                        echo "<b>Source</b>: ".$this->Html->link($qsys['url'],$qsys['url'],['target'=>'_blank','escape'=>false])."<br />";
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <h4>Dimensions</h4>
                    <table class="table table-striped">
                        <tr>
                            <th class="col-md-6">Dimension</th>
                            <th class="col-md-6">Symbol</th>
                        </tr>
                        <?php
                        // formats
                        foreach($dims as $dim) {
                            echo "<tr>";
                            echo "<td>".ucfirst($dim['type'])."</td>";
                            echo "<td>".$dim['symbol']."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
