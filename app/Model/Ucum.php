<?php

/**
 * Class Ucum
 * Ucum model
 */
class Ucum extends AppModel
{
    // database table contains all entries defined in the 2017 file ucum-essence.xml

    public $virtualFields = ['codes'=>"CONCAT(Ucum.lcode,':',Ucum.ucode,':',Ucum.printSymbol)"];

    /**
     * get a list of the codes articulated in the ucum specfication
     * @throws Exception
     */
    public function list()
    {
        return $this->find('list',['fields'=>['id','codes']]);
    }

    public function ingest()
    {
        $file = simplexml_load_file(WWW_ROOT.'files/ucum/ucum-essence.xml');
        $file->registerXPathNamespace("u", "http://unitsofmeasure.org/ucum-essence");
        $entries=[];$type='unit'; // usie prefix, base-unit, or unit
        foreach($file->xpath('//u:'.$type) as $prefix) {
            $ucum = json_decode(json_encode($prefix),true);
            $found=$this->find('list',['conditions'=>['lcode'=>$ucum['@attributes']['Code']]]);
            //if($found) { continue; }
            $p=[];$p['type']=$type;
            $attrs=['Code','CODE','isMetric','class'];
            if(isset($ucum['@attributes']['isMetric'])) {
                $p['isMetric']=$ucum['@attributes']['isMetric'];unset($ucum['@attributes']['isMetric']);
            }
            if(isset($ucum['@attributes']['class'])) {
                $p['class']=$ucum['@attributes']['class'];unset($ucum['@attributes']['class']);
            }
            if(isset($ucum['@attributes']['Code'])) {
                $p['lcode']=$ucum['@attributes']['Code'];unset($ucum['@attributes']['Code']);
            }
            if(isset($ucum['@attributes']['CODE'])) {
                $p['ucode'] = $ucum['@attributes']['CODE'];unset($ucum['@attributes']['CODE']);
            }
            if(isset($ucum['@attributes']['dim'])) {
                $p['dim'] = $ucum['@attributes']['dim'];unset($ucum['@attributes']['dim']);
            }
            if(isset($ucum['@attributes']['isSpecial'])) {
                $p['isSpecial'] = $ucum['@attributes']['isSpecial'];unset($ucum['@attributes']['isSpecial']);
            }
            if(isset($ucum['@attributes']['isArbitrary'])) {
                $p['isArbitrary'] = $ucum['@attributes']['isArbitrary'];unset($ucum['@attributes']['isArbitrary']);
            }
            if(is_array($ucum['name'])) {
                $p['names']=implode(",",$ucum['name']);
            } else {
                $p['names']=$ucum['name'];
            }
            unset($ucum['name']);
            if(isset($ucum['printSymbol'])) {
                $p['printSymbol'] = $ucum['printSymbol'];unset($ucum['printSymbol']);
            }
            if(isset($ucum['property'])) {
                $p['property'] = $ucum['property'];unset($ucum['property']);
            }
            # value
            $val = json_decode(json_encode($prefix->value),true);
            unset($ucum['value']);
            if(isset($val['@attributes']['value'])) {
                $p['value'] = $val['@attributes']['value'];
                unset($val['@attributes']['value']);
            }
            if(isset($val['@attributes']['Unit'])) {
                $p['lunit'] = $val['@attributes']['Unit'];
                unset($val['@attributes']['Unit']);
            }
            if(isset($val['@attributes']['UNIT'])) {
                $p['uunit'] = $val['@attributes']['UNIT'];
                unset($val['@attributes']['UNIT']);
            }
            if(isset($val[0])) {
                $p['valuestr'] = $val[0];unset($val[0]);
            }
            if(isset($val['function'])) {
                $p['fname'] = $val['function']['@attributes']['name'];
                $p['fvalue'] = $val['function']['@attributes']['value'];
                $p['funit'] = $val['function']['@attributes']['Unit'];
                unset($val['function']);
            }
            # have we got everything?
            if(empty($ucum['@attributes'])) {
                unset($ucum['@attributes']);
            } else {
                debug($ucum);exit;
            }
            if(!empty($ucum)) {
                debug($ucum);exit;
            }
            if(empty($val['@attributes'])) {
                unset($val['@attributes']);
            } else {
                debug($val);exit;
            }
            if(!empty($val)) {
                debug($val);exit;
            }
            $this->create();
            $this->save(['Ucum'=>$p]);
            $this->clear();
            debug($p);
        }
        exit;
    }

    public function makesymbol()
    {
        // ucum definitions of units, prefixes and base-units are in the ucum table
        $usyms=$this->find('list',['fields'=>['names','printSymbol'],'conditions'=>['type'=>['unit','base-unit']],'recursive'=>-1]);
        $pfixs=$this->find('list',['fields'=>['names','printSymbol'],'conditions'=>['type'=>'prefix'],'recursive'=>-1]);
        $powers=['squared'=>2,'square'=>2,'cubed'=>3,'cubic'=>3,'root'=>'(1/2)','reciprocal'=>-1]; // textual powers
        debug($usyms);exit;
    }

}