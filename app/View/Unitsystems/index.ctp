<?php //pr($data); ?>
<h2>Unit Systems</h2>
<div class="col-sm-10 col-sm-offset-1">
    <div class="panel panel-success">
        <div class="list-group">
            <?php
            foreach($data as $id=>$usys) {
                echo "<li class='list-group-item'>".$this->Html->link($usys,'/unitsystems/view/'.$id)."</li>";
            }
            ?>
        </div>
    </div>
</div>
