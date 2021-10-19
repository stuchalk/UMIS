<?php
//pr($data);exit;
$dv=$data['Dimensionvector'];
$qks=$data['Quantitykind'];
$qsys=$data['Quantitysystem'];
//pr($nyms);
?>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-12 col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">DIMENSION VECTOR: <?php echo $dv['name']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <?php
                    // quantity
                    echo "<b>Description</b>: ".$dv['description']."<br />";
                    echo "<b>Quantity System</b>: ".$this->Html->link($qsys['name'],'/quantitysystems/view/'.$qsys['id'],['escape'=>false])."<br />";
                    echo "<b>Short Code</b>: ".$dv['shortcode']."<br />";
                    echo "<b>Long Code</b>: ".$dv['longcode']."<br />";
                    echo "<b>Symbol</b>: ".$dv['symbol']."<br />";
                    if(!empty($qks)) {
                        echo "<b>Quantity Kind</b>: ";
                        foreach($qks as $idx=>$qk) {
                            if($idx>0) { echo ", "; }
                            echo $this->Html->link($qk['name'],'/quantitykinds/view/'.$qk['id'],['escape'=>false]);
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
