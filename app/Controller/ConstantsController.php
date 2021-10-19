<?php

/**
 * Class ConstantsController
 */
class ConstantsController extends AppController
{
    /**
     * models to use in this class
     * @var $uses string[]
     */
    public $uses=['Constant'];

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    /**
     * Constants index
     * @return mixed
     */
    public function index()
    {
        $data=$this->Constant->find('list',['fields'=>['id','name'],'order'=>'name']);
        $this->set('data',$data);

        // Return data if called as requestAction
        if($this->params['requested']) { return $data; }
    }

    /**
     * Units view
     * @param int $id
     * @return mixed
     */
    public function view($id)
    {
        $c=['Quantity'=>['Quantitykind'=>['Unit','Dimensionvector'],'Domain'],'Unit'];
        $data=$this->Constant->find('first',['conditions'=>['Constant.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $this->set('data',$data);

        // Return data if called as requestAction
        if($this->params['requested']) { return $data; }
    }

    /**
     * Units output in json
     * @param int $id
     * @return mixed
     */
    public function json(int $id)
    {
        $c=['Quantity'=>['Quantitykind'=>['Dimensionvector']],'Unit'];
        $data=$this->Constant->find('first',['conditions'=>['Constant.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $con=$data['Constant'];
        $q=$data['Quantity'];
        $u=$data['Unit'];
        $qk=$q['Quantitykind'];
        $dv=$qk['Dimensionvector'];
        // constant metadata
        $output=[];
        $output["name"]=$con['name'];
        $output["description"]=$con['description'];
        $output["version"]=$con['version'];
        $output["symbol"]=$con['symbol'];
        $output["validfrom"]=$con['validfrom'];
        $output['mantissa']=$con['mantissa'];
        $output['exponent']=$con['exponent'];
        if($con['exact']=='yes') {
            $output["stdUncertainty"]="exact";
            $output["relStdUncertainty"]="exact";
        } else {
            $output["stdUncertainty"]=$con['stduncertainty'];
            $output["relStdUncertainty"]=$con['relstduncertainty'];
        }
        $output["unitString"]=$con['unitstr'];
        $output["unitStringSI"]=$con['unitstrsi'];
        $output["unitCode"]=$u['shortcode'];
        $output["dimensionvectorCode"]=$dv['shortcode'];
        $output["provenance"]=[
            "authority"=>"EXAM/NMI",
            "timestamp"=>"2018-12-31",
            "ref"=>"https://example.org/tgfc"
        ];
        header('Content-Type: application/json');
        echo json_encode($output);exit;
    }

    /**
     * Units output in jsonld
     * @param int $id
     * @return mixed
     */
    public function jsonld(int $id)
    {
        $c=['Quantity'=>['Quantitykind'=>['Dimensionvector']],'Unit'];
        $data=$this->Constant->find('first',['conditions'=>['Constant.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $con=$data['Constant'];
        $q=$data['Quantity'];
        $u=$data['Unit'];
        $qk=$q['Quantitykind'];
        $dv=$qk['Dimensionvector'];
        // context
        $output=[];
        $base="https://umis.stuchalk.domains.unf.edu/constants/".$con['shortname']."/";
        $output['@context']=[
            "https://example.org/digitalSI/context/constants.jsonld",
            [
                "xsd"=>"https://www.w3.org/2001/XMLSchema#",
                "exam"=>"https://example.org/digitalSI/ontology/si.owl"
            ],
            ["@base"=>$base]
        ];
        $output['@id']=$base;
        $output['@graph']=[];
        // constant metadata
        $graph=[];
        $graph["@id"]="";
        $graph["@type"]="exam:physical_constant";
        $graph["name"]=$con['name'];
        $graph["description"]=$con['description'];
        $graph["version"]=$con['version'];
        $graph["symbol"]=$con['symbol'];
        $graph["validfrom"]=$con['validfrom'];
        $graph['mantissa'] =[
            "@value"=>$con['mantissa'],
            "@type"=>"xsd:decimal"
        ];
        $graph['exponent'] =[
            "@value"=>$con['exponent'],
            "@type"=>"xsd:integer"
        ];
        if($con['exact']=='yes') {
            $graph["stdUncertainty"]="exact";
            $graph["relStdUncertainty"]="exact";
        } else {
            $graph["stdUncertainty"]=$con['stduncertainty'];
            $graph["relStdUncertainty"]=$con['relstduncertainty'];
        }
        $graph["unitString"]=$con['unitstr'];
        $graph["unitStringSI"]=$con['unitstrsi'];
        $graph["unitCode"]=$u['shortcode'];
        $graph["dimensionvectorCode"]=$dv['shortcode'];
        $graph["provenance"]=[
            "@id"=>"provenance/",
            "@type"=>"exam:provenance",
            "authority"=>"exam",
            "timestamp"=>"2018-12-31",
            "ref"=>"https://example.org/tgfc"
        ];
        $output['@graph']=$graph;
        //debug($output);exit;
        header('Content-Type: application/ld+json');
        echo json_encode($output);exit;
    }

}

?>