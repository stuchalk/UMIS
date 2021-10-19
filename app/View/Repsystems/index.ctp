<?php //pr($data); ?>
<div class="col-sm-10 col-sm-offset-1">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h2 class="panel-title">Unit Representation Systems</h2>
        </div>
        <div class="panel-body list-group">
            <?php
            foreach($data as $id=>$repsys) {
                echo "<li class='list-group-item'>".$this->Html->link($repsys,'/repsystems/view/'.$id)."</li>";
            }
            ?>
        </div>
    </div>
</div>
