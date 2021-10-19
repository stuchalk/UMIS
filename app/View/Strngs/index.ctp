<?php //pr($data); ?>
<div class="col-sm-10 col-sm-offset-1">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h2 class="panel-title">Representation strings</h2>
        </div>
        <div class="panel-body list-group">
            <?php
            foreach($data as $id=>$repstr) {
                echo "<li class='list-group-item'>".$this->Html->link($repstr,'/repstrings/view/'.$id)."</li>";
            }
            ?>
        </div>
    </div>
</div>
