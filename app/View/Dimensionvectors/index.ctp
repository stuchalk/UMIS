<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">Dimension Vectors</h3>
            </div>
            <div class="list-group">
                <?php
                foreach($data as $id=>$dv) {
                    echo "<li class='list-group-item'>".$this->Html->link($dv,'/dimensionvectors/view/'.$id)."</li>";
                }
                ?>
            </div>
        </div>
    </div>
</div>