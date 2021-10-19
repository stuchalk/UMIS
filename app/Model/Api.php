<?php

/**
 * Class Api
 * Api model
 */
class Api extends AppModel
{

    public $useTable=false;

    /**
     * get dimensionvector from string
     * @param mixed $unit
     * @param string $type
     * @return string
     **/
    public function getdv($unit=null, $type='short') {
        $Strng=ClassRegistry::init('Strng');
        $c=['Representation'=>['Unit'=>['Quantitykind'=>['Dimensionvector']]]];
        $str = $Strng->find('first',['conditions'=>['string'=>$unit],'contain'=>$c,'recursive'=>-1]);
        $reps = $str['Representation'];$tmp = [];
        foreach($reps as $rep) { $tmp[]=$rep['unit_id']; }
        $tmp=array_unique($tmp);
        if(count($tmp)>1) {
            return "error: ambiguous unit";
        } else {
            $dv=$reps[0]['Unit']['Quantitykind']['Dimensionvector'];
            if($type=='short') {
                $code=$dv['shortcode'];
            } else {
                $code=$dv['longcode'];
            }
        }
        return $code;
    }

    /**
     * get baseunit from string
     * @param mixed $unit
     * @return string
     **/
    public function getbaseunit($unit=null) {
        $Strng=ClassRegistry::init('Strng');
        $c=['Representation'=>['Unit'=>['Quantitykind'=>['Dimensionvector','BaseUnit']]]];
        $str = $Strng->find('first',['conditions'=>['string'=>$unit],'contain'=>$c,'recursive'=>-1]);
        $reps = $str['Representation'];$tmp = [];
        foreach($reps as $rep) { $tmp[]=$rep['unit_id']; }
        $tmp=array_unique($tmp);
        if(count($tmp)>1) {
            return "error: ambiguous unit";
        } else {
            //debug($reps[0]['Unit']);
            return $reps[0]['Unit']['Quantitykind']['BaseUnit']['shortcode'];
        }
    }

    /**
     * get unit from string
     * @param mixed $unit
     * @return array
     **/
    public function getunitbystr($unit=null) {
        $Strng=ClassRegistry::init('Strng');
        $c=['Representation'=>['Unit'=>['Prefix','Factor','Quantitykind'=>['Dimensionvector','BaseUnit']]]];
        $str = $Strng->find('first',['conditions'=>['string'=>$unit],'contain'=>$c,'recursive'=>-1]);
        if(empty($str)) { return ['error'=>'unknown unit']; }
        $reps = $str['Representation'];$tmp = [];
        foreach($reps as $rep) { $tmp[]=$rep['unit_id']; }
        $tmp=array_unique($tmp);
        if(count($tmp)>1) {
            return ['error'=>'ambiguous unit unit'];
        } else {
            //debug($reps[0]['Unit']);
            return $reps[0]['Unit'];
        }
    }

    /**
     * get unit from id
     * @param mixed $id
     * @return string
     **/
    public function getunitbyid($id=null) {
        $Unit=ClassRegistry::init('Unit');
        $c=['Quantitykind'=>['Dimensionvector','BaseUnit'],'Unitsystem','Prefix','Representation'=>['Strng','Repsystem','Unitsystem']];
        return $Unit->find('first',['conditions'=>['Unit.id'=>$id],'contain'=>$c,'recursive'=>-1]);
    }

    /**
     * get units that are same dimensionality as the one indicated
     * @param mixed $id
     * @return mixed
     **/
    public function getunitsbydv($id=null) {
        $Unit=ClassRegistry::init('Unit');
        $c=['Quantitykind'=>['Dimensionvector']];
        $unit=$Unit->find('first',['conditions'=>['Unit.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $data=[];
        $data['unit']=$unit['Unit'];
        $data['dv']=$unit['Quantitykind']['Dimensionvector'];
        $dvid=$data['dv']['id'];
        $QKind=ClassRegistry::init('Quantitykind');
        $c=['Unit'];
        $qkinds=$QKind->find('all',['conditions'=>['Quantitykind.dimensionvector_id'=>$dvid],'contain'=>$c,'recursive'=>-1]);
        foreach($qkinds as $qkind) {
            foreach($qkind['Unit'] as $uidx=>$unit) {
                if($unit['id']==$id) {
                    # ignore the unit the search is based on
                    unset($qkind['Unit'][$uidx]);
                }
            }
            $data['units'][]=$qkind;
        }
        return $data;
    }

    /**
     * canonicalize a dimensvector
     * @param string $dv
     * @return string
     */
    public function candv($dv=null)
    {
        // check format of input dv and normalize
        // url decode
        $dv=urldecode($dv);
        // remove html
        $dv=strip_tags($dv);
        // remove caret(^)
        $dv=str_replace("^","",$dv);
        // normalize any spaces to singles
        $dv=preg_replace("/\s+/"," ",$dv);
        // change out dimension names for symbols
        $dims=['L'=>'length','M'=>'mass','T'=>'time','I'=>'current','H'=>'temperature','N'=>'amount-of-substance','J'=>'luminous instensity'];
        // TODO replace out squared, cubed, etc.
        foreach($dims as $s=>$n) {
            if(stristr($dv,$n)) {
                $dv=str_replace($n,$s,$dv);
            }
        }
        //debug($dv);
        // is there a 'per' or '/'
        $delims=[" ",";",":"];
        if(stristr($dv,'per')||stristr($dv,'/')) {
            // spilt string
            if(stristr($dv,'per')) {
                list($n,$d)=explode("per",$dv);
            } else {
                list($n,$d)=explode("/",$dv);
            }
            $n=trim($n);$d=trim($d);
            // convert denominator powers to negative
            foreach($delims as $delim) {
                if(stristr($d,$delim)) {
                    $parts=explode($delim,$d);$d="";
                    foreach($parts as $idx=>$part) {
                        if(preg_match('/^([A-Za-z]+)(\d+)$/',$part,$m)) {
                            if($idx!=0){ $d.=" "; }
                            $d.=$m[1]."-".$m[2];
                        }
                    }
                    break;
                }
            }
            // check to see if there was only one dimension in the denominator...
            if(preg_match('/^([A-Za-z]+)(\d+)$/',$d,$m)) {
                $d=$m[1]."-".$m[2];
            }
            $dv=$n." ".$d;
        }
        // bring together numerator and denominator dimension powers
        $parts=[];
        foreach($delims as $delim) {
            if(stristr($dv,$delim)) {
                $parts=explode($delim,$dv);
                break;
            }
        }
        if(empty($parts)) { $parts[0]=$dv; }
        $dims=[];
        foreach($parts as $idx=>$part) {
            if(preg_match('/^([A-Za-z]+)(-?\d+)?$/',$part,$m)) {
                if(empty($m[2])) { $m[2]=1; }
                $dims[$m[1]][]=$m[2];
            }
        }
        foreach($dims as $didx=>$dim){
            $dims[$didx]=array_sum($dim);
        }
        //debug($dims);exit;
        // write out short dimension string
        $n=$d=['L'=>0,'M'=>0,'T'=>0,'I'=>0,'H'=>0,'N'=>0,'J'=>0,'D'=>0];
        foreach($dims as $dim=>$power) {
            if($power>0) {
                $n[$dim]=$power;
            } else {
                $d[$dim]=abs($power);
            }
        }
        $lcode=$scode="";
        foreach($n as $dim=>$power) {
            if($power!=0) {
                $scode.=$dim."'".$power."'";
            }
            $lcode.=$dim."'".$power."'";
        }
        $scode.="_";
        $lcode.="_";
        foreach($d as $dim=>$power) {
            if($power!=0) {
                $scode.=$dim."'".$power."'";
            }
            $lcode.=$dim."'".$power."'";
        }
        $scode.="_D'0'";
        $lcode.="_D'0'";
        return $scode;
    }
}