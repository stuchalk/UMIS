<?php

App::uses('AppModel', 'Model');
App::uses('HttpSocket', 'Network/Http');

Configure::config('default', new PhpReader());
Configure::load('Fedora.config','default');

class FedoraAppModel extends AppModel {

    // Needed for URL access to plugin

}