<?php

/**
 * Class Repsystem
 * Repsystem model
 */
class Repsystem extends AppModel
{

	public $hasMany=['Representation'];
	
	public $belongsTo=['Domain'];
	
    public $virtualFields = ['title'=>"CONCAT(Repsystem.name,' (',Repsystem.abbrev,')')"];
	
	/**
	 * Get the unit representations for a repsystem
	 * @param $id
	 * @return array
	 */
    public function units($id)
	{
		$c=['Representation'=>['Strng','Unit']];
		$units=$this->find('first',['conditions'=>['Repsystem.id'=>$id],'contain'=>$c,'recursive'=>-1]);
		$data=[];$reps=$units['Representation'];
		foreach($reps as $rep) {
            if(!empty($rep['Unit'])) {
                $data[$rep['Unit']['id']]=$rep['Unit']['name'].":".$rep['Strng']['string'];
            }
        }
		return $data;
	}
	
	/**
	 * Get the value of a field or a specfic repsystem
	 * @param $id
	 * @param $field
	 * @return mixed
	 */
	public function getfield($id,$field)
	{
		$this->id = $id;
		return $this->field($field);
	}

    /**
     * validate vounit string and expand to dimensionvector, basesi_short, basesi_long
     * @param $str
     * @return mixed
     */
	public function vounits($str) {
        $cmd="/usr/local/bin/unity -ivounits -v '".$str."'";
        $output=shell_exec($cmd);
        if(substr_count($output,'yes')==3) {
            // valid format unit so now convert to other formats
            // split out units and powers
			$lines=explode("\n",$output);
			$uline=substr($lines[0],2);
			$ivoabases=[
				'rad'=>['m'=>['n'=>1,'d'=>1],'u'=>1],
				'sr'=>['m'=>['n'=>2,'d'=>2],'u'=>1],
				'N'=>['m'=>1,'g'=>1,'s'=>-2],
				'Pa'=>['g'=>1,'m'=>-1,'s'=>-2],
				'J'=>['m'=>2,'g'=>1,'s'=>-2],
				'W'=>['m'=>2,'g'=>1,'s'=>-3],
				'C'=>['s'=>1,'A'=>1],
				'V'=>['m'=>2,'g'=>1,'s'=>-3,'A'=>-1],
				'S'=>['s'=>3,'A'=>2,'m'=>-2,'g'=>-1],
				'F'=>['s'=>4,'A'=>2,'m'=>-2,'g'=>-1],
				'Wb'=>['m'=>2,'g'=>1,'s'=>-2,'A'=>-1],
				'T'=>['g'=>1,'s'=>-2,'A'=>-1],
				'H'=>['m'=>2,'g'=>1,'s'=>-2,'A'=>-2],
				'lm'=>['cd'=>1,'sr'=>1],
				'lx'=>['m'=>-2,'cd'=>1],
				'Ohm'=>['m'=>2,'g'=>1,'s'=>-3,'A'=>-2],
				'Hz'=>['s'=>-1]
			];
			$unitarray=[
				'n'=>['m'=>0,'g'=>0,'s'=>0,'A'=>0,'K'=>0,'mol'=>0,'cd'=>0,'u'=>0],
				'd'=>['m'=>0,'g'=>0,'s'=>0,'A'=>0,'K'=>0,'mol'=>0,'cd'=>0,'u'=>0],
				'u'=>0
			];
			$units=explode(" ",$uline);
			$dunits=['n'=>[],'d'=>[]];$uniques=[];
			$uparts=[];$ndim=null;$ddim=null;$nuunit=null;$duunit=null;
			foreach($units as $part) {
            	list($mag,$unit,$power)=explode('/',$part);
            	$uparts[]=['unit'=>$unit,'mag'=>$mag,'pow'=>$power];
                if(in_array($unit,array_keys($unitarray['n']))) {
					if($power>0) {
						$unitarray['n'][$unit]+=$power;
					} else {
						$unitarray['d'][$unit]+=abs($power);
					}
				} elseif(in_array($unit,array_keys($ivoabases))) {
                    $sunits=$ivoabases[$unit];
                    if(!isset($sunits['u'])) {
						foreach($sunits as $sunit=>$power) {
							if($power>0) {
							    if($sunit=='rad') {
                                    $unitarray['n']['m']+=($power*1);
                                    $unitarray['n']['u']+=($power*1);
                                    $unitarray['d']['m']+=($power*1);
                                    $ndim="D'1'@L'1'@";
                                    $nuunit="U'1'@rad@";
                                } elseif($sunit=='sr') {
                                    $unitarray['n']['m']+=($power*2);
                                    $unitarray['n']['u']+=($power*2);
                                    $unitarray['d']['m']+=($power*2);
                                    $ndim="D'1'@L'2'@";
                                    $nuunit="U'1'@sr@";
                                } else {
                                    $unitarray['n'][$sunit]+=$power;
                                }
							} else {
                                if($sunit=='rad') {
                                    $unitarray['n']['m']+=($power*1);
                                    $unitarray['d']['m']+=($power*1);
                                    $unitarray['d']['u']+=($power*1);
                                    $ddim="D'1'@L'1'@";
                                    $duunit="U'1'@rad@";
                                } elseif($sunit=='sr') {
                                    $unitarray['n']['m']+=($power*2);
                                    $unitarray['d']['m']+=($power*2);
                                    $unitarray['d']['u']+=($power*2);
                                    $ddim="D'1'@L'2'@";
                                    $duunit="U'1'@sr@";
                                } else {
                                    $unitarray['d'][$sunit]+=abs($power);
                                }
							}
						}
					} else {
                        unset($sunits['u']);
                        foreach ($sunits as $sunit => $powers) {
                            //echo "POWERS:";debug($powers);
                            $unitarray['n'][$sunit] += $powers['n'];
                            $unitarray['d'][$sunit] += $powers['d'];
                        }
                        $unitarray['u'] = 1;
                    }
                } else {
					echo 'Bad unit:'.$unit.'<br/>';
					echo 'in '.$part;exit;
				}
                // identify dimensionless units
                if($unit=='sr'||$unit=='rad') {
				    if($power>1) {
                        if($unit=='sr') {
                            $unitarray['n']['m']-=2;
                            $unitarray['d']['m']-=2;
                            $ndim="D'1'@L'2'@";
                            $nuunit="U'1'@sr@";
                        } elseif($unit=='rad') {
                            $unitarray['n']['m']-=1;
                            $unitarray['d']['m']-=1;
                            $ndim="D'1'@L'1'@";
                            $nuunit="U'1'@rad@";
                        }
                    } elseif($power<0) {
                        if($unit=='sr') {
                            $unitarray['n']['m']-=2;
                            $unitarray['d']['m']-=2;
                            $ddim="D1@L'2'@";
                            $duunit="U1@sr@";
                        } elseif($unit=='rad') {
                            $unitarray['n']['m']-=1;
                            $unitarray['d']['m']-=1;
                            $ddim="D'1'@L'1'@";
                            $duunit="U'1'@rad@";
                        }
                    }
                }
                if(!in_array($unit,array_keys($uniques))) {
                    $uniques[$unit]=$power;
                } else {
                    if($unitarray['u']==1&&abs($uniques[$unit])==abs($power)) {
                        if($uniques[$unit]>1) {
                            $ndim="D'1'@".$unit."'".abs($power)."'@";
                            $nuunit="U'1'@".$unit."@";
                        } else {
                            $ddim="D'1'@".$unit."'".abs($power)."'@";
                            $duunit="U'1'@".$unit."@";
                        }
                     }
                }
			}

			// create codes
            $pmags=[
                'Y'=>'24','Z'=>'21','E'=>'18','P'=>'15','T'=>'12','G'=>'9','M'=>'6','k'=>'3','h'=>'2','da'=>'1',
                'd'=>'-1','c'=>'-2','m'=>'-3','u'=>'-6','n'=>'-9','p'=>'-12','f'=>'-15','a'=>'-18','z'=>'-21','y'=>'-24'];
            $udims=['m'=>'L','g'=>'M','s'=>'T','A'=>'I','K'=>'H','mol'=>'N','cd'=>'J'];
            $bunits=array_keys($udims);$dless=$unitarray['u'];
			$return=['shortcode'=>'','longcode'=>'','basesi_shortcode'=>'','basesi_longcode'=>'','unit_shortcode'=>''];
			if(!$dless) {
                // if unitarray has 'u' set to 0 then consolidate num/denom units
                //echo  'initial unitarray: ';debug($unitarray);
                foreach($bunits as $bunit) {
                    if($unitarray['n'][$bunit]!=0&&$unitarray['d'][$bunit]!=0) {
                        if($unitarray['n'][$bunit]>$unitarray['d'][$bunit]) {
                            $unitarray['n'][$bunit]-=$unitarray['d'][$bunit];
                            $unitarray['d'][$bunit]=0;
                        } elseif($unitarray['n'][$bunit]<$unitarray['d'][$bunit]) {
                            $unitarray['d'][$bunit]-=$unitarray['n'][$bunit];
                            $unitarray['n'][$bunit]=0;
                        } elseif($unitarray['n'][$bunit]==$unitarray['d'][$bunit]) {
                            $unitarray['n'][$bunit]=0;
                            $unitarray['d'][$bunit]=0;
                        }
                    }
                }
            }

			// create dimensionvector shortcode and longcode
            foreach($unitarray['n'] as $nunit=>$npow) {
                $code=$udims[$nunit]."'".$npow."'";
                if($npow>0) { $return['shortcode'].=$code; }
			    $return['longcode'].=$code;
                unset($code);
            }
            if(!is_null($ndim)) {
                $return['shortcode'].='\''.$ndim.'\'';$return['longcode'].='\''.$ndim.'\'';
            } else {
                $return['shortcode'].='';$return['longcode'].='D\'0\'';
            }
            $return['shortcode'].='_';$return['longcode'].='_';
            foreach($unitarray['d'] as $dunit=>$dpow) {
                $code=$udims[$dunit]."'".$dpow."'";
                if($dpow>0) { $return['shortcode'].=$code; }
                $return['longcode'].=$code;
                unset($code);
            }
            if(!is_null($ddim)) {
                $return['shortcode'].='\''.$ddim.'\'';$return['longcode'].='\''.$ddim.'\'';
            } else {
                $return['shortcode'].='';$return['longcode'].='D\'0\'';
            }
            $return['shortcode'].='_D'.$dless;$return['longcode'].='_D'.$dless;
            // create basesi shortcode and longcode
            foreach($unitarray['n'] as $nunit=>$npow) {
                if($nunit=='g') {
                    $code='k'.$nunit."'".$npow."'";
                } else {
                    $code=$nunit."'".$npow."'";
                }
                if($npow>0) { $return['basesi_shortcode'].=$code; }
                $return['basesi_longcode'].=$code;
            }
            if(!is_null($nuunit)) {
                $return['basesi_shortcode'].=$nuunit;$return['basesi_longcode'].=$nuunit;
            } else {
                $return['basesi_shortcode'].='';$return['basesi_longcode'].="U'0'";
            }
            $return['basesi_shortcode'].='_';$return['basesi_longcode'].='_';
            //debug($return['basesi_shortcode']);
            foreach($unitarray['d'] as $dunit=>$dpow) {
                if($dunit=='g') {
                    $code='k'.$dunit."'".$dpow."'";
                } else {
                    $code=$dunit."'".$dpow."'";
                }
                if($dpow>0) { $return['basesi_shortcode'].=$code; }
                $return['basesi_longcode'].=$code;
            }
            //debug($dunit);

            if(!is_null($duunit)) {
                $return['basesi_shortcode'].=$duunit;$return['basesi_longcode'].=$duunit;
            } else {
                $return['basesi_shortcode'].='';$return['basesi_longcode'].="U'0'";
            }
            $return['basesi_shortcode'].='_U'.$dless;$return['basesi_longcode'].='_U'.$dless;
            //debug($return['basesi_shortcode']);
            // create unit shortcode and longcode
            foreach($uparts as $upart) {
                if($upart['pow']>0) {
                    $prefix=array_search($upart['mag'],$pmags);
                    $code=$prefix.$upart['unit']."'".$upart['pow']."'";
                    $return['unit_shortcode'].=$code;
                }
                unset($code);
            }
            if(!is_null($nuunit)) { $return['unit_shortcode'].=$nuunit; }
            $return['unit_shortcode'].='_';
            foreach($uparts as $upart) {
                if($upart['pow']<0) {
                    $prefix=array_search(abs($upart['mag']),$pmags);
                    $code=$prefix.$upart['unit']."'".abs($upart['pow'])."'";
                    $return['unit_shortcode'].=$code;
                }
                unset($code);
            }
            if(!is_null($dunit)) { $return['unit_shortcode'].=$duunit; }
            $return['unit_shortcode'].='_U'.$dless;
            // consolidate dimension powers for ingestion
            foreach ($bunits as $bunit) {
                $return['dim_exp_'.$udims[$bunit]]=$unitarray['n'][$bunit]-$unitarray['d'][$bunit];
            }
            $return['dim_exp_D']=$dless;
            return $return;
        } else {
            return $output;
        }
    }

    /**
     * get abbrevs
     */
    public function getabbrevs()
    {
        return $this->find('list',['fields'=>['abbrev'],'recursive'=>-1]);
    }

}