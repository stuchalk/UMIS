<?php

/**
 * Class DimensionvectorsController
 */
class DimensionvectorsController extends AppController
{
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view');
	}

    /**
     * List all dimension vectors
     */
    public function index()
    {
        $data=$this->Dimensionvector->find('list',['fields'=>['id','name'],'order'=>['name'],'recursive'=>-1]);
        $this->set('data',$data);
    }

    /**
     * View a unit dimension vector
     * @param $id
     */
    public function view($id)
    {
        $c=['Quantitykind','Quantitysystem'];
        if(preg_match("/[LMTIHNJD'\d!@]*_[LMTIHNJD'\d!@]*_D[01]/",$id)) {
            $data=$this->Dimensionvector->find('first',['conditions'=>['OR'=>['Dimensionvector.shortcode'=>$id,'Dimensionvector.longcode'=>$id]],'contain'=>$c,'recursive'=>-1]);
            echo "Matches encoding!";
            debug($data);exit;
        } else {
            $data=$this->Dimensionvector->find('first',['conditions'=>['Dimensionvector.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        }
        $this->set('data',$data);
    }

    /**
	 * Add a dimensionvector
	 */
    public function add($str)
	{
		$found=$this->Dimensionvector->find('first',['conditions'=>['shortcode'=>$str],'recursive'=>-1]);
		if($found) {
			$this->redirect('dimensionvectors/view/'.$found['Dimensionvector']['id']);
		} else {
			// dv does not exist so create
			
			// check that str is a valid shortcode
			$basereg="((?:(?:L|M|T|I|H|N|J|D)'\d+!?\d*')+)";
			preg_match("/^".$basereg."_".$basereg."_(D[01])$/",$str,$m);
			if(!$m){
				echo "Invalid dimensionvector shortcode!";exit;
			}
			$n=$m[1];$d=$m[2];$u=$m[3];
			if($u=='D1'&&$n!=$m) {
				echo "Not a unitless number! Check string.";exit;
			}
			// process parts
			$dv=['shortcode'=>$str];
			$dims=['length'=>'L','mass'=>'M','time'=>'T','current'=>'I','temperature'=>'H','amount-of-substance'=>'N','candela'=>'J','dimensionless'=>'D'];
			$numstr='';$denstr='';$powers=['n'=>[],'d'=>[]];
			foreach($dims as $dim) {
				$patt="/".$dim."'(\d+!?\d*)'/";
				if(stristr($n,$dim)) {
					preg_match($patt,$n,$m);
					$numstr.=$dim."'".$m[1]."'";
					$powers['n'][$dim]=$m[1];
				} else {
					$numstr.=$dim."'0'";
					$powers['n'][$dim]=0;
				}
				if(stristr($d,$dim)) {
					preg_match($patt,$d,$m);
					$denstr.=$dim."'".$m[1]."'";
					$powers['d'][$dim]=$m[1];
				} else {
					$denstr.=$dim."'0'";
					$powers['d'][$dim]=0;
				}
			}
			$dv['longcode']=$numstr."_".$denstr."_".$u;
			
			// create dim powers
			foreach($powers['n'] as $dim=>$npower) {
                $dpower=$powers['d'][$dim];
                if(stristr($npower,'!')) {
                    list($npownint,$npowdint)=explode('!',$npower);
                    $npower=((int)$npownint/(int)$npowdint);
                    $powers['n'][$dim]=$npower;
                }
                if(stristr($dpower,'!')) {
                    list($dpownint,$dpowdint)=explode('!',$dpower);
                    $dpower=((int)$dpownint/(int)$dpowdint);
                    $powers['d'][$dim]=$dpower;
                }

                $dv['dim_exp_'.$dim]=$npower-$dpower;
			}

            // add the dimensionless value
            if($u=='D0') {
                $dv['dim_exp_D']=0;
            } elseif($u=='D1') {
                $dv['dim_exp_D']=1;
            } else {
                $dv['dim_exp_D']=0;
            }

            // create name
            $name="";$quantities=array_flip($dims);
            $powstrs=['0.5'=>'to the half power','1'=>'','1.5'=>'to the three halves power','2'=>'squared','2.5'=>'to the five halves power','3'=>'cubed','4'=>'to the fourth power','5'=>'to the fifth power','6'=>'to the sixth power','7'=>'to the seventh power'];
			foreach($powers['n'] as $dim=>$npower) {
                if($npower!=0) {
                    $name.=$quantities[$dim]." ".$powstrs[(string)$npower]." ";
                }
            }
            foreach($powers['d'] as $dim=>$dpower) {
                if($dpower!=0) {
                    $name.="per ".$quantities[$dim]." ".$powstrs[(string)$dpower]." ";
                }
            }
            $dv['name']=ucfirst(str_replace("  "," ",trim($name)));

            // create description
            $dv['description']="Dimension vector of ".str_replace("  "," ",trim($name));

            // create symbol
            $symbol=""; // HTML
            foreach($dv as $field=>$value) {
                if(stristr($field,'dim_exp_')&&$value!=0) {
                    $dim=str_replace("dim_exp_","",$field);
                    $symbol.=$dim."<sup>".$value."</sup> ";
                }
            }
            $dv['symbol']=trim($symbol);

            // create basesi_shortcode, basesi_longcode
            $basesis=$basesil=$numstrs=$denstrs=$numstrl=$denstrl="";
            $baseunits=['L'=>'m','M'=>'kg','T'=>'s','I'=>'A','H'=>'K','N'=>'mol','J'=>'cd'];
            foreach($dv as $field=>$value) {
                if(stristr($field, 'dim_exp_')) {
                    $dim=str_replace("dim_exp_","",$field);
                    if($dim!='D') {
                        if($value>0) {
                            if($value==0.5) { $value="1!2"; }
                            elseif($value==1.5) { $value="3!2"; }
                            elseif($value==2.5) { $value="5!2"; }
                            $numstrs.=$baseunits[$dim]."'".$value."'";
                            $numstrl.=$baseunits[$dim]."'".$value."'";
                            $denstrl.=$baseunits[$dim]."'0'";
                        } elseif($value<0) {
                            if(abs($value)==0.5) { $value="1!2"; }
                            elseif(abs($value)==1.5) { $value="3!2"; }
                            elseif(abs($value)==2.5) { $value="5!2"; }
                            $denstrs.=$baseunits[$dim]."'".abs($value)."'";
                            $denstrl.=$baseunits[$dim]."'".abs($value)."'";
                            $numstrl.=$baseunits[$dim]."'0'";
                        } elseif($value==0) {
                            $numstrl.=$baseunits[$dim]."'0'";
                            $denstrl.=$baseunits[$dim]."'0'";
                        }
                    }
                }
            }
            $dv['basesi_shortcode']=$numstrs."_".$denstrs."_U0";
            $dv['basesi_longcode']=$numstrl."U'0'_".$denstrl."U'0'_U0";

            // add the quantitysystem
            $dv['quantitysystem_id']=1;

            // save in the database
            $this->Dimensionvector->create();
            $added=$this->Dimensionvector->save(['Dimensionvector'=>$dv]);
            debug($added);exit;
		}
	}
}

?>