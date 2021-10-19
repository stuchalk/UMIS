<?php
//pr($data);exit;
$qnt=$data['Quantity'];
$qkind=$data['Quantitykind'];
$qsys=$qkind['Quantitysystem'];
$units=$qkind['Unit'];
$nyms=$data['Nym'];
//pr($nyms);
?>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-12 col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">QUANTITY: <?php echo $qnt['name']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-7">
                    <?php
                    // quantity
                    echo "<b>Description</b>: ".$qnt['description']."<br />";
                    echo "<b>Quantity Kind</b>: ".$this->Html->link($qkind['name'],'/quantitykinds/view/'.$qkind['id'],['escape'=>false])."<br />";
                    echo "<b>Quantity System</b>: ".$this->Html->link($qsys['name'],'/quantitysystems/view/'.$qsys['id'],['escape'=>false])."<br />";
                    echo "<b>Symbol</b>: ".$qnt['symbol']."<br />";
                    if(!empty($nyms)) {
                        echo "<b>Synonyms</b>: ";
                        foreach($nyms as $idx=>$nym) {
                            if($idx>0) { echo ", "; }
                            echo $nym['value'];
                        }
                        echo "<br/>";
                    }
                    if(!is_null($qnt['url'])) {
                        echo "<b>Source</b>: ".$this->Html->link($qnt['url'],$qnt['url'],['target'=>'_blank','escape'=>false])."<br />";
                    }
                    ?>
                </div>
                <div class="col-md-5">
                    <?php
                    if(!empty($units)) {
                        echo "<b><em>Units that are used for this quantity</em></b><br />";
                        foreach($units as $idx=>$u) {
                            echo $this->Html->link($u['name'],'/units/view/'.$u['id'],['class'=>'btn btn-xs btn-warning']).'&nbsp';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
