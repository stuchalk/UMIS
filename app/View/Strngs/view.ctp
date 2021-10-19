<?php
//pr($data);exit;
$rstr=$data['Strng'];
$renc=$data['Encoding'];
?>

<div class="row" style="margin-top: 10px;">
    <div class="col-xs-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h2 class="panel-title">Representation String: <?php echo $rstr['string']; ?></h2>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <h4>Encodings</h4>
                    <table class="table table-striped">
                        <tr>
                            <th class="col-md-3">String</th>
                            <th class="col-md-4">Format</th>
                            <th class="col-md-5">Status</th>
                        </tr>
                        <?php
                        // representations
                        foreach($reps as $rep) {
                            echo "<tr>";
                            if(!empty($rep['Unit'])) {
                                $u=$rep['Unit'];
                                echo "<td>".$this->Html->link($u['name'],'/units/view/'.$u['id'],['escape'=>false])."</td>";

                            }
                            if(!is_null($rep['url'])) {
                                echo "<td>".$this->Html->link($rep['string'],$rep['url'],['target'=>'_blank','escape'=>false]);
                                echo "&nbsp;<span class='glyphicon glyphicon-link'></span>";
                                echo "</td>";
                            } else {
                                echo "<td>".$rep['string']."</td>";
                            }
                            echo "<td>".$rep['status']."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

