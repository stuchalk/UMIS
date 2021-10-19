<div class="container">
    <?php
    echo $this->Session->flash('auth');
    echo $this->Form->create('User',['url'=>['controller'=>'users','action'=>'login'],'class'=>'form-signin','inputDefaults'=>['label'=>false,'div'=>false]]);
    ?>
    <h2 class="form-signin-heading">Please sign in</h2>
    <label class="sr-only" for="UserUsername">Username</label>
    <?php echo $this->Form->input('username',['type'=>'text','class'=>'form-control','placeholder'=>"Username"]); ?>
    <label class="sr-only" for="UserPassword">Password</label>
    <?php echo $this->Form->input('password',['type'=>'password','class'=>'form-control','placeholder'=>"Password"]); ?>

    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    <?php echo $this->Form->end(); ?>
</div>