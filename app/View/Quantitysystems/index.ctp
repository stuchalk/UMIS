<?php //pr($data); ?>
<h2>Quantity Systems</h2>
<div class="col-sm-10 col-sm-offset-1">
    <div class="panel panel-success">
        <div class="list-group">
            <?php
            foreach($data as $id=>$qsys) {
                echo "<li class='list-group-item'>".$this->Html->link($qsys,'/quantitysystems/view/'.$id)."</li>";
            }
            ?>
        </div>
    </div>
</div>
