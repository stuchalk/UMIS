<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Quantity Kinds</h3>
            </div>
            <div class="list-group">
                <?php
                foreach($data as $id=>$unit) {
                    echo "<li class='list-group-item'>".$this->Html->link($unit,'/quantitykinds/view/'.$id)."</li>";
                }
                ?>
            </div>
        </div>
    </div>
</div>