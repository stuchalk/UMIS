<?php
App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('HttpSocket', 'Network/Http');

/**
 * Class Unece
 * Unece model
 */
class Unece extends AppModel
{

    public $useTable='unece';
    public $qpath='https://query.wikidata.org/sparql';
    public $virtualFields = ['namecode'=>"CONCAT(Unece.name,' (',Unece.code,')')"];

    /**
     * get crosswalk list of unece units
     * @param string|null $type
     */
    public function list(string $type=null)
    {
        if(is_null($type)) {
            return $this->find('list',['fields'=>['code','namecode'],'order'=>'name']);
        } elseif($type=='qudt') {
            return $this->find('list',['fields'=>['qudtunit_id','code'],'order'=>'name','conditions'=>['NOT'=>['qudtunit_id'=>null]]]);
        }
    }
}
