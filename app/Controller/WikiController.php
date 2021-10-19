<?php

/**
 * Class WikiController
 */
class WikiController extends AppController
{

    public $uses=['Wiki','Qudtunit'];

    /**
     * update the wikidata table
     */
    public function update()
    {
        $units = $this->Wiki->getunits();
        foreach($units as $idx=>$u) {
            $unit=[];
            $unit['code']=str_replace('http://www.wikidata.org/entity/','',$u['unit']['value']);
            $unit['label']=$u['unitLabel']['value'];
            if(isset($u['ucum'])) { $unit['ucum']=$u['ucum']['value']; }  // in json format
            if(isset($u['uncc'])) { $unit['uncc']=$u['uncc']['value']; }
            if(isset($u['qudt'])) { $unit['qudt']=$u['qudt']['value']; }
            if(isset($u['wolf'])) { $unit['wolf']=$u['wolf']['value']; }
            if(isset($u['upac'])) { $unit['upac']=$u['upac']['value']; }
            if(isset($u['oum2'])) { $unit['oum2']=$u['oum2']['value']; }
            if(isset($u['symb'])) { $unit['symbol']=$u['symb']['value']; }
            // check if this unit is already in the system
            $exists = $this->Wiki->find('list',['fields'=>['code','id'],'conditions'=>['code'=>$unit['code']]]);
            if(!$exists) {
                $this->Wiki->create();
            } else {
                $this->Wiki->id=$exists[$unit['code']];
            }
            $saved = $this->Wiki->save(['Wiki'=>$unit]);
            $wid = $this->Wiki->id;
            echo "Added/updated Wikidata unit ".$unit['code']."<br/>";
            // link to qudt units
            if(!empty($saved['Wiki']['qudt'])) {
                // in qudtunit table?
                $code = $saved['Wiki']['qudt'];
                $found = $this->Qudtunit->find('list',['fields'=>['code','id'],'conditions'=>['code'=>$code]]);
                if($found) {
                    $this->Wiki->id=$wid;
                    $this->Wiki->saveField('qudtunit_id',$found[$code]);
                }
            }
        }
        debug($units);exit;

    }

}
