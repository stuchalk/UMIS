<?php

/**
 * Class ApiController
 */
class ApiController extends AppController
{

    public $uses=['Api','Strng','Unit','Repsystem'];

    public $jldcxt=[
        'https://umis.stuchalk.domains.unf.edu/files/contexts/umis.jsonld',
        [
            'xsd'=>'https://www.w3.org/2001/XMLSchema#',
            'ncit'=>'http://purl.obolibrary.org/obo/ncit.owl',
            'qkind'=>'http://www.qudt.org/schema/qudt/'
            ],
        ['@base'=>'']
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
     * openapi endpoint page
     */
    public function index()
    {
        // redirect to the SwaggeHub page for the API spec
        $this->redirect('https://app.swaggerhub.com/apis-docs/stuchalk/nist-umis/1.0.0');
    }

    /**
     * conversion service
     * tested only for simple prefix and factor conversions
     * @param mixed $unit1
     * @param mixed $unit2
     * @param mixed $value
     * @param mixed $error
     * @return mixed
     */
    public function convert($unit1=null,$unit2=null,$value=null,$error=null)
    {
        $testvalue=false;
        if(is_null($value)) { $value=1.234;$testvalue=true;}
        $errorgiven=false;
        if(is_null($error)) {
            // estimated
            $tmp=$this->exponentialGen($value);
            //debug($tmp);
            $error=$tmp['error'];
        } else {
            $errorgiven=true;
        }

        // get dimension vectors for unit1 and unit2 and decide if same dimensionality
        $dv1 = $this->Api->getdv($unit1);$dv2 = $this->Api->getdv($unit2);
        $samedim = false;
        if($dv1==$dv2) { $samedim=true; }
        // get base unit for unit1 and unit2 and decide if same
        $bu1 = $this->Api->getbaseunit($unit1);$bu2 = $this->Api->getbaseunit($unit2);
        $samebaseunit = false;
        if($bu1==$bu2) { $samebaseunit=true; }

        // decide how to proceed
        if($samedim&&$samebaseunit) {
            // these are in the same base unit and thus can be converted by a prefix factor
            $u1=$this->Api->getunitbystr($unit1);
            $u2=$this->Api->getunitbystr($unit2);

            $prefix1=$prefix2=$factor1=$factor2=false;
            //debug($u1);debug($u2);
            if(isset($u1['Prefix']['id'])) {
                $prefix1=$u1['Prefix']['value'];
            }
            if(isset($u1['Factor']['id'])) {
                $factor1=$u1['Factor']['nfactor']/$u1['Factor']['dfactor'];
            }
            if(isset($u2['Prefix']['id'])) {
                $prefix2=$u2['Prefix']['value'];
            }
            if(isset($u2['Factor']['id'])) {
                $factor2=$u2['Factor']['nfactor']/$u2['Factor']['dfactor'];
            }
            $ansval=$anserr=null;

            if($prefix1&&$prefix2) {
                $ansval=$value*$prefix1/$prefix2;
                $anserr=$error*$prefix1/$prefix2;
            } elseif($prefix1&&$factor2) {
                // TODO: this 'works' but does not check units which is an issue
                $ansval=$value*$prefix1/$factor2;
                $anserr=$error*$prefix1/$factor2;
                $u1['Prefix']=[];
            }
            $ans=$this->exponentialGen($ansval);
            $err=$this->exponentialGen($anserr);

            // create output
            $output=[];
            $timestamp=date("Ymd:his");
            $base="https://example.org/dsi/conversion/".$timestamp."/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            // general metadata
            $graph=[];
            $graph["@id"]="";
            $graph["@type"]="exam:convertedmeasurement";
            $graph["uid"]="exam:conversion:".$timestamp;
            $graph["name"]="Certified SI Conversion Result";
            $graph["description"]="Automated conversion of a measurement value between SI units.  This process is certified by the National Metrology Institutes (NMIs).";
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]="https://umis.stuchalk.domains.unf.edu/api/convert/<fromunit>/<tounit>/<value>/<error>";
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
            // input
            $input=[];
            $input["@id"]="input/";
            $input["@type"]="exam:conversioninput";
            $input["fromUnit"]=$unit1;
            $input["toUnit"]=$unit2;
            $input["value"]=$value;
            $input["uncertainty"]=$error."";
            $input["testValue"]=$testvalue;
            $input["uncertaintyGiven"]=$errorgiven;
            $output['@graph']["input"]=$input;
            // analysis
            $analysis=[];
            $analysis["@id"]="analysis/";
            $analysis["@type"]="exam:conversionanalysis";
            $analysis["sameDimensions"]=$samedim;
            $analysis["sameBaseUnit"]=$samebaseunit;
            $analysis["fromUnit"]="https://umis.stuchalk.domains.unf.edu/units/view/".$u1['id'];
            $analysis["toUnit"]="https://umis.stuchalk.domains.unf.edu/units/view/".$u2['id'];
            $analysis["conversionType"]="prefixbased";
            $output['@graph']["analysis"]=$analysis;
            // result
            $result=[];
            $result["@id"]="result/";
            $result["@type"]="umis:conversionresult";
            $result["value"]=$ans['scinot'];
            $result["uncertainty"]=$err['scinot'];
            $result["unit"]="https://umis.stuchalk.domains.unf.edu/units/view/".$u2['id'];
            $result["convertedUsing"]=[];
            if(isset($u1['Prefix']['id'])) {
                $factor1=[];
                $factor1["@id"]="factor/1/";
                $factor1["@type"]="exam:conversionfactor";
                $factor1["type"]="https://umis.stuchalk.domains.unf.edu/prefixes/view/".$u1['Prefix']['id'];
                $factor1["uncertainty"]="exact";
                $factor1["exact"]=true;
                $factor1["operation"]="multiplication";
                $result["convertedUsing"][]=$factor1;
            }
            if(isset($u1['Factor']['id'])) {
                $factor1=[];
                $factor1["@id"]="factor/1/";
                $factor1["@type"]="exam:conversionfactor";
                $factor1["type"]="https://umis.stuchalk.domains.unf.edu/factors/view/".$u1['Factor']['id'];
                $factor1["uncertainty"]="exact";
                $factor1["exact"]=true;
                $factor1["operation"]="multiplication";
                $result["convertedUsing"][]=$factor1;
            }
            if(isset($u2['Prefix']['id'])) {
                $factor2=[];
                $factor2["@id"]="factor/2/";
                $factor2["@type"]="exam:conversionfactor";
                $factor2["type"]="https://umis.stuchalk.domains.unf.edu/prefixes/view/".$u2['Prefix']['id'];
                $factor2["uncertainty"]="exact";
                $factor2["exact"]=true;
                $factor2["operation"]="division";
                $result["convertedUsing"][]=$factor2;
            }
            if(isset($u2['Factor']['id'])) {
                $factor2=[];
                $factor2["@id"]="factor/2/";
                $factor2["@type"]="exam:conversionfactor";
                $factor2["type"]="https://umis.stuchalk.domains.unf.edu/factors/view/".$u2['Factor']['id'];
                $factor2["uncertainty"]="exact";
                $factor2["exact"]=true;
                $factor2["operation"]="division";
                $result["convertedUsing"][]=$factor2;
            }
            $output['@graph']["result"]=$result;
            $cert=[];
            $cert["@id"]="certification/";
            $cert["@type"]="exam:certification";
            $cert["authority"]="Global National Metrology Institutes (NMIs)";
            $cert["url"]="https://example.org/dsi/certification/";
            $output['@graph']["certification"]=$cert;
            // signature (fake)
            $proof=[];
            $proof["@id"]="proof/";
            $proof["@type"]="jsig:signatureproof";
            $proof["type"]="Ed25519Signature2018";
            $proof["proofPurpose"]="assertionMethod";
            $proof["created"]="2017-09-23T20:21:34Z";
            $proof["verificationMethod"]="did:exam:123456#key1";
            $proof["challenge"]="2bbgh3dgjg2302d-d2b3gi423d42";
            $proof["domain"]="example.org";
            $proof["jws"]="eyJ0eXAiOiJK...gFWFOEjXk";
            $output["proof"]=$proof;
            //debug($output);exit;
            header('Content-Type: application/json');
            echo json_encode($output);exit;
        } else {
            $u1=$this->Api->getunitbystr($unit1);
            $u2=$this->Api->getunitbystr($unit2);
            debug($u1);debug($u2);exit;

        }

        // Return data if called as requestAction
        //if($this->params['requested']) { return $data; }
    }

    /**
     * unit functions
     * @param string $action
     * @param mixed $unitid
     * @param mixed $rep
     */
    public function units(string $action="list",$unitid=null,$rep=null) {
        // capture what was requested
        $request=$unitid;
        // normalize unitid to id in units table
        if($action=="candim"||$action=="list") {
          // override other comparisons
        } elseif(!is_numeric($unitid)) {
            $found=false;
            // check unit names
            $hit=$this->Unit->find('first',['conditions'=>['name'=>$unitid],'recursive'=>-1]);
            if(!empty($hit)) { $unitid=$hit['Unit']['id'];$found=true; }
            // check unit strings/symbols
            if(!$found) {
                $hit=$this->Api->getunitbystr($unitid);
                if($hit) {
                    if(isset($hit['id'])) {
                        $unitid=$hit['id'];
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode($hit);exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['error'=>'unknown unit']);exit;
                }
            }
        } elseif(!is_integer($unitid+0)) {
            header('Content-Type: application/json');
            echo json_encode(['error'=>'unknown unit']);exit;
        }
        //debug($unitid);exit;
        $output=[];
        $site="https://umis.stuchalk.domains.unf.edu/";
        $context=$this->jldcxt;
        if($action=="list") {
            # list all units
            $units=$this->Unit->find('list',['fields'=>['id','name'],'order'=>'name']);
            $base=$site."allunits/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            $output['generatedAt']=date(DATE_ATOM);
            $output['version']='0.5';
            $graph=[];
            $graph["@id"]="umislist/";
            $graph["@type"]="ncit:NCIT_C43432";
            $graph["uid"]="umis:units:list:0.5";
            $graph["title"]="List of SI Units";
            $graph["description"]="List of SI units in the UMIS database";
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]=$site."api/units/list";
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
            $uoms=[];
            foreach($units as $unitid=>$name) {
                $uom=[];
                $uom["@id"]="unit/".$unitid.'/';
                $uom["@type"]="ncit:NCIT_C25709";
                $uom["name"]=$name;
                $uom["url"]="https://umis.stuchalk.domains.unf.edu/api/units/view/".$unitid;
                $uoms[]=$uom;
            }
            $output['@graph']['units']=$uoms;
        } elseif($action=='view') {
            $data=$this->Api->getunitbyid($unitid);
            $unit=$data['Unit'];
            $qkind=$data['Quantitykind'];
            $usys=$data['Unitsystem'];
            $dimv=$qkind['Dimensionvector'];
            $reps=$data['Representation'];
            $bu=null;
            if($unit['type']!="SI Base") {
                # unit is not a base unit of the SI
                $bu=$qkind['BaseUnit'];
            }
            $pre=null;
            if(!is_null($unit['prefix_id'])) {
                $pre=$data['Prefix'];
            }
            $base=$site."unit/".$unitid;
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            $output['generatedAt']=date(DATE_ATOM);
            $output['version']='0.5';
            $graph=[];
            $graph["@id"]="umisunit";
            $graph["@type"]="ncit:NCIT_C25709";
            if($unit['unitsystem_id']==1) {
                $graph["name"]="SI Unit - ".$unit['name'];
            } else {
                $graph["name"]="Non-SI Unit - ".$unit['name'];
            }
            $graph["description"]=$unit['description'];
            $graph["url"]=$site.'units/view/'.$request;
            if(!empty($unit['url'])) { $graph["defnurl"]=$unit['url']; }
            $graph["apiurl"]=$site."api/units/view/".$request;
            $graph["apiversion"]="0.1";
            $graph["unittype"]=$unit['type'];
            $graph["unitshortcode"]=$unit['shortcode'];
            $graph["timestamp"]=date(DATE_ATOM);
            $output['@graph']=$graph;
            // quantitykind
            $kind=[];
            $kind["@id"]="quantitykind/";
            $kind["@type"]="qkind:QuantityKind";
            $kind["name"]=$qkind['name'];
            $kind["description"]=$qkind['description'];
            $kind["symbol"]=$qkind['symbol'];
            $output['@graph']["quantitykind"]=$kind;
            // dimensionvector
            $dv=[];
            $dv["@id"]="dimensionvector/";
            $dv["@type"]="qkind:QuantityKindDimensionVector";
            $dv["name"]=$dimv['name'];
            $dv["symbol"]=$dimv['symbol'];
            $dv["dimshortcode"]=$dimv['shortcode'];
            $output['@graph']["dimensionvector"]=$dv;
            // representations
            $allreps=[];
            foreach($reps as $rep) {
                $r=[];
                $r["@id"]="representation/".$rep['id']."/";
                $r["@type"]="ncit:NCIT_C67045";
                $r["string"]=$rep['Strng']['string'];
                $r['usagestatus']=$rep['Strng']['status'];
                if(!empty($rep['Unitsystem'])) {
                    $r['unitsystem']=$rep['Unitsystem']['name'];
                    $r['unitsystemurl']=$rep['Unitsystem']['url'];
                } elseif(!empty($rep['Repsystem'])) {
                    $r['repsystem']=$rep['Repsystem']['name'];
                    $r['repsystemurl']=$rep['Repsystem']['url'];
                    if(!is_null($rep['url'])) {
                        $r['repsystemuniturl']=$rep['url'];
                    }
                } else {
                    $r['usagestatus']="discouraged";
                    $r['repsystem']="General (colloquial) usage";
                }
                $allreps[]=$r;
            }
            if(!empty($allreps)) {
                $output['@graph']["representations"]=$allreps;
            }
        } elseif($action=="samedim") {
            # get the dimensionality of a unit and all the other units of that dimensionality
            $data=$this->Api->getunitsbydv($unitid);
            $unit=$data['unit'];
            $dv=$data['dv'];
            $sames=$data['units'];
            // context
            $base=$site."samedimension/".$request."/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            // unit info
            $graph=[];
            $graph["@id"]=$base;
            $graph["@type"]="exam:siunit";
            $graph["name"]="SI Unit - ".$unit['name'];
            $graph["description"]=$unit['description'];
            $graph["url"]=$unit['url'];
            $graph["type"]=$unit['type'];
            $graph["unitshortcode"]=$unit['shortcode'];
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]=$site."api/units/samedim/".$request;
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
            // dimensionality
            $dim=[];
            $dim["@id"]="dimvector/".$dv['id']."/";
            $dim["@type"]="umis:dimensionvector";
            $dim["name"]=$dv['name'];
            $dim["symbol"]=$dv['symbol'];
            $dim["dimshortcode"]=$dv['shortcode'];
            $output['@graph']['dimensionvector']=$dim;
            // units of the same dimensionality
            $samedimunits=[];
            foreach($sames as $same) {
                $units=$same['Unit'];
                foreach($units as $unit) {
                    $uom=[];
                    $uom["@id"]="unit/".$unit['id'].'/';
                    $uom["@type"]="umis:unit";
                    $uom["name"]=$unit['name'];
                    $uom["url"]=$site."units/view/".$unit['id'];
                    $uom["apiurl"]=$site."api/units/view/".$unit['id'];
                    $samedimunits[]=$uom;
                }
            }
            $output['@graph']['dimequivunits']=$samedimunits;
        } elseif($action=="getdim") {
            $data=$this->Api->getunitsbydv($unitid);
            $unit=$data['unit'];
            $dv=$data['dv'];
            // context
            $base=$site."getdimension/".$request."/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            // unit info
            $graph=[];
            $graph["@id"]=$base;
            $graph["@type"]="umis:unit";
            $graph["name"]="SI Unit - ".$unit['name'];
            $graph["dimensionvector"]=$dv['shortcode'];
            $graph["url"]=$unit['url'];
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]="https://umis.stuchalk.domains.unf.edu/api/units/getdim/".$request;
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
        } elseif($action=="candim") {
            $code=$this->Api->candv($unitid);
            // context
            $base=$site."candimension/".$request."/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            // unit info
            $graph=[];
            $graph["@id"]=$base;
            $graph["@type"]="umis:dimensionvector";
            $graph["request"]=$request;
            $graph["canonicaldv"]=$code;
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]="https://umis.stuchalk.domains.unf.edu/api/units/candim/".$request;
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
        } elseif($action=="canunit") {
            $data=$this->Api->getunitbyid($unitid);
            $baseunit=$data['Quantitykind']['BaseUnit'];
            // context
            $base=$site."canunit/".$request."/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            // unit info
            $graph=[];
            $graph["@id"]="";
            $graph["@type"]="umis:baseunit";
            $graph["request"]=$request;
            $graph["sibaseunit"]=$baseunit['shortcode'];
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]=$site."api/units/canunit/".$request;
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
        } elseif($action=="describe") {
            $repsyss=$this->Repsystem->getabbrevs();
            if(!in_array($rep,$repsyss)) {
                echo "Representation system not found";exit;
            }
            $data=$this->Api->getunitbyid($unitid);
            $reps=$data['Representation'];$str="";$repsys=[];
            foreach($reps as $r) {
                if(!empty($r['Repsystem'])&&$r['Repsystem']['abbrev']==$rep) {
                    $repsys=$r['Repsystem'];
                    $str=$r['Strng']['string'];
                }
            }
            // if representation not found then let them know to contact me...
            if(empty($repsys)) {
                echo "No representation of this unit is currently available in this encoding";exit;
            }
            // context
            $base=$site."describe/".$request."/";
            $context[2]['@base']=$base;
            $output['@context']=$context;
            $output['@id']=$base;
            // unit info
            $graph=[];
            $graph["@id"]=$base;
            $graph["@type"]="umis:unitencoding";
            $graph["unit"]=$request;
            $graph["repsystem"]=$repsys['name'];
            $graph["encoding"]=$str;
            $graph["timestamp"]=date(DATE_ATOM);
            $graph["apiurl"]=$site."api/units/describe/".$request;
            $graph["apiversion"]="0.1";
            $output['@graph']=$graph;
            //debug($data);exit;
        }
        header('Content-Type: application/json');
        echo json_encode($output);exit;
    }

    /**
     * context functions
     * (show JSON-LD context files)
     */
    public function context($type="umis") {
        header('Content-Type: application/json');
        if($type=='umis') {
            $file=file_get_contents(WWW_ROOT.'files'.DS.'contexts'.DS.'umis.jsonld');
            echo $file;exit;
        }
    }

    /**
     * Generates a exponential number removing any zeros at the end not needed
     * @param $string
     * @return array
     */
    private function exponentialGen($string) {
        $return=[];
        $return['text']=$string."";
        $return['value']=floatval($string);
        if($string==0) {
            $return+=['dp'=>0,'scinot'=>'0e+0','exponent'=>0,'significand'=>0,'error'=>null,'sf'=>0];
        } elseif(stristr($string,'E')) {
            list($man,$exp)=explode('E',$string);
            if($man>0){
                $sf=strlen($man)-1;
            } else {
                $sf=strlen($man)-2;
            }
            $return['scinot']=$string;
            $return['error']=pow(10,$exp-$sf+1);
            $return['exponent']=$exp;
            $return['significand']=$man;
            $return['dp']=$sf;
        } else {
            $string=str_replace([",","+"],"",$string);
            $num=explode(".",$string);
            $neg=false;
            if(stristr($num[0],'-')) { $neg=true; }
            $dpoint=false;
            if(stristr($string,'.')) { $dpoint=true; }
            // If there is something after the decimal
            if(isset($num[1])){
                $return['dp']=strlen($num[1]);
                if($num[0]!=""&&$num[0]!=0) {
                    // All digits count (-1 for period)
                    if($neg) {
                        // substract 1 for the minus sign and 1 for decimal point
                        $return['sf']=strlen($string)-2;
                        $return['exponent']=strlen($num[0])-2;
                    } else {
                        $return['sf']=strlen($string)-1;
                        $return['exponent']=strlen($num[0])-1;
                    }
                    // Exponent is based on digit before the decimal -1
                } else {
                    // Remove any leading zeroes after decimal and count string length
                    $return['sf']=strlen(ltrim($num[1],'0'));
                    // Count leading zeros
                    preg_match('/^(0*)[1234567890]+$/',$num[1],$match);
                    $return['exponent']=-1*(strlen($match[1]) + 1);
                }
                $return['scinot']=sprintf("%." .($return['sf']-1). "e", $string);
                $s=explode("e",$return['scinot']);
                $return['significand']=$s[0];
                $return['error']=pow(10,$return['exponent']-$return['sf']+1);
            } else {
                $return['dp']=0;
                if($dpoint==false) {
                    $return['scinot']=sprintf("%." .(strlen(rtrim($string,"0"))-1). "e", $string);
                } else {
                    $return['scinot']=sprintf("%." .(strlen($string)-1). "e", $string);
                }

                $s=explode("e",$return['scinot']);
                $return['significand']=$s[0];
                $return['exponent'] = $s[1];
                $z=explode(".",$return['significand']);
                $return['sf']=strlen($return['significand'])-1;
                // Check for negative
                if(isset($z[1])) {
                    $return['error']=pow(10,strlen($z[1])-$s[1]-$neg); // # SF after decimal - exponent
                } else {
                    $return['error']=pow(10,0-$s[1]); // # SF after decimal - exponent
                }
            }
        }
        return $return;
    }

}

?>