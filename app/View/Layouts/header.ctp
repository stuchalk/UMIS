<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid" id="navfluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="sr-only">Toggle navigation</span>
            </button>
            <a class="navbar-brand" href="<?php echo $this->Html->url('/'); ?>"><b>NIST UMIS</b></a>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false" style="font-size: 18px;">Browse the system<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><?php echo $this->Html->link('Units', '/units/index'); ?></li>
                        <li><?php echo $this->Html->link('Unit Systems', '/unitsystems/index'); ?></li>
                        <li><?php echo $this->Html->link('Unit Representation Systems', '/repsystems/index'); ?></li>
                        <li><?php echo $this->Html->link('Unit Representations', '/representations/index'); ?></li>
                        <li><?php echo $this->Html->link('Quantity Systems', '/quantitysystems/index'); ?></li>
                        <li><?php echo $this->Html->link('Quantity Kinds', '/quantitykinds/index'); ?></li>
                        <li><?php echo $this->Html->link('Quantities', '/quantities/index'); ?></li>
                        <li><?php echo $this->Html->link('Dimension Vectors', '/dimensionvectors/index'); ?></li>
                        <li><?php echo $this->Html->link('Fundamental Constants', '/constants/index'); ?></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false" style="font-size: 18px;">Services<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><?php echo $this->Html->link('Unit Crosswalks', '/units/crosswalk'); ?></li>
                        <li><?php echo $this->Html->link('REST API', '/api/index'); ?></li>
                    </ul>
                </li>
            </ul>
            <?php if ($this->Session->read('Auth.User.type') == 'admin'||$this->Session->read('Auth.User.type') == 'superadmin') { ?>
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false" style="font-size: 18px;">Admin Services<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link('To be added...', ''); ?></li>
                        </ul>
                    </li>
                </ul>
            <?php } ?>
            <ul class="nav navbar-nav navbar-right">
                <?php
                echo $this->Form->create('Unit',['url'=>'/units/search','class'=>'navbar-form navbar-left']);
                echo $this->Form->input('unit',['type'=>'text','class'=>'form-control','div'=>false,'label'=>false,'placeholder'=>'Search units...']);
                echo $this->Form->end();
                ?>
                <div class="navbar-text text-danger">
                    <?php echo $this->Flash->render(); ?>
                </div>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" style="font-size: 18px;" aria-haspopup="true" aria-expanded="false">
                        <?php
                        if($this->Session->read('Auth.User')) {
                            echo $this->Session->read('Auth.User.fullname');
                        } else {
                            echo "My Units";
                        }
                        ?>
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php
                        if($this->Session->read('Auth.User')) {
                            echo "<li>".$this->Html->link('Logout','/users/logout')."</li>";
                        } else {
                            echo "<li>".$this->Html->link('Login','/users/login')."</li>";
                        }
                        ?>
                    </ul>
                </li>
            </ul>
        </div><!-- /.nav-collapse -->
    </div><!-- /.container-fluid -->
</nav>