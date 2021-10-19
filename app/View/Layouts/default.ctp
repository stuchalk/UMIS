<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo $this->Html->charset(); ?>
    <title>UMIS <?php echo $title_for_layout; ?></title>
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('firefox');
    echo $this->Html->css('jquery-ui');
    echo $this->Html->css('jquery-ui.structure');
    echo $this->Html->css('jquery-ui.theme');
    echo $this->Html->css('bootstrap.min');
    echo $this->Html->css('bootstrap-theme.min');
    echo $this->Html->css('sticky-footer-navbar');
    echo $this->Html->css('boottabs');
    echo $this->Html->css('shadows');
    echo $this->Html->css('signin');
    echo $this->Html->css('umis');
    echo $this->Html->script('jquery.min');
    echo $this->Html->script('jquery-ui');
    //echo $this->Html->script('jqcake');
    echo $this->Html->script('bootstrap.min');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
<?php include('header.ctp'); ?>
<div class="container theme-showcase" role="main">
    <?php echo $this->fetch('content'); ?>
</div>
<?php include('footer.ctp'); ?>
</body>
</html>