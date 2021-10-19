<?php

/**
 * Class UnitsController
 */
class UnitsController extends AppController
{
	
	public $uses=['Unit','Quantitykind','Quantitysystem','Repsystem','Nerc','Qudtunit','Qudtqkind','Strng'];
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view','unlist','crosswalk','search','ivoa');
	}
	
    /**
	 * Units index
	 * @return mixed
	 */
	public function index()
	{
		$f=['Unit.id','Unit.name','Quantitykind.name'];
		$c=['Quantitykind'];$o=['Quantitykind.name','Unit.name'];
	    $data=$this->Unit->find('list',['fields'=>$f,'contain'=>$c,'order'=>$o]);
		$this->set('data',$data);
		
		// Return data if called as requestAction
		if($this->params['requested']) { return $data; }
	}
	
	/**
	 * Add index
	 * @return mixed
	 */
	public function add()
	{
		if(!empty($this->request->data)) {
			//debug($this->request->data);exit;
			$data=$this->request->data;
			if($data['Unit']['prefix_id']!='') {
				$data['Unit']['type']=$data['Unit']['type'].'Prefixed';
			}
			$this->Unit->create();
			if($this->Unit->save($data)) {
				$this->Flash->set('The unit has been added');
				$this->redirect(['action' => 'index']);
			} else {
				$this->Flash->set('The unit could not be added.');
			}
		} else {
			$qkinds=$this->Quantitykind->find('list',['fields'=>['id','name'],'recursive'=>-1]);
			$this->set('qkinds',$qkinds);
		}
	}
	
	/**
	 * Units view
     * @param int $id
     * @param string $output
	 * @return mixed
	 */
	public function view($id,$output="html")
	{
		$c=['FromEUnit'=>['To','From','Prefix','Constant'],
			'ToEUnit'=>['To','From','Prefix','Constant'],
			'FromCUnit'=>['To','From','Prefix','Constant'],
			'ToCUnit'=>['To','From','Prefix','Constant'],
			'Unitsystem','Prefix','Quantitykind'=>[
				'Dimensionvector','Nym'=>['Domain'],'Quantitysystem','Quantity'],
			'Representation'=>[
				'Strng'=>['Encoding'=>['Format']],'Repsystem', 'Unitsystem'
			],
			'Nym'
		];
		$data=$this->Unit->find('first',['conditions'=>['Unit.id'=>$id],'contain'=>$c,'recursive'=>-1]);
		// hack to deal with Equivalent Model From alias not taking the from_unit ID to find Unit
		if(isset($data['ToEUnit'])&&!empty($data['ToEUnit'])) {
			foreach($data['ToEUnit'] as $idx=>$tunit) {
				if(isset($tunit['From'])&&!empty($tunit['From'])) {
					unset($tunit['From']);
					$fromid=$tunit['from_unit'];
					$f=$this->Unit->find('first',['conditions'=>['id'=>$fromid],'recursive'=>-1]);
					$data['ToEUnit'][$idx]['From']=$f['Unit'];
				}
				if(isset($tunit['To'])&&!empty($tunit['To'])) {
					unset($tunit['To']);
					$toid=$tunit['to_unit'];
					$t=$this->Unit->find('first',['conditions'=>['id'=>$toid],'recursive'=>-1]);
					$data['ToEUnit'][$idx]['To']=$t['Unit'];
				}
			}
		}
		if(isset($data['FromEUnit'])&&!empty($data['FromEUnit'])) {
			foreach($data['FromEUnit'] as $idx=>$funit) {
				if(isset($funit['From'])&&!empty($funit['From'])) {
					unset($funit['From']);
					$fromid=$funit['from_unit'];
					$f=$this->Unit->find('first',['conditions'=>['id'=>$fromid],'recursive'=>-1]);
					$data['FromEUnit'][$idx]['From']=$f['Unit'];
				}
				if(isset($funit['To'])&&!empty($funit['To'])) {
					unset($funit['To']);
					$toid=$funit['to_unit'];
					$t=$this->Unit->find('first',['conditions'=>['id'=>$toid],'recursive'=>-1]);
					$data['FromEUnit'][$idx]['To']=$t['Unit'];
				}
			}
		}
		if(isset($data['ToCUnit'])&&!empty($data['ToCUnit'])) {
			foreach($data['ToCUnit'] as $idx=>$tunit) {
				if(isset($tunit['From'])&&!empty($tunit['From'])) {
					unset($tunit['From']);
					$fromid=$tunit['from_unit'];
					$f=$this->Unit->find('first',['conditions'=>['id'=>$fromid],'recursive'=>-1]);
					$data['ToCUnit'][$idx]['From']=$f['Unit'];
				}
				if(isset($tunit['To'])&&!empty($tunit['To'])) {
					unset($tunit['To']);
					$toid=$tunit['to_unit'];
					$t=$this->Unit->find('first',['conditions'=>['id'=>$toid],'recursive'=>-1]);
					$data['ToCUnit'][$idx]['To']=$t['Unit'];
				}
			}
		}
		if(isset($data['FromCUnit'])&&!empty($data['FromCUnit'])) {
			foreach($data['FromCUnit'] as $idx=>$funit) {
				if(isset($funit['From'])&&!empty($funit['From'])) {
					unset($funit['From']);
					$fromid=$funit['from_unit'];
					$f=$this->Unit->find('first',['conditions'=>['id'=>$fromid],'recursive'=>-1]);
					$data['FromCUnit'][$idx]['From']=$f['Unit'];
				}
				if(isset($funit['To'])&&!empty($funit['To'])) {
					unset($funit['To']);
					$toid=$funit['to_unit'];
					$t=$this->Unit->find('first',['conditions'=>['id'=>$toid],'recursive'=>-1]);
					$data['FromCUnit'][$idx]['To']=$t['Unit'];
				}
			}
		}
		if($output=="html") {
		    $reps=['preferred'=>[],'current'=>[],'alternate'=>[],'discouraged'=>[],'legacy'=>[],'incorrect'=>[]];
		    foreach($data['Representation'] as $rep) {
		        $reps[$rep['Strng']['status']][$rep['Strng']['string']]=['string'=>$rep['Strng'],'systems'=>[]];
		    }
            foreach($data['Representation'] as $rep) {
                $temp=['url'=>$rep['url'],'Repsystem'=>$rep['Repsystem'],'Unitsystem'=>$rep['Unitsystem']];
                if(!empty($rep['Repsystem'])) {
                    $reps[$rep['Strng']['status']][$rep['Strng']['string']]['systems'][$rep['Repsystem']['name']]=$temp;
                } elseif(!empty($rep['Unitsystem'])) {
                    $reps[$rep['Strng']['status']][$rep['Strng']['string']]['systems'][$rep['Unitsystem']['name']]=$temp;
                } else {
                    $reps[$rep['Strng']['status']][$rep['Strng']['string']]['systems']['General']=$temp;
                }
            }
            //debug($data['Representation']);
		    //debug($reps);exit;
            $this->set('reps',$reps);
            $this->set('data',$data);
        } elseif($output=="json") {
		    $out=[];
		    $unit=$data['Unit'];
            $out['id']=$unit['id'];
			$out['pid']='umis:U'.$unit['id'];
			$out['name']=$unit['name'];
            $out['type']=$unit['type'];
            $out['description']=$unit['description'];
            if(!empty($data['Nym'])) {
            	$nyms=[];
            	foreach($data['Nym'] as $nym) {
            		$nyms[]=$nym['value'];
				}
				$out['synonyms']=$nyms;
			}
            $out['source']=$unit['url'];
            $out['link']='https://umis.stuchalk.domains.unf.edu/units/view/'.$unit['id'];
            $out['date']=$unit['updated'];
            // prefix
            if($data['Prefix']['id']!=null) {
                $p=$data['Prefix'];
                $pre=[];
                $pre['id']=$p['id'];
                $pre['name']=$p['name'];
                $pre['symbol']=$p['symbol'];
                $pre['url']='https://umis.stuchalk.domains.unf.edu/prefix/view/'.$p['id'];
                $out['prefix'][]=$pre;
            }
            // quantity kind
            $qk=$data['Quantitykind'];
            $out['quantitykind']['id']=$qk['id'];
            $out['quantitykind']['name']=$qk['name'];
            $out['quantitykind']['link']='https://umis.stuchalk.domains.unf.edu/quantitykinds/view/'.$qk['id'];
			// unit system
			$us=$data['Unitsystem'];
			$out['unitsystem']['id']=$us['id'];
			$out['unitsystem']['name']=$us['name'];
			$out['unitsystem']['source']=$us['url'];
			$out['unitsystem']['link']='https://umis.stuchalk.domains.unf.edu/unitsystems/view/'.$us['id'];
			// quantity system
            $qs=$qk['Quantitysystem'];
            $out['quantitysystem']['id']=$qs['id'];
            $out['quantitysystem']['name']=$qs['name'];
            $out['quantitysystem']['source']=$qs['url'];
            $out['quantitysystem']['link']='https://umis.stuchalk.domains.unf.edu/quantitysystems/view/'.$qs['id'];
            // dimension vector
            $dv=$qk['Dimensionvector'];
            $out['dimensionality']['id']=$dv['id'];
            $out['dimensionality']['name']=$dv['name'];
            $out['dimensionality']['shortcode']=$dv['shortcode'];
            $out['dimensionality']['longcode']=$dv['longcode'];
            $out['dimensionality']['symbol']=$dv['symbol'];
            $out['dimensionality']['link']='https://umis.stuchalk.domains.unf.edu/umis/dimensionvectors/view/'.$dv['id'];
            // representations
            $reps=$data['Representation'];
            //debug($reps);exit;
            $out['representations']=[];
            foreach($reps as $rep) {
                $str=$rep['Strng'];
                $r=[];
                $r['id']=$rep['id'];
                $r['string']=$str['string'];
                $r['status']=$str['status'];
                $r['link']='https://umis.stuchalk.domains.unf.edu/representations/view/'.$rep['id'];
                if(!empty($rep['url'])) { $r['source']=$rep['url']; }
                if(!empty($str['Encoding'])) {
                    $r['encodings']=[];
                    foreach($str['Encoding'] as $enc) {
                        $r['encodings'][]=['format'=>$enc['format'],'string'=>$enc['string']];
                    }
                }
                $out['representations'][]=$r;
            }
            // equivalents
			$out['equivalents']=[];
			if(!empty($data['FromEUnit'])) {
				$froms=$data['FromEUnit'];
                foreach($froms as $from) {
                    $f="1 ".strtolower($unit['name'])." = ";
                    if(!is_null($from['factor'])) {
                        $f.=$from['factor'];
                    } elseif(!empty($from['Prefix'])) {
                        $f.=$from['Prefix']['value'];
                    } elseif(!empty($from['Constant'])) {
                        $f.=$from['Constant']['value'];
                    }
                    $f.=" ".strtolower($from['To']['name']);
                    $out['equivalents'][]=$f;
                }
            }
            if(!empty($data['ToEUnit'])) {
                $tos=$data['ToEUnit'];
                foreach($tos as $to) {
                    $t="1 ".strtolower($unit['name'])." = ";
                    if(!is_null($to['factor'])) {
                        $t.=$to['factor'];
                    } elseif(!empty($to['Prefix'])) {
                        $t.=$to['Prefix']['inverse'];
                    } elseif(!empty($to['Constant'])) {
                        $t.=$to['Constant']['inverse'];
                    }
                    $t.=" ".strtolower($to['From']['name']);
                    $out['equivalents'][]=$t;
                }
            }
			if(empty($out['equivalents'])) { unset($out['equivalents']); }
            // correspondents
			$out['correspondents']=[];
			if(!empty($data['FromCUnit'])) {
				$froms=$data['FromCUnit'];
				foreach($froms as $from) {
					$f="1 ".strtolower($unit['name'])." <-> ";
					if(!is_null($from['factor'])) {
						$f.=$from['factor'];
					} elseif(!is_null($from['factoreqn'])) {
						$f.=$from['factoreqn'];
					} elseif(!empty($from['Prefix'])) {
						$f.=$from['Prefix']['value'];
					} elseif(!empty($from['Constant'])) {
						$f.=$from['Constant']['value'];
					}
					$f.=" ".strtolower($from['To']['name']);
					$out['correspondents'][]=$f;
				}
			}
			if(!empty($data['ToCUnit'])) {
				$tos=$data['ToEUnit'];
				foreach($tos as $to) {
					$t="1 ".strtolower($unit['name'])." <-> ";
					if(!is_null($to['factor'])) {
						$t.=$to['factor'];
					} elseif(!is_null($to['factoreqn'])) {
						$t.=$to['factoreqn'];
					} elseif(!empty($from['Prefix'])) {
						$t.=$to['Prefix']['inverse'];
					} elseif(!empty($from['Constant'])) {
						$t.=$to['Constant']['inverse'];
					}
					$t.=" ".strtolower($to['From']['name']);
					$out['correspondents'][]=$t;
				}
			}
			if(empty($out['correspondents'])) { unset($out['correspondents']); }
			
			header('Content-Type: application/json');
            echo "[".json_encode($out)."]";exit;
        }
	}

    /**
     * Present an interface where a user can obtain a crosswalk between two unit representations
     * @param mixed $sysid1
     * @param mixed $sysid2
     */
	public function crosswalk($sysid1=null, $sysid2=null) {
		if($this->request->data||(!is_null($sysid1)&&!is_null($sysid2))) {
			if($this->request->data) {
				$sysid1=$this->request->data['Unit']['sys1'];
				$sysid2=$this->request->data['Unit']['sys2'];
			}
			$units1=$this->Repsystem->units($sysid1);
			$units2=$this->Repsystem->units($sysid2);
			$sysname1=$this->Repsystem->getfield($sysid1,'name');
			$sysname2=$this->Repsystem->getfield($sysid2,'name');
			$data=[];
			$path="https://umis.stuchalk.domains.unf.edu/repsystems/view/";
			$data['systems'][]=['id'=>$sysid1,'name'=>$sysname1,'link'=>$path.$sysid1];
			$data['systems'][]=['id'=>$sysid2,'name'=>$sysname2,'link'=>$path.$sysid2];
			$data['units']=[];
			// match units in sys1 to those in sys2
            $path="https://umis.stuchalk.domains.unf.edu/units/view/";
            foreach($units1 as $unit=>$rep) {
				$u=[];
                $u['id']=$unit;
                $u['unit']=$path.$unit;
				$u[$sysid1]=$rep;
				if(isset($units2[$unit])) {
					// found equivalent unit
					$u[$sysid2]=$units2[$unit];
				} else {
					// no equivalent unit
					$u[$sysid2]="no equivalent";
				}
				$data['units'][]=$u;
				unset($units2[$unit]);
			}
			// any remaining units have no equivalent
			foreach($units2 as $unit=>$rep) {
				$u=[];
                $u['id']=$unit;
                $u['unit']=$path.$unit;
				$u[$sysid1]="no equivalent";
				$u[$sysid2]=$rep;
				$data['units'][]=$u;
			}
			header('Content-Type: application/json');
			echo json_encode($data);exit;
		} else {
			$data=$this->Repsystem->find('list',['fields'=>['id','title'],'order'=>['name']]);
			$this->set('data',$data);
		}
    }

	/**
	 * Get list of units for one quantity kind, returned as JSON
	 * @param $qid
	 */
	public function unlist($qid)
	{
		$uns=$this->Unit->find('list',['conditions'=>['quantitykind_id'=>$qid],'recursive'=>-1]);
		header('Content-Type: application/json');
		echo "[".json_encode($uns)."]";exit;
	}

    /**
     * Find a unit
     * @param $unit
     */
	public function search($unit=null)
    {
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            if(isset($data['Unit']['unit'])) { $unit=$data['Unit']['unit']; }
        }
        if(is_null($unit)) { $this->redirect('/units'); }
        $f=['Unit.id','Unit.name','Quantitykind.name'];
        $c=['Quantitykind'];$o=['Quantitykind.name','Unit.name'];
        $con=['Unit.name like'=>'%'.$unit.'%'];
        $data=$this->Unit->find('list',['conditions'=>$con,'fields'=>$f,'contain'=>$c,'order'=>$o]);
        if(empty($data)) {
            $c=['Representation'=>['Unit'=>['Quantitykind']]];$con=['string'=>$unit];$f=['Strng.id','Strng.string'];$o=['Strng.string'];
            $strs=$this->Strng->find('all',['conditions'=>$con,'fields'=>$f,'contain'=>$c,'order'=>$o]);
            $data=[];
            foreach($strs[0]['Representation'] as $rep) {
                $qkname = $rep['Unit']['Quantitykind']['name'];
                $uid = $rep['Unit']['id'];$uname = $rep['Unit']['name'];
                if(isset($data[$qkname])) {
                    $keys = array_keys($data[$qkname]);
                    if(!in_array($uid,$keys)) {
                        $data[$qkname][$uid] = $uname;
                    }
                } else {
                    $data[$qkname][$uid] = $uname;
                }
            }
        }
        $count=0;$uids=[];
        foreach($data as $qk=>$units) {
            $count += count($units);
            foreach($units as $uid=>$unit) {
                $uids[] = $uid;
            }
        }
        if($count==1) { $this->redirect('/units/view/'.$uids[0]); }
        $this->set('data',$data);
        $this->render('index');
    }

    /**
     * Encode a unit in the IVOA VOUnits format
     * @param $uid int
     */
    public function ivoa($uid)
    {
        // base format of a unit in VOUnits format is 1.663e-1mm.s**-1
        // regex ((?:(?:[0-9]\.?[0-9]*)?(?:e[\+-][0-9]+)?(?:Y|Z|E|P|T|G|M|k|h|da|d|c|m|u|n|p|f|a|z|y)?(?:mol|Ohm|rad|cd|Hz|lm|lx|Pa|sr|Wb|A|C|F|g|H|J|K|N|m|S|s|T|V|W)(?:\*?\*[\+-][0-9]+)?\.?)+)

        $ubase=['m'=>'metre','s'=>'second','A'=>'ampere','K'=>'kelvin','mol'=>'mole','cd'=>'candela'
            ,'g'=>'gram','rad'=>'radian','sr'=>'steradian','Hz'=>'hertz','N'=>'newton','Pa'=>'pascal'
            ,'J'=>'joule','W'=>'watt','C'=>'coulomb','V'=>'volt','S'=>'siemens','F'=>'farad'
            ,'Wb'=>'weber','T'=>'tesla','H'=>'henry','lm'=>'lumen','lx'=>'lux','Ohm'=>'ohm'];
        $uabbs=array_keys($ubase);
        $pnames=[
            'Y'=>'yotta','Z'=>'zetta','E'=>'exa','P'=>'peta','T'=>'tera','G'=>'giga','M'=>'mega',
            'k'=>'kilo','h'=>'hecto','da'=>'deca','d'=>'deci','c'=>'centi','m'=>'milli','u'=>'micro',
            'n'=>'nano','p'=>'pico','f'=>'femto','a'=>'atto','z'=>'zepto','y'=>'yocto'];
        $pfixs=array_keys($pnames);
        $pmags=[
            'Y'=>'24','Z'=>'21','E'=>'18','P'=>'15','T'=>'12','G'=>'9','M'=>'6','k'=>'3','h'=>'2','da'=>'1',
            'd'=>'-1','c'=>'-2','m'=>'-3','u'=>'-6','n'=>'-9','p'=>'-12','f'=>'-15','a'=>'-18','z'=>'-21','y'=>'-24'];
        $bnames=[
            'Ki'=>'kibi','Mi'=>'mebi','Gi'=>'gibi','Ti'=>'tebi','Pi'=>'pebi','Ei'=>'exbi','Zi'=>'zebi','Yi'=>'yobi'
        ];
        $babbs=array_keys($bnames);
        $bmags=['Ki'=>'10','Mi'=>'20','Gi'=>'30','Ti'=>'40','Pi'=>'50','Ei'=>'60','Zi'=>'70','Yi'=>'80'];

        $inverse=['reciprocal','inverse','per'];
        $powers=['0.5'=>'to the half power','1'=>'','1.5'=>'to the three halves power','2'=>'squared','2.5'=>'to the five halves power','3'=>'cubed','4'=>'to the fourth power','5'=>'to the fifth power','6'=>'to the sixth power','7'=>'to the seventh power'];

        $equivunits=[
            'Hz'=>'s-1','N'=>'kg m s-2','J'=>'kg m2 s-2'
        ];

        $ivoabases=[
            'rad'=>['n'=>['m'=>1],'d'=>['m'=>1]],
            'sr'=>['n'=>['m'=>2],'d'=>['m'=>2]],
            'N'=>['n'=>['m'=>1,'kg'=>2],'d'=>['s'=>1]],
            'Pa'=>['n'=>['kg'=>1],'d'=>['m'=>1,'s'=>1]],
            'J'=>['n'=>['m'=>2,'kg'=>1],'d'=>['s'=>2]],
            'W'=>['n'=>['m'=>2,'kg'=>1],'d'=>['s'=>3]],
            'C'=>['n'=>['s'=>1,'A'=>1],'d'=>[]],
            'V'=>['n'=>['m'=>2,'kg'=>1],'d'=>['s'=>3,'A'=>1]],
            'S'=>['n'=>['s'=>3,'A'=>2],'d'=>['m'=>2,'kg'=>1]],
            'F'=>['n'=>['s'=>4,'A'=>2],'d'=>['m'=>2,'kg'=>1]],
            'Wb'=>['n'=>['m'=>2,'kg'=>1],'d'=>['s'=>2,'A'=>1]],
            'T'=>['n'=>['kg'=>1],'d'=>['s'=>2,'A'=>1]],
            'H'=>['n'=>['m'=>2,'kg'=>1],'d'=>['s'=>2,'A'=>2]],
            'lm'=>['n'=>['cd'=>1,'m'=>2],'d'=>['m'=>2]],
            'lx'=>['n'=>['cd'=>1],'d'=>['m'=>2]],
            'Ohm'=>['n'=>['m'=>2,'kg'=>1],'d'=>['s'=>3,'A'=>2]],
            'Hz'=>['n'=>[],'d'=>['s'=>1]]
        ];

        $data=$this->Unit->find('first',['conditions'=>['Unit.id'=>$uid],'recursive'=>2]);
        $unit=$data['Unit'];
        $uwords=explode(" ",strtolower($unit['name']));

        $captured=[];$numerator=1;$loc=$lastunit="";
        foreach($uwords as $idx=>$uword) {
            foreach ($pnames as $pname) {
                if (preg_match('/^'.$pname.'/', $uword)) {
                    if ($numerator) {
                        $captured['n'][$idx]['prefix'] = $pname;
                        $uword = str_replace($pname, "", $uword);
                    } else {
                        $captured['d'][$idx]['prefix'] = $pname;
                        $uword = str_replace($pname, "", $uword);
                    }
                }
            }
            if($uword=="meter") { $uword="metre"; }
            if (in_array($uword, $ubase)) {
                if ($numerator) {
                    $captured['n'][$idx]['type'] = 'ivoa';
                    $captured['n'][$idx]['unit'] = $uword;
                    $loc = 'n';
                } else {
                    $captured['d'][$idx]['type'] = 'ivoa';
                    $captured['d'][$idx]['unit'] = $uword;
                    $loc = 'd';
                }
                $lastunit = $idx;
                unset($uwords[$idx]);
            }
            if (in_array($uword, $powers)) {
                $power = array_search($uword, $powers);
                $captured[$loc][$lastunit]['power'] = $power;
                unset($uwords[$idx]);
            }
            if (in_array($uword, $inverse)) {
                $numerator = 0;
                unset($uwords[$idx]);
            }
        }

        //debug($uwords);debug($captured);exit;
        // encode as ivoa
        $unitstr="";
        if(isset($captured['n'])) {
            foreach ($captured['n'] as $nunit) {
                $prefix="";$power="";
                if ($nunit['type'] == 'ivoa') {
                    $symbol = array_search($nunit['unit'], $ubase);
                    if(isset($nunit['prefix'])) { $prefix = array_search($nunit['prefix'],$pnames); }
                    if(isset($nunit['power'])) { $power = $nunit['power']; }
                    $unitstr .= $prefix.$symbol.$power;
                }
            }
        }
        if(isset($captured['d'])) {
            $unitstr.="/";
            foreach ($captured['d'] as $dunit) {
                $prefix="";$power="";
                if ($dunit['type'] == 'ivoa') {
                    $symbol = array_search($dunit['unit'], $ubase);
                    if(isset($dunit['prefix'])) { $prefix = array_search($dunit['prefix'],$pnames); }
                    if(isset($dunit['power'])) { $power = $nunit['power']; }
                    $unitstr .= $prefix.$symbol.$power;
                }
            }
        }

        // get ivoa string
        $command="/usr/local/bin/unity -ovounits -v '".$unitstr."'";$update="";
        $output=explode("\n",shell_exec($command));
        if(stristr($output[1],'yes')&&stristr($output[2],'yes')&&stristr($output[3],'yes')) {
            $this->Unit->id=$uid;
            $update=$this->Unit->saveField('ivoa',$output[0]);
        }

        debug($output);debug($update);exit;
    }

}

?>