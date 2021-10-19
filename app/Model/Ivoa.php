<?php

/**
 * Class Ivoa
 * Ivoa model
 */
class Ivoa extends AppModel
{

    /**
     * Encode a unit in the IVOA VOUnits format
     * @param $uid int
     */
    public function makesymbol($uid)
    {
        // base format of a unit in VOUnits format is 1.663e-1mm.s**-1
        // regex ((?:(?:[0-9]\.?[0-9]*)?(?:e[\+-][0-9]+)?(?:Y|Z|E|P|T|G|M|k|h|da|d|c|m|u|n|p|f|a|z|y)?(?:mol|Ohm|rad|cd|Hz|lm|lx|Pa|sr|Wb|A|C|F|g|H|J|K|N|m|S|s|T|V|W)(?:\*?\*[\+-][0-9]+)?\.?)+)
        $Unit=ClassRegistry::init('Unit');
        $Str=ClassRegistry::init('Strng');
        if(!is_numeric($uid)) {
            // find the uid from this unit
            $found=$Str->find('first',['conditions'=>['Strng.string'=>$uid],'contain'=>['Representation'],'recursive'=>-1]);
            if(!$found) {
                $found=$Unit->find('first',['conditions'=>['Unit.name'=>$uid],'recursive'=>-1]);
                if(!$found) {
                    echo "Unknown unit";exit;
                } else {
                    if(!is_null($found['Unit']['ivoa'])) {
                        return $found['Unit']['ivoa'];
                    }
                    $uid=$found['Unit']['id'];
                }
            }
        }

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

        $equivunits=['Hz'=>'s-1','N'=>'kg m s-2','J'=>'kg m2 s-2'];

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

        $data=$Unit->find('first',['conditions'=>['Unit.id'=>$uid],'recursive'=>2]);
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
        $ivoa=$this->getstr($unitstr);
        if(!$ivoa) {
            $Unit->id=$uid;
            $update=$Unit->saveField('ivoa',$ivoa);
        }
        return $ivoa;
    }

    /**
     * get the ivoa string using the command line application 'unity'
     * @param $str
     * @return false|mixed|string
     */
    public function getstr($str)
    {
        // get ivoa string
        $command="/usr/local/bin/unity -ovounits -v '".$str."'";$ivoa=false;
        $output=explode("\n",shell_exec($command));
        if(stristr($output[1],'yes')&&stristr($output[2],'yes')&&stristr($output[3],'yes')) {
            $ivoa=$output[0];
        }
        return $ivoa;
    }
}