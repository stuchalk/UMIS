<?php
//pr($data);exit;
$rstr=$data['Strng'];
$encs=$data['Encoding'];
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
                            <th class="col-md-6">String</th>
                            <th class="col-md-6">Format(s)</th>
                        </tr>
                        <?php
                        // representations
                        //debug($encs);
                        foreach($encs as $enc) {
                            echo "<tr>";
                            if (stristr($enc['string'],'<')||stristr($enc['string'],'&#')) {
                                echo "<td>".htmlspecialchars($enc['string'])."</td>";
                            } else {
                                echo "<td>".$enc['string']."</td>";
                            }
                            if(!empty($enc['Format'])) {
                                $fstr="";
                                foreach($enc['Format'] as $idx=>$f) {
                                    $fstr.=$f['abbrev'];
                                    if(($idx+1)<count($enc['Format'])) {
                                        $fstr.=", ";
                                    }
                                }
                                echo "<td><b>".$fstr."</b></td>";
                            } else {
                                echo "<td>".$enc['format']."</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

