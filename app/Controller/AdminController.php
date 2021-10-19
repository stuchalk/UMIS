<?php
/**
 * Class AdminController
 *
 */
class AdminController extends AppController
{

    public $uses = ['Factor', 'Iec', 'Ivoa', 'Nerc', 'Prefix', 'Quantitykind', 'Quantitysystem', 'Qudtunit',
        'Representation', 'Repsystem', 'Strng', 'Ucum', 'Udunit', 'Unece', 'Unit', 'Unitsystem', 'Wiki'
    ];

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    /**
     * add new unit representations
     * pick a unit that is in qudt that is not in the units table and add it with as many variants as possible
     * @param string $code
     */
    public function addunit(string $code)
    {
        if(!empty($this->request->data)) {
            $data=$this->request->data;
            $unit=$data['Unit'];
            if(empty($unit['id'])) {
                unset($unit['id']);
                $this->Unit->create();
            }
            foreach($unit as $n=>$v) {
                if($v=='') { $unit[$n]=null; }
            }
            $saved = $this->Unit->save(['Unit'=>$unit]);
            if(!isset($unit['id'])) {
                $unit = $saved['Unit'];
                $this->Qudtunit->id=$data['Qudt']['id'];
                $this->Qudtunit->saveField('qudt_id',$unit['id']);
            }
            //debug($unit);exit;
            $uid=$unit['id'];
            if($data['Unece']['code']!='') {
                $unid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'UNECE']]);
                // check if this code is already in the list of representations...
                $found = $this->Representation->find('first',['conditions'=>['repsystem_id'=>$unid['UNECE'],'unit_id'=>$uid],'recursive'=>-1]);
                if(!$found) {
                    // add to representations
                    // check strngs first
                    $uncode = $data['Unece']['code'];$strid = null;
                    $opts = ['name'=>'upright '.$uncode,'string'=>$uncode,'status'=>'current'];
                    $strid = $this->Strng->add($opts);
                    // representation
                    $opts=['unit_id'=>$uid,'repsystem_id'=>$unid['UNECE'],'strng_id'=>$strid];
                    $this->Representation->create();
                    $saved = $this->Representation->save(['Representation'=>$opts]);
                    if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                }
            }
            if($data['Ucum']['codes']!='') {
                $ucid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'UCUM']]);
                // check if this code is already in the list of representations...
                // code list is comma separated
                $codes = explode(', ',$data['Ucum']['codes']);
                $found = $this->Representation->find('all',['conditions'=>['repsystem_id'=>$ucid['UCUM'],'unit_id'=>$uid],'contain'=>['Strng'],'recursive'=>-1]);
                if(!$found) {
                    // add to representations
                    // check strngs first
                    foreach($codes as $uccode) {
                        $strid = null;
                        $opts = ['name'=>'upright '.$uccode,'string'=>$uccode,'status'=>'current'];
                        $strid = $this->Strng->add($opts);
                        // add representation
                        $opts=['unit_id'=>$uid,'repsystem_id'=>$ucid['UCUM'],'strng_id'=>$strid];
                        $this->Representation->create();
                        $saved = $this->Representation->save(['Representation'=>$opts]);
                        if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                    }
                } else {
                    // check each one is present
                    foreach($codes as $cidx=>$code) {
                        foreach($found as $hit) {
                            if($hit['Strng']['string']==$code) {
                                unset($codes[$cidx]);break;
                            }
                        }
                    }
                    if(!empty($codes)) {
                        // these need to be added...
                        foreach($codes as $uccode) {
                            $strid = null;
                            $opts = ['name'=>'upright '.$uccode,'string'=>$uccode,'status'=>'current'];
                            $strid = $this->Strng->add($opts);
                            // add representation
                            $opts=['unit_id'=>$uid,'repsystem_id'=>$ucid['UCUM'],'strng_id'=>$strid];
                            $this->Representation->create();
                            $saved = $this->Representation->save(['Representation'=>$opts]);
                            if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                        }
                    }
                }
            }
            if($data['Wiki']['code']!='') {
                $wdid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'WDATA']]);
                // check if this code is already in the list of representations...
                $found = $this->Representation->find('first',['conditions'=>['repsystem_id'=>$wdid['WDATA'],'unit_id'=>$uid],'contain'=>['Strng'],'recursive'=>-1]);
                if(!$found) {
                    // add to representations
                    // check strngs first
                    $wdcode = $data['Wiki']['code'];$strid = null;
                    $opts = ['name'=>'upright '.$wdcode,'string'=>$wdcode,'status'=>'current'];
                    $strid = $this->Strng->add($opts);
                    // representation
                    $opts=['unit_id'=>$uid,'repsystem_id'=>$wdid['WDATA'],'strng_id'=>$strid,'url'=>'https://www.wikidata.org/wiki/'.$wdcode];
                    $this->Representation->create();
                    $saved = $this->Representation->save(['Representation'=>$opts]);
                    if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                }
            }
            if($data['Nerc']['code']!='') {
                $ncid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'NERC']]);
                // check if this code is already in the list of representations...
                $found = $this->Representation->find('first',['conditions'=>['repsystem_id'=>$ncid['NERC'],'unit_id'=>$uid],'contain'=>['Strng'],'recursive'=>-1]);
                if(!$found) {
                    // add to representations
                    // check strngs first
                    $nccode = $data['Nerc']['code'];$strid = null;
                    $opts = ['name'=>'upright '.$nccode,'string'=>$nccode,'status'=>'current'];
                    $strid = $this->Strng->add($opts);
                    // representation
                    $opts=['unit_id'=>$uid,'repsystem_id'=>$ncid['NERC'],'strng_id'=>$strid,'url'=>'http://vocab.nerc.ac.uk/collection/P06/current/'.$nccode];
                    $this->Representation->create();
                    $saved = $this->Representation->save(['Representation'=>$opts]);
                    if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                }
            }
            if($data['Udunit']['symbol']!='') {
                $udid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'UDUNITS']]);
                // check if this code is already in the list of representations...
                $found = $this->Representation->find('first',['conditions'=>['repsystem_id'=>$udid['UDUNITS'],'unit_id'=>$uid],'contain'=>['Strng'],'recursive'=>-1]);
                if(!$found) {
                    // add to representations
                    // check strngs first
                    $udsym = $data['Udunit']['symbol'];$strid = null;
                    $opts = ['name'=>'upright '.$udsym,'string'=>$udsym,'status'=>'current'];
                    $strid = $this->Strng->add($opts);
                    // representation
                    $opts=['unit_id'=>$uid,'repsystem_id'=>$udid['UDUNITS'],'strng_id'=>$strid];
                    $this->Representation->create();
                    $saved = $this->Representation->save(['Representation'=>$opts]);
                    if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                }
            }
            if(isset($data['Other'])) {
                if(!empty($data['Other']['ieccodes'])) {
                    $iecid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'IEC']]);
                    // check if this code is already in the list of representations...
                    // code list is comma separated
                    $codes = explode(', ',$data['Other']['ieccodes']);
                    $found = $this->Representation->find('all',['conditions'=>['repsystem_id'=>$iecid['IEC'],'unit_id'=>$uid],'contain'=>['Strng'],'recursive'=>-1]);
                    if(!$found) {
                        // add to representations
                        // check strngs first
                        foreach($codes as $ieccode) {
                            $strid = null;
                            $encd = urlencode(str_replace('/','-',$ieccode));
                            $opts = ['name'=>'upright '.$ieccode,'string'=>$ieccode,'status'=>'current'];
                            $strid = $this->Strng->add($opts);
                            // add representation
                            $opts=['unit_id'=>$uid,'repsystem_id'=>$iecid['IEC'],'strng_id'=>$strid,'url'=>'https://cdd.iec.ch/cdd/iec61360/iec61360.nsf/Units/'.$encd];
                            $this->Representation->create();
                            $saved = $this->Representation->save(['Representation'=>$opts]);
                            if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                        }
                    } else {
                        // check each one is present
                        foreach($codes as $cidx=>$code) {
                            foreach($found as $hit) {
                                if($hit['Strng']['string']==$code) {
                                    unset($codes[$cidx]);break;
                                }
                            }
                        }
                        if(!empty($codes)) {
                            // these need to be added...
                            foreach($codes as $ieccode) {
                                $strid = null;
                                $encd = urlencode(str_replace('/','-',$ieccode));
                                $opts = ['name'=>'upright '.$ieccode,'string'=>$ieccode,'status'=>'current'];
                                $strid = $this->Strng->add($opts);
                                // add representation
                                $opts=['unit_id'=>$uid,'repsystem_id'=>$iecid['IEC'],'strng_id'=>$strid,'url'=>'https://cdd.iec.ch/cdd/iec61360/iec61360.nsf/Units/'.$encd];
                                $this->Representation->create();
                                $saved = $this->Representation->save(['Representation'=>$opts]);
                                if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                            }
                        }
                    }
                }
                if(!empty($data['Other']['ncit'])) {
                    $ntid = $this->Repsystem->find('list',['fields'=>['abbrev','id'],'conditions'=>['abbrev'=>'NCIT']]);
                    // check if this code is already in the list of representations...
                    // code list is comma separated
                    $code = $data['Other']['ncit'];
                    $found = $this->Representation->find('all',['conditions'=>['repsystem_id'=>$ntid['NCIT'],'unit_id'=>$uid],'contain'=>['Strng'],'recursive'=>-1]);
                    if(!$found) {
                        // add to representations
                        // check strngs first
                        $strid = null;
                        $opts = ['name'=>'upright '.$code,'string'=>$code,'status'=>'current'];
                        $strid = $this->Strng->add($opts);
                        // add representation
                        $opts=['unit_id'=>$uid,'repsystem_id'=>$ntid['NCIT'],'strng_id'=>$strid,'url'=>'http://purl.obolibrary.org/obo/'.$code];
                        $this->Representation->create();
                        $saved = $this->Representation->save(['Representation'=>$opts]);
                        if(!$saved) { echo "Error! Representation did not save";debug($opts);exit; }
                    }
                }
            }
            $this->redirect('/units');
        }
        $codes = []; $qudt = $unit = null;
        $found = $this->Qudtunit->find('first',['conditions'=>['code'=>$code],'contain'=>['Unit'],'recursive'=>-1]);
        if($found) {
            $qudt = $found['Qudtunit'];$codes['qudt'] = $code;
            $codes['qudt'] = null;
            if(!empty($found['Unit'])) { $unit = $found['Unit']; }
        } else {
            $codes['qudt'] = null;
        }

        // unece
        $codes['unece'] = null;$unlist = $this->Unece->list();
        if(isset($qudt['unececode'])&&!is_null($qudt['unececode'])) {
            $codes['unece'] = $qudt['unececode'];
        } elseif(isset($qudt['id'])) {
            $unq = $this->Unece->list('qudt');
            if(in_array($qudt['id'],array_keys($unq))) {
                $codes['unece']=$unq[$qudt['id']];
            }
        }

        // iecs
        // as of 5/20/21 there is no list of iec units easily available
        // although a Google search of '"list of units" iec cdd' gives 3720 results
        $codes['iec'] = json_decode($qudt['ieccodes'], true);

        // ucum string
        $ucumlist = $this->Ucum->list();
        $codes['ucum'] = json_decode($qudt['ucumcodes'], true);

        // ivoa
        // retrieves unit str using the command line application 'unity'
        if(!empty($codes['ucum'])) {
            $codes['ivoa'] = $this->Ivoa->getstr($codes['ucum'][0]);
        }

        // wikidata
        $codes['wiki'] = null; $wikilist = $this->Wiki->list('qudt');
        if(in_array($code,array_keys($wikilist))) { $codes['wiki'] = $wikilist[$code]; }
        $wikilist = $this->Wiki->list();
        // TODO: check wikidata for entries if in system...

        // nerc
        $codes['nerc'] = null; $nerclist = $this->Nerc->list('qudt');
        if(in_array($code,$nerclist)) { $codes['nerc'] = array_search($code,$nerclist); }
        $nerclist = $this->Nerc->list();

        // udunits
        $codes['ud'] = null; $udlist = $this->Udunit->list();
        if(in_array($code,array_keys($udlist))) { $codes['ud'] = $code; }

        // data from other tables to populate dropdown menus on page
        $qklist = $this->Quantitykind->find('list',['fields'=>['id','name'],'order'=>'name']);
        $uslist = $this->Unitsystem->find('list',['fields'=>['id','name'],'order'=>'name']);
        $pflist = $this->Prefix->find('list',['fields'=>['id','name'],'order'=>'name']);
        $ftlist = $this->Factor->find('list',['fields'=>['id','name'],'order'=>'name']);

        // send data to view
        $this->set('unit',$unit);
        $this->set('qudt',$qudt);
        $this->set('codes',$codes);
        $this->set('wikilist',$wikilist);
        $this->set('ucumlist',$ucumlist);
        $this->set('nerclist',$nerclist);
        $this->set('udlist',$udlist);
        $this->set('unlist',$unlist);
        $this->set('qklist',$qklist);
        $this->set('uslist',$uslist);
        $this->set('pflist',$pflist);
        $this->set('ftlist',$ftlist);
    }

    /**
     * website access to the NERC update function
     */
    public function nercupd()
    {
        $this->Nerc->update();
    }

    /**
     * website access to the QUDT unit ingest function
     */
    public function qingest()
    {
        $this->Qudtunit->ingest();
    }

    /**
     * website access to the QUDT QuantityKind ingest function
     */
    public function qkingest()
    {
        $this->Qudtqkind->ingest();
    }

    /**
     * website access to the IEC ingest function
     */
    public function iecingest()
    {
        $this->Iec->ingest();
    }

    public function temp()
    {
        $nrcs = $this->Nerc->find('list',['fields'=>['id','sameas'],'conditions'=>['sameas like'=>'%qudt%']]);
        foreach($nrcs as $nid=>$nrc) {
            $lnks = json_decode($nrc);
            foreach($lnks as $lnk) {
                if(stristr($lnk,'qudt')) {
                    $nrcs[$nid]=str_replace('http://qudt.org/vocab/unit/','',$lnk);break;
                }
            }
        }
        $qdts = $this->Qudtunit->find('list',['fields'=>['id','code']]);
        foreach($nrcs as $nrcid=>$code) {
            if(in_array($code,$qdts)) {
                $this->Nerc->save(['Nerc'=>['id'=>$nrcid,'qudtunit_id'=>array_search($code,$qdts)]]);
                echo "Code '".$code."' added<br/>";
            }
        }
        exit;
    }
}
