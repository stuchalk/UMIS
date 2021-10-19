<?php
$constant=$data['Constant'];
$unit=$data['Unit'];
$quantity=$data['Quantity'];
$qkind=$quantity['Quantitykind'];
$domain=$quantity['Domain'];
$dv=$qkind['Dimensionvector'];
?>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-10 col-xs-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <?php echo $this->Html->link('JSON','/constants/json/'.$constant['id'],['class'=>'btn btn-xs btn-info pull-right']); ?>
                <h2 class="panel-title">Constant: <?php echo $constant['name']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <p>
                        <?php
                            echo $constant['description']."<br/>";
                            echo $this->Html->link($constant['url'],$constant['url'],['target'=>'_blank']);
                        ?>
                    </p>
                    <ul>
                        <li>VALUE: <?php echo $constant['value']; ?></li>
                        <?php if(is_null($constant['stduncertainty'])&&$constant['exact']=='yes') { $constant['stduncertainty']= "None (exact)"; } ?>
                        <li>STANDARD UNCERTAINTY: <?php echo $constant['stduncertainty']; ?></li>
                        <?php if(is_null($constant['relstduncertainty'])&&$constant['exact']=='yes') { $constant['relstduncertainty']= "None (exact)"; } ?>
                        <li>RELATIVE STANDARD UNCERTAINTY: <?php echo $constant['relstduncertainty']; ?></li>
                        <li>CONCISE FORM: <?php echo $constant['concise_html']; ?></li>
                        <li>UNIT: <?php echo $unit['html']; ?> (<?php echo $unit['name']; ?>)</li>
                        <li>UNIT CODE: <?php echo $unit['shortcode']; ?></li>
                    </ul>
                    <h4>Quantity</h4>
                    <ul>
                        <li>NAME: <?php echo $quantity['name']; ?></li>
                        <li>TYPE: <?php echo $quantity['description']; ?></li>
                        <li>DOMAIN: <?php echo $domain['title']; ?></li>
                    </ul>
                    <h4>Quantity Kind</h4>
                    <ul>
                        <li>NAME: <?php echo $qkind['name']; ?></li>
                        <li>TYPE: <?php echo $qkind['type']; ?></li>
                        <li>DIMENSIONALITY: <?php echo $qkind['symbol']; ?></li>
                    </ul>
                    <h4>Dimension Vector</h4>
                    <ul>
                        <li>SHORT CODE: <?php echo $dv['shortcode']; ?></li>
                        <li>LONG CODE: <?php echo $dv['longcode']; ?></li>
                    </ul>
                    <?php
                    //pr($data);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
