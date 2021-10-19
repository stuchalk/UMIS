<?php

App::uses('PhpReader', 'Configure');

Configure::config('default', new PhpReader());
Configure::load('Fedora.config','default');

class FedoraAppController extends AppController {

    // Needed for URL access to plugin

}