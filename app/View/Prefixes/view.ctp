<?php
$prefix=$data['Prefix'];
?>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-10 col-xs-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">SI Prefix: <?php echo $prefix['name']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <ul>
                        <li>SYMBOL: <?php echo $prefix['symbol']; ?></li>
                        <li>VALUE: <?php echo $prefix['value']; ?></li>
                        <li>INVERSE: <?php echo $prefix['inverse']; ?></li>
                        <li>QUANTITYSYSTEM: The International System of Quantities</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
