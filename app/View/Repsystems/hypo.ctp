<?php
$rsystem=$repsys['Repsystem'];
$unit=$unit['Unit'];
// other variable is $cases
?>

<div class="row" style="margin-top: 10px;">
    <div class="col-xs-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">Representation System: <?php echo $rsystem['title']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-12" style="display: none;">
                    <?php
                    echo "<b>Description</b>: ".$rsystem['description']."<br />";
                    echo "<b>System Type</b>: ".ucfirst($rsystem['type'])."<br />";
                    if(!empty($domain['id'])) {
                        echo "<b>Domain</b>: ".$this->Html->link($domain['title'],'/domains/view/'.$domain['id'],['target'=>'_blank'])."<br />";
                    }
                    echo "<b>Source</b>: ".$this->Html->link($rsystem['url'],$rsystem['url'],['target'=>'_blank'])."<br />";
                    if(!is_null($rsystem['version'])) { echo "<b>Version</b>: ".$rsystem['version']."<br />"; }
                    $perm="https://umis.stuchalk.domains.unf.edu/repsystems/view/".$rsystem['abbrev'];
                    echo "<b>Permalink</b>: ".$this->Html->link($perm,$perm)."<br />";
                    ?>
                </div>
                <div class="col-md-12">
                    <h3>Unit: <?php echo $unit['name']; ?></h3>
                    <p style="color: red;font-weight: bold;font-size: 16px;">NOTE: This is just a hypothetical example and not endorsed/approved by IUPAC (Stuart Chalk)</b><br/>&nbsp</p>
                    <h4>Suggested Representation per Use Case</h4>
                    <table class="table table-striped">
                        <tr>
                            <th class="col-md-3">Use Case</th>
                            <th class="col-md-3">Representation</th>
                            <th class="col-md-6">System</th>
                        </tr>
                        <?php
                        // representations
                        foreach($cases as $case) {
                            echo "<tr>";
                            $string=$case['Representation']['Strng']['string'];
                            if(stristr($string,'<code>')) {
                                $string=str_replace(['<code>','</code>'],'',$string);
                                $string=htmlspecialchars($string);
                            }
                            echo "<td>".$case['Usecase']['name']."</td>";
                            echo "<td>".$string."</td>";
                            echo "<td>".$case['Representation']['Repsystem']['name']."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

