<h2>QUANTITIES (by Quantity Kind)</h2>
<div class="row">
    <?php foreach($data as $qk=>$qnts) { ?>
        <div class="col-sm-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $qk; ?></h3>
                </div>
                <div class="list-group">
                    <?php
                    foreach($qnts as $id=>$qnt) {
                        echo "<li class='list-group-item'>".$this->Html->link($qnt,'/quantities/view/'.$id)."</li>";
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>