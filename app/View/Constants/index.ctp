<h2>Fundamental Constants</h2>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">SI System</h3>
			</div>
			<div class="list-group">
				<?php
				foreach($data as $id=>$name) {
					echo "<li class='list-group-item'>".$this->Html->link($name,'/constants/view/'.$id)."</li>";
				}
				?>
			</div>
		</div>
	</div>
</div>