<?php
App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('HttpSocket', 'Network/Http');

/**
 * Class Wiki
 * Wikidata model
 */
class Wiki extends AppModel
{
    public $useTable='wikidata';
    public $qpath='https://query.wikidata.org/sparql';
    public $virtualFields = ['labelcode'=>"CONCAT(Wiki.label,' (',Wiki.code,')')"];

    /**
     * get crosswalk list of wikidata units
     * choose a system from 'ucum','uncc','qudt','wolf','upac','ou20'
     * @param string $repsys
     * @return mixed
     */
    public function list(string $repsys=null)
    {
        if(is_null($repsys)) {
            return $this->find('list',['fields'=>['code','labelcode'],'order'=>'label']);
        } elseif(in_array($repsys,['ucum','uncc','qudt','wolf','upac','ou20'])) {
            return $this->find('list',['fields'=>[$repsys,'code'],'conditions'=>['NOT'=>[$repsys=>null]],'order'=>[$repsys]]);
        } else {
            return false;
        }
    }

    /**
     * get all available units and equivalencies in wikidata
     * @return false|mixed
     */
    public function getunits()
    {
        $HttpSocket = new HttpSocket();
        $path=$this->qpath;
        $sparql = 'SELECT ?unit ?unitLabel ?sub ?ucum ?uncc ?qudt ?wolf ?upac ?oum2 ?symb ';
        $sparql .= 'WHERE { ?sub wdt:P279 wd:Q47574 . ?unit wdt:P31 ?sub .';
        $sparql .= 'OPTIONAL { ?unit wdt:P7825 ?ucum .} ';
        $sparql .= 'OPTIONAL { ?unit wdt:P6512 ?uncc .} ';
        $sparql .= 'OPTIONAL { ?unit wdt:P2968 ?qudt .} ';
        $sparql .= 'OPTIONAL { ?unit wdt:P7007 ?wolf .} ';
        $sparql .= 'OPTIONAL { ?unit wdt:P4732 ?upac .} ';
        $sparql .= 'OPTIONAL { ?unit wdt:P8769 ?oum2 .} ';
        $sparql .= 'OPTIONAL { ?unit wdt:P5061 ?symb .} ';
        $sparql .= 'FILTER (?sub != wd:Q8142) ';
        $sparql .= 'FILTER (lang(?symb) = \'en\') ';
        $sparql .= 'FILTER ( ?ucum != "" || ?uncc!="" || ?qudt!="" || ?wolf!="" || ?upac!="" || ?oum2!="" || ?symb!="") ';
        $sparql .= 'SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }}';
        $sparql .= 'ORDER BY ?unitLabel';
        $url=$path."?query=".$sparql."&format=json";
        $json=$HttpSocket->get($url);
        $hits=json_decode($json,true);
        //debug($hits);exit;
        if(count($hits['results']['bindings'])==0) {
            return false;
        } else {
            return $hits['results']['bindings'];
        }
    }

}
