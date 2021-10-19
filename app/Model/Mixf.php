<?php

/**
 * Class Mixf
 * Mixf model
 */
class Mixf extends AppModel
{

    // this class encapsulates the specficiation of MIXF-10 defined in
    // https://people.csail.mit.edu/jaffer/MIXF/MIXF-10 2011-10-09

    // expression syntax
    public $vsyntax="**value**.**unit**";

    // prefix syntax
    public $usyntax="[prefix]**baseunit**";

    // unit multiplier character
    public $mulitplier=".";

    // unit divider character
    public $divider="/";

    // unit exponent character
    public $exponent="^";

    // grouping
    public $group="(**unit1**)(**unit2**)";

    // units where prefixes are not allowed
    public $noprefix=['minute'=>'min','hour'=>'hr','day'=>'d','decibel'=>'dB','unified_atom_mass'=>'u'];

    // units where only submultiple prefixes are allowed
    public $subonlyprefix=['liter'=>'L','neper'=>'Np','degree','o'];

    // units where  submultiple prefixes are not allowed
    public $nosubprefix=['metric tonne'=>'t','revolution'=>'r','baud'=>'Bd'];

    // uppercase unit symbols
    public $upperunits=['liter'=>'L','hertz'=>'Hz','kelvin'=>'K','baud'=>'Bd','byte'=>'B','newton'=>'N','pascal'=>'P',
        'joule'=>'J','watt'=>'W','neper'=>'Np','ampere'=>'A','coulomb'=>'C','volt'=>'V','farad'=>'F','ohm'=>'Ohm',
        'siemens'=>'S','weber'=>'Wb','tesla'=>'T','henry'=>'H','becquerel'=>'Bq','gray'=>'Gy','sievert'=>'Sv'];

    // uppercase prefix symbols
    public $upperprefs=['mega'=>'M','giga'=>'G','tera'=>'T','peta'=>'P','exa'=>'E','zetta'=>'Z','yotta'=>'Y'];

    // SI prefixes
    public $siprefsymbols=['deci'=>'d','centi'=>'c','milli'=>'m','micro'=>'u','pico'=>'p','nano'=>'n',
        'femto'=>'f','atto'=>'a','zepto'=>'z','yocto'=>'y','deca'=>'d','hecto'=>'h','kilo'=>'k',
        'mega'=>'M','giga'=>'G','tera'=>'T','peta'=>'P','exa'=>'E','zetta'=>'Z','yotta'=>'Y'];
    public $siprefscales=['deci'=>'1e-1','centi'=>'1e-2','milli'=>'1e-3','micro'=>'1e-6','pico'=>'1e-9','nano'=>'1e-12',
        'femto'=>'1e-15','atto'=>'1e-18','zepto'=>'1e-21','yocto'=>'1e-24','deca'=>'1e1','hecto'=>'1e2','kilo'=>'1e3',
        'mega'=>'1e6','giga'=>'1e9','tera'=>'1e12','peta'=>'1e15','exa'=>'1e18','zetta'=>'1e21','yotta'=>'1e24'];

    // Binary prefixes
    public $binarysymbols=['kibi'=>'Ki','mibi'=>'Mi','gibi'=>'Gi','tebi'=>'Ti','pebi'=>'Pi','exbi'=>'Ei'];
    public $binaryscales=['kibi'=>'1024','mibi'=>'1048576','gibi'=>'1073741824','tebi'=>'1099511627776',
        'pebi'=>'1125899906842624','exbi'=>'1152921504606846976'];

    // Units
    public $unitsymbols=['second'=>'s','minute'=>'min','hour'=>'hr','day'=>'d','hertz'=>'Hz','baud'=>'Bd','meter'=>'m',
        'liter'=>'L','radian'=>'rad','steradian'=>'sr','revolution'=>'r','degree'=>'o','bit'=>'bit','byte'=>'B',
        'octet'=>'B','gram'=>'g','ton'=>'t','unified atomic mass unit'=>'u','mole'=>'mol','katal'=>'kat','kelvin'=>'K',
        'degree Celcius'=>'oC','candela'=>'cd','lumen'=>'lm','lux'=>'lx','newton'=>'N','pascal'=>'Pa','joule'=>'J',
        'electronvolt'=>'eV','watt'=>'W','neper'=>'Np','decibel'=>'dB','ampere'=>'A','coulomb'=>'C','volt'=>'V',
        'farad'=>'F','ohm'=>'Ohm','siemens'=>'S','weber'=>'Wb','tesla'=>'T','henry'=>'H','becquerel'=>'Bq','gray'=>'Gy',
        'sievert'=>'Sv'];
    public $unitequivs=['second'=>null,'minute'=>'60.s','hour'=>'60.min','day'=>'24.h','hertz'=>'s^-1','baud'=>'s^-1',
        'meter'=>null,'liter'=>'dm^3','radian'=>null,'steradian'=>'rad^2','revolution'=>'6.283185307179586.rad',
        'degree'=>'2.777777777777778e-3.r','bit'=>null,'byte'=>'8.bit','octet'=>'8.bit','gram'=>null,'ton'=>'Mg',
        'unified atomic mass unit'=>'1.660538782e-27.kg','mole'=>null,'katal'=>'mol/s','kelvin'=>null,'degree Celcius'=>null,
        'candela'=>null,'lumen'=>'cd.sr','lux'=>'lm/m^2','newton'=>'m.kg.s^-2','pascal'=>'N/m^2','joule'=>'N.m',
        'electronvolt'=>'1.602176487e-19.J','watt'=>'J/s','neper'=>null,'decibel'=>'0.1151293.Np','ampere'=>null,
        'coulomb'=>'s.A','volt'=>'W/A','farad'=>'C/V','ohm'=>'V/A','siemens'=>'A/V','weber'=>'V.s','tesla'=>'Wb/m^2',
        'henry'=>'Wb/A','becquerel'=>'s^-1','gray'=>'m^2.s^-2','sievert'=>'m^2.s^-2'];

    // example derived units
    public $derunits=['square meter'=>'m^2','mole per cubic meter'=>'mol/m^3','joule per mole kelvin'=>'J/(mol.K)',
        'cubic meter'=>'m^3','watt per square meter steradian'=>'W/(m^2.sr)','nanovolt per root hertz'=>'nV/Hz^(1/2)'];

    // textual powers
    public $powers=['squared'=>2,'square'=>2,'cubed'=>3,'cubic'=>3,'root'=>'(1/2)','reciprocal'=>-1];

    /**
     * function to create the symbol string from the name
     * @param string $unit
     */
    public function makesymbol($unit=null)
    {
        if(is_null($unit)) {
            $tests=$this->derunits;
        } else {
            $tests[strtolower($unit)] = '';
        }
        $powers=$this->powers;
        $usyms=$this->unitsymbols;
        $pfixs=$this->siprefsymbols;
        foreach($tests as $test=>$sym) {
            // separate peices
            $analysis=['n'=>[],'d'=>[]];
            if(stristr($test,' per ')) {
                list($n,$d)=explode(" per ",$test);
                $analysis['n']=$this->getparts($n);
                $analysis['d']=$this->getparts($d);
            } else {
                $analysis['n']=$this->getparts($test);
            }
            // analyze
            $npows=$nunts=$dpows=$dunts=[];
            if(!empty($analysis['n'])) {
                if(isset($analysis['n']['power'])) {
                    foreach($analysis['n']['power'] as $pidx=>$npow) {
                        $npows[$pidx] = $powers[$npow];
                    }
                }
                if(isset($analysis['n']['unit'])) {
                    foreach($analysis['n']['unit'] as $uidx=>$nunit) {
                        $nunts[$uidx] = $usyms[$nunit];
                    }
                }
                if(isset($analysis['n']['prefix'])) {
                    foreach($analysis['n']['prefix'] as $uidx=>$npfix) {
                        $npfixs[$uidx] = $pfixs[$npfix];
                    }
                }
            }
            if(!empty($analysis['d'])) {
                if(isset($analysis['d']['power'])) {
                    foreach($analysis['d']['power'] as $pidx=>$dpow) {
                        $dpows[$pidx] = $powers[$dpow];
                    }
                }
                if(isset($analysis['d']['unit'])) {
                    foreach($analysis['d']['unit'] as $uidx=>$dunit) {
                        $dunts[$uidx] = $usyms[$dunit];
                    }
                }
                if(isset($analysis['d']['prefix'])) {
                    foreach($analysis['d']['prefix'] as $uidx=>$dpfix) {
                        $dpfixs[$uidx] = $pfixs[$dpfix];
                    }
                }
            }

            // create symbol
            $symbol="";
            if(!empty($nunts)) {
                foreach($nunts as $idx=>$nunt) {
                    if($idx!=0) { $symbol.="."; }
                    if(!empty($npfixs[$idx])) {
                        $symbol.=$npfixs[$idx];
                    }
                    $symbol.=$nunt;
                    if(!empty($npows[$idx])) {
                        $symbol.="^".$npows[$idx];
                    }
                }
            }
            if(!empty($dunts)) {
                if($symbol!="") {
                    $symbol.="/";
                }
                if(count($dunts)>1) { $symbol.="("; }
                foreach($dunts as $idx=>$dunt) {
                    if($idx!=0) { $symbol.="."; }
                    if(!empty($dpfixs[$idx])) {
                        $symbol.=$dpfixs[$idx];
                    }
                    $symbol.=$dunt;
                    if(!empty($dpows[$idx])) {
                        $symbol.="^".$dpows[$idx];
                    }
                }
                if(count($dunts)>1) { $symbol.=")"; }
            }
            if($unit==null) {
                debug($test);debug($symbol);
            } else {

            }
        }
        if($unit==null) { exit; }
        return $symbol;
    }

    /**
     * identify words in input as units, powers etc
     * @param $input
     * @return array
     */
    private function getparts($input)
    {
        $powers=$this->powers;
        $usyms=$this->unitsymbols;
        $pfixs=array_keys($this->siprefsymbols);
        $parts=explode(" ",$input);
        $analysis=[];
        foreach($parts as $pidx=>$part) {
            foreach($pfixs as $pfix) {
                if(preg_match("/^".$pfix."/",$part)) {
                    $analysis['prefix'][]=$pfix;
                    $part=str_replace($pfix,"",$part);
                }
            }
            if(in_array($part,array_keys($powers))) {
                $analysis['power'][]=$part;
            } elseif(in_array($part,array_keys($usyms))) {
                $analysis['unit'][]=$part;
            }
        }
        return $analysis;
    }

}