<?php
//pr($quans);pr($syss);exit;
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.qkind').on('change', function(){
            var qid = $(this).find("option:selected").val();
            $('#unitlist').empty();
            if(qid!="") {
                var url = "https://umis.stuchalk.domains.unf.edu/units/unlist/" + qid;
                $.getJSON(url).done(function(data) {
                    $.each(data[0], function(i,qk) {
                        var badge=$('<span class="badge">' + qk + '</span>');
                        $('#unitlist').append(badge);
                    });
                    $('#units').show();
                });
                // get quantity system ID for quantity
                var qsid=0;
                $.ajax({
                    method: "GET",
                    url: "https://umis.stuchalk.domains.unf.edu/quantities/getfield/" + qid + "/quantitysystem_id",
                    async: false
                }).done(function( value ) {
                    qsid=value;
                });
                var url = "https://umis.stuchalk.domains.unf.edu/prefixes/plist/" + qsid;
                $.getJSON(url).done(function(data) {
                    $.each(data[0], function(i,pre) {
                        $('#UnitPrefixId').append($('<option>', {
                                value: i,
                                text: pre
                            })
                        );
                    });
                    $('.prefix').show();
                });
            } else {
                $('#unitlist').empty();
                $('#units').hide();
                $('.prefix').hide();
            }
            return false;
        });
    });
</script>

<div class="row" style="margin-top: 10px;">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Add Unit Representation System</h3>
            </div>
            <div class="panel-body">
                <?php
                $options=['class'=>'form-horizontal','enctype'=>'multipart/form-data','inputDefaults'=>['label'=>false,'selected'=>'empty','div'=>false]];
                echo $this->Form->create('Repsystem',$options);
                ?>
                <div id="name" class="form-group">
                    <label for="RepsystemName" class="col-md-2 control-label">Name:</label>
                    <div class="col-md-5">
                        <?php echo $this->Form->input('name', ['type'=>'text','class'=>'form-control','placeholder'=>'Name of the system']); ?>
                    </div>
                </div>
                <div id="description" class="form-group">
                    <label for="RepsystemDescription" class="col-md-2 control-label">Description:</label>
                    <div class="col-md-10">
                        <?php echo $this->Form->input('description', ['type'=>'text','class'=>'form-control','placeholder'=>'Description of the system...']); ?>
                    </div>
                </div>
                <div id="url" class="form-group">
                    <label for="RepsystemUrl" class="col-md-2 control-label">Website URL:</label>
                    <div class="col-md-10">
                        <?php echo $this->Form->input('url', ['type'=>'text','class'=>'form-control','placeholder'=>'Website about the system...']); ?>
                    </div>
                </div>
                <div id="url" class="form-group">
                    <label for="RepsystemRepository" class="col-md-2 control-label">Repository:</label>
                    <div class="col-md-10">
                        <?php echo $this->Form->input('repository', ['type'=>'text','class'=>'form-control','placeholder'=>'System repository...']); ?>
                    </div>
                </div>
                <div id="abbrev_version" class="form-group">
                    <label for="RepsystemAbbrev" class="col-md-2 control-label">Abbreviation:</label>
                    <div class="col-md-4">
                        <?php echo $this->Form->input('abbrev', ['type'=>'text','class'=>'form-control','placeholder'=>'System abbreviation...']); ?>
                    </div>
                    <label for="RepsystemVersion" class="col-md-2 control-label">Version:</label>
                    <div class="col-md-4">
                        <?php echo $this->Form->input('version', ['type'=>'text','class'=>'form-control','placeholder'=>'System version...']); ?>
                    </div>
                </div>
                <div id="type_status_domain" class="form-group">
                    <label for="RepsystemType" class="col-md-2 control-label">Type:</label>
                    <div class="col-md-2">
                        <?php echo $this->Form->input('type', ['type'=>'select','options'=>$typeopts,'class'=>'form-control','empty'=>'Pick type...']); ?>
                    </div>
                    <label for="RepsystemStatus" class="col-md-2 control-label">Status:</label>
                    <div class="col-md-2">
                        <?php echo $this->Form->input('status', ['type'=>'select','options'=>$statopts,'class'=>'form-control','empty'=>'Pick status...']); ?>
                    </div>
                    <label for="RepsystemDomainId" class="col-md-2 control-label">Domain:</label>
                    <div class="col-md-2">
                        <?php echo $this->Form->input('domain_id', ['type'=>'select','options'=>$domopts,'class'=>'form-control','empty'=>'Pick domain...']); ?>
                    </div>
                </div>
                <div id="add" class="form-group">
                    <div id="button" class="col-md-12">
                        <button type="submit" class="btn btn-default pull-right">Add Representation System</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>