<div class="row">
    <div class="col-sm-6 col-sm-offset-3">
        <h3>Register an Account on ChemExtractor</h3>
<?php
echo $this->Form->create('User', ['url'=>['controller'=>'users','action'=>'register']]);
echo $this->Form->input('username', ['type'=>'text','class'=>['form-control']]);
echo $this->Form->input('password', ['type'=>'password','class'=>['form-control']]);
echo $this->Form->input('firstname', ['type'=>'text','label'=>'First Name','class'=>['form-control']]);
echo $this->Form->input('lastname', ['type'=>'text','label'=>'Last Name','class'=>['form-control']]);
echo $this->Form->input('email', ['type'=>'text','label'=>'Email Address','class'=>['form-control']]);
echo $this->Form->input('phone', ['type'=>'text','label'=>'Phone Number','class'=>['form-control']]);
echo $this->Form->input('type', ['type'=>'hidden','value'=>'registered']);
echo $this->Form->end(['label'=>'Register','div'=>['class'=>['pull-right'],'style'=>['margin-top: 10px;']]]);
?>
    </div>
</div>