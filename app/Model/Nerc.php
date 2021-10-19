<?php

/**
 * Class NERC
 * Nerc model
 */
class Nerc extends AppModel
{

    // database table contains all entries defined at https://vocab.nerc.ac.uk/collection/P06/current/

    public $virtualFields=[
        'nercidver'=>"CONCAT(Nerc.nercid,':',Nerc.version)",
        'nercidsym'=>"CONCAT(Nerc.name,':',Nerc.symbol,':',Nerc.sameas)",
        'namecode'=>"CONCAT(Nerc.name,' (',Nerc.code,')')",
    ];

    /**
     * get a list of nerc defined units in an array
     * @param string|null $type
     */
    public function list(string $type=null)
    {
        $list = null;
        if(is_null($type)) {
            $list = $this->find('list',['fields'=>['code','namecode']]);
        } elseif($type=='qudt') {
            $hits = $this->find('list', ['fields' => ['nercid', 'sameas'], 'conditions' => ['sameas like' => '%qudt.org%']]);
            $list=[];
            foreach($hits as $nercid=>$hit) {
                $opts=json_decode($hit);
                foreach($opts as $opt) {
                    if(stristr($opt,'qudt.org')) {
                        $list[$nercid]=str_replace('http://qudt.org/vocab/unit/','',$opt);
                    }
                }
            }
        }
        return $list;
    }

    /**
     * ingest the nerc list of units
     * @throws Exception
     */
    public function ingest()
    {
        $file = simplexml_load_file(WWW_ROOT.'files/nerc/nerc.xml');
        $ns=$file->getNamespaces(true);
        foreach($file->xpath('//skos:Concept') as $entry) {
            $e=[];
            $e['nercid']=(string)$entry->xpath('dc:identifier')[0];
            $e['name']=(string)$entry->xpath('skos:prefLabel')[0];
            $e['symbol']=(string)$entry->xpath('skos:altLabel')[0];
            $e['definition']=(string)$entry->xpath('skos:definition')[0];
            $e['url']=(string)$entry->attributes('rdf',true)[0];
            $e['version']=(string)$entry->xpath('pav:version')[0];
            $e['deprecated']=(string)$entry->xpath('owl:deprecated')[0];
            $e['note']=(string)$entry->xpath('skos:note')[0];
            $e['lastupdated']=(string)$entry->xpath('dc:date')[0];
            $sameas=[];
            foreach($entry->xpath('owl:sameAs') as $item) {
                $sameas[]=(string)$item->attributes('rdf',true)[0];
                //debug($sameas);
            }
            $e['sameas']=str_replace("\\/", "/", json_encode($sameas));
            $this->create();
            $done=$this->save(['Nerc'=>$e]);
            debug($done);
        }
        exit;
    }

    /**
     * update the nerc table with the latest changes to the defined units
     * @throws Exception
     */
    public function update()
    {
        $file = simplexml_load_file(WWW_ROOT.'files/nerc/nerc_latest.xml');
        $ns=$file->getNamespaces(true);
        $nercids=$this->find('list',['fields'=>['nercid','id']]);
        //debug($nercs);exit;
        foreach($file->xpath('//skos:Concept') as $entry) {
            $e=[];
            $e['nercid']=(string)$entry->xpath('dc:identifier')[0];
            $e['name']=(string)$entry->xpath('skos:prefLabel')[0];
            $e['symbol']=(string)$entry->xpath('skos:altLabel')[0];
            $e['definition']=(string)$entry->xpath('skos:definition')[0];
            $e['url']=(string)$entry->attributes('rdf',true)[0];
            $e['version']=(string)$entry->xpath('pav:version')[0];
            $e['deprecated']=(string)$entry->xpath('owl:deprecated')[0];
            $e['note']=(string)$entry->xpath('skos:note')[0];
            $e['lastupdated']=(string)$entry->xpath('dc:date')[0];
            $sameas=[];
            foreach($entry->xpath('owl:sameAs') as $item) {
                $sameas[]=(string)$item->attributes('rdf',true)[0];
                //debug($sameas);
            }
            $e['sameas']=str_replace("\\/", "/", json_encode($sameas));
            if(isset($nercids[$e['nercid']])) {
                $entry=$this->find('first',['conditions'=>['id'=>$nercids[$e['nercid']]],'recursive'=>-1]);
                if(substr($e['lastupdated'],0,-2)==$entry['Nerc']['lastupdated']) {
                    echo $e['nercid']." is up to date<br/>";
                } else {
                    echo $e['nercid']." has been updated<br/>";exit;
                }
            } else {
                echo "New entry ".$e['nercid'];
                $this->create();
                $done=$this->save(['Nerc'=>$e]);
                debug($done);exit;
            }
        }
        exit;
    }

}