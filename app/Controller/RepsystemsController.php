<?php

/**
 * Class RepsystemsController
 */
class RepsystemsController extends AppController
{

    public $uses=['Repsystem','Dimensionvector','Quantitykind','Quantity','Representation',
        'Unit','Strng','Encoding','Ucum','Domain','RepsystemsUsecase'];

    /**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view','units','ingest');
	}
	
	/**
     * Repsystems index
     */
    public function index()
    {
        $data=$this->Repsystem->find('list',['fields'=>['id','title'],'order'=>['name']]);
        $this->set('data',$data);

        // Return data if called as requestAction
        if($this->request->is('requested')) { return $data; }
    }
    
    /**
     * Repsystems view
	 * @param int $id
     */
    public function view(int $id)
    {
    	if(!(is_numeric($id))) {
    		// translate from symbol to id
			$rsys=$this->Repsystem->find('first',['conditions'=>['abbrev'=>$id]]);
			$id=$rsys['Repsystem']['id'];
		}
    	
    	$c=['Representation'=>['Unit','Strng','conditions'=>['NOT'=>['unit_id'=>null]]],'Domain'];
        $data=$this->Repsystem->find('first',['conditions'=>['Repsystem.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $current=[];
        if($data['Repsystem']['status']=='legacy') {
        	$conds=['abbrev'=>$data['Repsystem']['abbrev'],'status'=>'current'];
        	$current=$this->Repsystem->find('first',['conditions'=>$conds,'recursive'=>-1]);
			$current=$current['Repsystem'];
		}
		$this->set('current',$current);
		$this->set('data',$data);
    }
	
	/**
	 * Get a list of units for a representation system (return as json)
	 * @param $id
	 */
    public function units($id)
	{
		$data=$this->Repsystem->units($id);
		//debug($data);exit;
        header('Content-Type: application/json');
        echo json_encode($data);exit;
    }

    /**
     * Ingest unit representations from a system
     * @param string $type
     */
    public function ingest(string $type='sp811')
    {
        if($type=='sp811') {
            $ingest=file(WWW_ROOT.'files'.DS.'ingest'.DS.'ingest_sp811.txt',FILE_IGNORE_NEW_LINES);
            foreach($ingest as $idx=>$row) { $ingest[$idx]=explode("\t",$row); }
            $fields=$ingest[0];unset($ingest[0]);
            debug($fields);
            foreach ($ingest as $uid=>$unit) {
                if($uid>=6) { debug($unit); }
                $encs=$this->Repsystem->vounits($unit[13]);
                if($uid>=6) { debug($encs);exit; }
                if(isset($encs['shortcode'])) {
                    // add unit representation to the database
                    debug($unit);debug($encs);
                    if($uid>=6) { exit; }
                    // 1) check dimvector already present
                    $found=$this->Dimensionvector->find('first',['conditions'=>['longcode'=>$encs['longcode']],'resursive'=>-1]);
                    if(!empty($found)) {
                        echo "Dimensionvector found...<br/>";
                        $dvid=$found['Dimensionvector']['id'];
                        if(is_null($found['Dimensionvector']['basesi_longcode'])) {
                            $this->Dimensionvector->id=$dvid;
                            $this->Dimensionvector->saveField('basesi_longcode',$encs['basesi_longcode']);
                        }
                    } else {
                        echo "Dimensionvector not found...<br/>";
                        // add dimension vector
                        $encs['name']=$unit[21];
                        $encs['description']=$unit[22];
                        $encs['quantitysystem_id']=$unit[19];
                        $encs['symbol']=$unit[23];

                        $this->Dimensionvector->create();
                        $added=$this->Dimensionvector->save(['Dimensionvector'=>$encs]);
                        $dvid=$added['Dimensionvector']['id'];
                        $dv=$added['Dimensionvector'];
                        echo "Dimensionvector (".$dv['longcode'].") added...<br/>";
                    }
                    // 2) check quantitykind already present
                    $found=$this->Quantitykind->find('first',['conditions'=>['dimensionvector_id'=>$dvid],'resursive'=>-1]);
                    if(!empty($found)) {
                        echo "Quantitykind found...<br/>";
                        $qkid=$found['Quantitykind']['id'];
                    } else {
                        echo "Quantitykind not found...<br/>";
                        // add dimension vector
                        $qk=[];
                        $qk['name']=$unit[15];
                        $qk['type']=$unit[16];
                        if($unit['17']!='') { $qk['description']=$unit[17]; }
                        $qk['symbol']=$unit[18];
                        $qk['baseunit_id']=null; // not created/retrieved yet - see below
                        $qk['quantitysystem_id']=$unit[19];
                        $qk['dimensionvector_id']=$dvid;
                        $this->Quantitykind->create();
                        $added=$this->Quantitykind->save(['Quantitykind'=>$qk]);
                        $qkid=$added['Quantitykind']['id'];
                        $qk=$added['Quantitykind'];
                        echo "Quantitykind '".$qk['name']."' added...<br/>";
                    }
                    // 3) check if quantity already present
                    $found=$this->Quantity->find('first',['conditions'=>['Quantity.name'=>$unit[0]],'resursive'=>-1]);
                    if(!empty($found)) {
                        echo "Quantity found...<br/>";
                        $qid=$found['Quantity']['id'];
                    } else {
                        echo "Quantity not found...<br/>";
                        // add quantity
                        $q=[];
                        $q['name']=$unit[0];
                        $q['quantitysystem_id']=1;
                        $q['quantitykind_id']=$qkid;
                        $this->Quantity->create();
                        $added=$this->Quantity->save(['Quantity'=>$q]);
                        $qid=$added['Quantity']['id'];
                        echo "Quantity '".$q['name']."' added...<br/>";
                    }
                    // 4) add base si unit for this unit (may be the same...)
                    $found=$this->Unit->find('first',['conditions'=>['Unit.shortcode'=>$encs['basesi_shortcode']],'resursive'=>-1]);
                    if(!empty($found)) {
                        echo "Base unit found...<br/>";
                        $baseunid=$found['Unit']['id'];
                        if(is_null($found['Unit']['alt_shortcode'])) {
                            $this->Unit->id=$baseunid;
                            $this->Unit->saveField('alt_shortcode',$encs['unit_shortcode']);
                        }
                    } else {
                        echo "Base unit not found...<br/>";
                        // add unit
                        $un=[];
                        $un['name']=$unit[1];
                        $un['quantitykind_id']=$qkid;
                        $un['unitsystem_id']=$unit[25];
                        $un['description']=null;
                        $un['prefix_id']=null;
                        $un['url']=null;
                        $un['dimensionvector_id']=$dvid;
                        $un['type']='SI derived';
                        $un['shortcode']=$encs['basesi_shortcode'];
                        $un['alt_shortcode']=$encs['unit_shortcode'];
                        $this->Unit->create();
                        $added=$this->Unit->save(['Unit'=>$un]);
                        $baseunid=$added['Unit']['id'];
                        // 4) update quantitykind with basesiunit id
                        $this->Quantitykind->id=$qkid;
                        $this->Quantitykind->saveField('baseunit_id',$baseunid);
                    }
                    // 5) add unit if needed
                    $found=$this->Unit->find('first',['conditions'=>['OR'=>['Unit.shortcode'=>$encs['unit_shortcode'],'Unit.alt_shortcode'=>$encs['unit_shortcode']]],'resursive'=>-1]);
                    if(!empty($found)) {
                        echo "Unit found...<br/>";
                        $unid=$found['Unit']['id'];
                    } else {
                        echo "Unit not found...<br/>";
                        // add unit
                        $un=[];
                        $un['name']=$unit[1];
                        $un['quantitykind_id']=$qkid;
                        $un['unitsystem_id']=$unit[25];
                        $un['description']=null;
                        $un['prefix_id']=null; // may need to move to join table
                        $un['url']=null;
                        $un['dimensionvector_id']=$dvid;
                        $un['type']=$unit[20];
                        $un['shortcode']=$encs['basesi_shortcode'];
                        $this->Unit->create();
                        $added=$this->Unit->save(['Unit'=>$un]);
                        $unid=$added['Unit']['id'];
                        $un=$added['Unit'];
                        echo "Unit '".$un['name']."' added...<br/>";
                    }
                    // 6) added strng (yes table has no 'i' so that it does not conflict with cakephp controlled word 'string')
                    // strng labels ('string' field) are encoded in HTML for display
                    $found=$this->Strng->find('all',['conditions'=>['string'=>$unit[2]],'resursive'=>-1]);
                    $strngid=null;
                    if(!empty($found)) {
                        if(count($found)==1) {
                            echo "String found...<br/>";
                            $strngid=$found[0]['Strng']['id'];
                        } else {
                            foreach($found as $sidx=>$strng) {
                                if ($strng['Strng']['string']==$unit[2]) {
                                    $strngid=$found[$sidx]['Strng']['id'];break;
                                }
                            }
                        }
                    } else {
                        echo "String not found...<br/>";
                        // add strng
                        $st=[];
                        $st['name']=$unit[5];
                        $st['string']=$unit[2];
                        $st['status']=$unit[6];
                        $st['reason']=$unit[7];
                        if($st['reason']=='') { $st['reason']=null; }
                        $this->Strng->create();
                        $added=$this->Strng->save(['Strng'=>$st]);
                        $strngid=$added['Strng']['id'];
                    }
                    // 7) add strng encodings
                    $encodings=['ascii'=>$unit[8],'html'=>$unit[9],'latex'=>$unit[10],'siunitx1'=>$unit[11],'siunitx2'=>$unit[12],'ivoa'=>$unit[13]];
                    foreach($encodings as $format=>$string) {
                        if(stristr($format,'siunitx')) { $format='siunitx'; }
                        $found=$this->Encoding->find('first',['conditions'=>['Encoding.strng_id'=>$strngid,'Encoding.format'=>$format],'recursive'=>-1]);
                        if(empty($found)) {
                            // add encoding
                            $en=[];
                            $en['strng_id']=$strngid;
                            $en['string']=$string;
                            if($format=='siunitx1'||$format=='siunitx2') { $format='siunitx'; }
                            $en['format']=$format;
                            $this->Encoding->create();
                            $this->Encoding->save(['Encoding'=>$en]);
                            $this->Encoding->clear();
                            echo "Added ".$string."<br/>";
                        } else {
                            echo "Already added ".$string."<br/>";
                        }
                    }
                    // 8) create the representation
                    $found=$this->Representation->find('first',['conditions'=>['unit_id'=>$unid,'repsystem_id'=>$unit[3]],'resursive'=>-1]);
                    if(!empty($found)) {
                        echo "Representation found...<br/>";
                        $rpid=$found['Representation']['id'];
                    } else {
                        echo "Representation not found...<br/>";
                        // add strng
                        $rp=[];
                        $rp['unit_id']=$unid;
                        $rp['repsystem_id']=$unit[3];
                        $rp['strng_id']=$strngid;
                        if($unit[4]=='') {
                            $rp['url']=null;
                        } else {
                            $rp['url']=$unit[4];
                        }
                        $this->Representation->create();
                        $added=$this->Representation->save(['Representation'=>$rp]);
                        $rpid=$added['Representation']['id'];
                        $rp=$added['Representation'];
                        echo "Representation ".$rpid." added...<br/>";
                    }
                } else {
                    echo "Somethings amiss...<br/>";
                    debug($unit);debug($encs);exit;
                }
            }
        } elseif($type=='ucum') {
            $outcome=$this->Ucum->ingest();

        }
        exit;
    }

    /**
     * Add a new unit representation system
     */
    public function add()
    {
        if(!empty($this->request->data)) {
            $data=$this->request->data;
            foreach($data['Repsystem'] as $n=>$v) {
                if($v=='') { unset($data['Repsystem'][$n]); }
            }
            //debug($data);exit;
            $this->Repsystem->create();
            $this->Repsystem->save($data);
            $this->redirect('/repsystems');
        } else {
            $type = $this->Repsystem->getColumnType('type');
            $json = str_replace(['enum(',')','\''],['[',']','"'],$type);
            $temp = json_decode($json,true);
            $typeopts = []; foreach($temp as $opt) { $typeopts[$opt]=$opt; }
            $this->set('typeopts',$typeopts);
            $status = $this->Repsystem->getColumnType('status');
            $json = str_replace(['enum(',')','\''],['[',']','"'],$status);
            $temp = json_decode($json,true);
            $statopts = []; foreach($temp as $opt) { $statopts[$opt]=$opt; }
            $this->set('statopts',$statopts);
            $domopts = $this->Domain->find('list',['fields'=>['id','title'],'order'=>'title']);
            $this->set('domopts',$domopts);
        }
    }

    /**
     * display preferred unit reps for different applications
     * @param int $repsysid
     * @param int $unitid
     */
    public function hypo(int $repsysid, int $unitid) {
        $repsys=$this->Repsystem->find('first',['conditions'=>['id'=>$repsysid],'recursive'=>-1]);
        $unit=$this->Unit->find('first',['conditions'=>['id'=>$unitid],'recursive'=>-1]);
        $cnds=['RepsystemsUsecase.repsystem_id'=>$repsysid,'RepsystemsUsecase.unit_id'=>$unitid];$ctns=['Usecase','Representation'=>['Strng','Repsystem']];
        $cases=$this->RepsystemsUsecase->find('all',['conditions'=>$cnds,'contain'=>$ctns,'recursive'=>-1]);
        $this->set('repsys',$repsys);
        $this->set('unit',$unit);
        $this->set('cases',$cases);
    }
}