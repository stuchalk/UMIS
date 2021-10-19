<?php

/* Configuration file for Fedora Commons v4 */

$config['scheme']='http';
$config['host']='localhost';
$config['port']='8080';
$config['path']='/rest/';
$config['api']=$config['scheme'].'://'.$config['host'].':'.$config['port'].$config['path'];

$config['rdf']['accept']=['application/ld+json','application/n-triples','application/rdf+xml',
    'application/x-turtle','application/xhtml+xml','application/xml','text/html',
    'text/n3','text/plain','text/rdf+n3','text/turtle'];

$config['fixity']['algorithm']='SHA-256';