<?php
require_once(ROOT.DS.'app'.DS.'Vendor'.DS.'easyrdf'.DS.'EasyRdf.php');

/**
 * Class QudtController
 */
class QudtController extends AppController
{

	public $uses=['Qudtunit','Qudtqkind','QudtqkindsQudtunit'];
    public $path=WWW_ROOT.'files'.DS.'qudt2'.DS;

    /**
     * get the qudt units from the RDF/XML version of the vocab
     * use protege to convert the .ttl file to .xml first ...
     */
    public function getunits()
    {
        $path=$this->path;
        $file=$path.'VOCAB_QUDT-UNITS-ALL-v2.1.xml';

        $xml = simplexml_load_file($file,'SimpleXMLElement',0,'rdf');
        $ns=$xml->getNamespaces(true);
        $done=$this->find('list',['fields'=>['id','code']]);
        foreach($xml->xpath("//rdf:Description/rdf:type[@rdf:resource='http://qudt.org/schema/qudt/Unit']/..") as $entry) {
            $about=(string)$entry->attributes('rdf',TRUE)->about;
            $code=str_replace('http://qudt.org/vocab/unit/','',$about);
            if(in_array($code,$done)) { echo "Unit ".$code." already added<br/>";continue; }
            //debug($entry->asXML());//exit;
            // variables
            $e=$qks=$uoss=$auoss=$duoss=$iecs=$inrefs=$syms=$ucs=$lbls=[];
            // code
            $e['code']=$code;
            // dimensionvector
            if(isset($entry->xpath('qudt:hasDimensionVector')[0])) {
                $dv = (string)$entry->xpath('qudt:hasDimensionVector')[0]->attributes('rdf', TRUE)[0];
                $e['dimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$dv);
            }
            // quantitykinds
            foreach($entry->xpath('qudt:hasQuantityKind') as $qk) {
                $qks[]=str_replace('http://qudt.org/vocab/quantitykind/','',(string)$qk->attributes('rdf',true)[0]);
            }
            $e['quantitykinds']=json_encode($qks);
            // systems
            // unitofsystem
            if(isset($entry->xpath('qudt:unitOfSystem')[0])) {
                foreach($entry->xpath('qudt:unitOfSystem') as $uos) {
                    $uoss[] = str_replace('http://qudt.org/vocab/sou/', '', (string)$uos->attributes('rdf', true)[0]);
                }
                $e['unitsystems']=json_encode($uoss);
            }
            // allowedunitofsystem
            if(isset($entry->xpath('qudt:allowedUnitOfSystem')[0])) {
                foreach($entry->xpath('qudt:allowedUnitOfSystem') as $auos) {
                    $auoss[]=str_replace('http://qudt.org/vocab/sou/','',(string)$auos->attributes('rdf',true)[0]);
                }
                $e['awdunitsystems']=json_encode($auoss);
            }
            // defindunitofsystem
            if(isset($entry->xpath('qudt:definedUnitOfSystem')[0])) {
                foreach($entry->xpath('qudt:definedUnitOfSystem') as $duos) {
                    $duoss[]=str_replace('http://qudt.org/vocab/sou/','',(string)$duos->attributes('rdf',true)[0]);
                }
                $e['defunitsystems']=json_encode($duoss);
            }
            // derivedcoherentunitofsystem
            if(isset($entry->xpath('qudt:derivedCoherentUnitOfSystem')[0])) {
                foreach($entry->xpath('qudt:derivedCoherentUnitOfSystem') as $duos) {
                    $duoss[]=str_replace('http://qudt.org/vocab/sou/','',(string)$duos->attributes('rdf',true)[0]);
                }
                $e['defcohunitsystems']=json_encode($duoss);
            }
            // derivedunitofsystem
            if(isset($entry->xpath('qudt:derivedUnitOfSystem')[0])) {
                foreach($entry->xpath('qudt:derivedUnitOfSystem') as $duos) {
                    $duoss[]=str_replace('http://qudt.org/vocab/sou/','',(string)$duos->attributes('rdf',true)[0]);
                }
                $e['derunitsystems']=json_encode($duoss);
            }
            // om unit
            if(isset($entry->xpath('qudt:omUnit')[0])) {
                $e['omunit']=(string)$entry->xpath('qudt:omUnit')[0]->attributes('rdf',TRUE)[0];
            }
            // multiplier
            if(isset($entry->xpath('qudt:conversionMultiplier')[0])) {
                $e['multiplier']=(string)$entry->xpath('qudt:conversionMultiplier')[0];
            }
            // dbpedia
            if(isset($entry->xpath('qudt:dbpediaMatch')[0])) {
                $e['dbpedia'] = (string)$entry->xpath('qudt:dbpediaMatch')[0];
            }
            // ieccodes (URI https://cdd.iec.ch/cdd/iec61360/iec61360.nsf/Units has first 501)
            // lists of units @ https://cdd.iec.ch/cdd/iec61360/iec61360.nsf/ListsOfUnitsAllVersions/
            foreach($entry->xpath('qudt:iec61360Code') as $iec) { $iecs[]=(string)$iec; }
            $e['ieccodes']=json_encode($iecs);
            // informativeReferences
            foreach($entry->xpath('qudt:informativeReference') as $inref) { $inrefs[]=(string)$inref; }
            $e['infrefs']=json_encode($inrefs);
            // symbols
            foreach($entry->xpath('qudt:symbol') as $sym) { $syms[]=(string)$sym; }
            foreach($entry->xpath('qudt:expression') as $sym) { $syms[]=(string)$sym; }
            $e['symbols']=json_encode($syms);
            // ucum codes
            foreach($entry->xpath('qudt:ucumCode') as $uc) { $ucs[]=(string)$uc; }
            $e['ucumcodes']=json_encode($ucs);
            // unece code
            if(isset($entry->xpath('qudt:uneceCommonCode')[0])) {
                $e['unececode']=(string)$entry->xpath('qudt:uneceCommonCode')[0];
            }
            // description (any)
            if(isset($entry->xpath('dcterms:description')[0])) {
                $e['description']=(string)$entry->xpath('dcterms:description')[0];
            }
            if(isset($entry->xpath('qudt:description')[0])) {
                $e['description']=(string)$entry->xpath('qudt:description')[0];;
            }
            if(isset($entry->xpath('qudt:plainTextDescription')[0])) {
                $e['description']=(string)$entry->xpath('qudt:plainTextDescription')[0];;
            }
            // labels
            foreach($entry->xpath('rdfs:label') as $lbl) {
                $lbls[]=str_replace('http://qudt.org/vocab/sou/','',(string)$lbl);
            }
            foreach($entry->xpath('skos:altLabel') as $lbl) {
                $lbls[]=str_replace('http://qudt.org/vocab/sou/','',(string)$lbl);
            }
            $e['labels']=json_encode($lbls);
            //debug($e);exit;
            $this->create();
            $this->save(['Qudtunit'=>$e]);
            $saved=$this->id;
            if($saved) {
                echo "Unit ".$about." added<br/>";
            } else {
                echo "Unit ".$about." not added";exit;
            }
        }
        exit;
    }

    /**
     * get the qudt quantitykinds from the vocab .ttl file
     */
    public function getkinds()
    {
        $jfile='kinds.json';
        // if ttl has not been converted to JSON and saved create the first time
        // this is due to the time it takes to read and convert the ttl using EasyRdf
        if(!file_exists($jfile)) {
            $path=$this->path;
            $file=$path.'VOCAB_QUDT-QUANTITY-KINDS-ALL-v2.1.ttl';
            $ttl=file_get_contents($file);
            // Read in turtle
            $graph = new EasyRdf_Graph();
            $graph->parse($ttl,'turtle');
            $kinds = $graph->toRdfPhp();
            $json = json_encode($kinds);
            echo file_put_contents($jfile,$json);
            unset($graph);
        } else {
            $temp = file_get_contents($jfile);
            $kinds= json_decode($temp,true);
        }
        $fcnts=$fields=[];
        $codes=$this->Qudtqkind->find('list',['fields'=>['code']]);
        $quids=$this->Qudtunit->find('list',['fields'=>['id','code']]);
        foreach($kinds as $id=>$kind) {
            if(stristr($id,'http://qudt.org/vocab/quantitykind/')) {
                $data=$inrefs=$isrefs=$nrefs=$lbls=$abbs=$brds=$aunits=$dims=$cdims=$sdims=$iudims=[];
                $idims=$hdims=$urls=$udims=$sees=$syms=$coms=$dbps=$mats=$cmats=$prvs=$sunits=$alts=[];
                // grab all fields and stats
                foreach($kind as $field=>$junk) {
                    if(!isset($fcnts[$field])) { $fcnts[$field]=0; }
                    $fcnts[$field] +=1;
                }
                // identifier
                $code=str_replace('http://qudt.org/vocab/quantitykind/','',$id);
                if(in_array($code,$codes)) { echo "Already done ".$code."<br/>";continue; }
                $data['code']=$code;
                $fields['http://qudt.org/vocab/quantitykind/']='code';
                // description
                if(isset($kind['http://purl.org/dc/terms/description'])) {
                    $data['description']=str_replace('\\','',$kind['http://purl.org/dc/terms/description'][0]['value']);
                    unset($kind['http://purl.org/dc/terms/description']);
                    $fields['http://purl.org/dc/terms/description']='description';
                }
                if(isset($kind['http://qudt.org/schema/qudt/plainTextDescription'])) {
                    $data['description']=str_replace('\\','',$kind['http://qudt.org/schema/qudt/plainTextDescription'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/plainTextDescription']);
                    $fields['http://qudt.org/schema/qudt/plainTextDescription']='description';
                }
                if(isset($kind['http://qudt.org/schema/qudt/description'])) {
                    $data['description']=str_replace('\\','',$kind['http://qudt.org/schema/qudt/description'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/description']);
                    $fields['http://qudt.org/schema/qudt/description']='description';
                }
                // applicable units
                if(isset($kind['http://qudt.org/schema/qudt/applicableUnit'])) {
                    foreach($kind['http://qudt.org/schema/qudt/applicableUnit'] as $aunit) {
                        $aunits[]=str_replace('http://qudt.org/vocab/unit/','',$aunit['value']);
                    }
                    unset($kind['http://qudt.org/schema/qudt/applicableUnit']);
                    //$data['units']=json_encode($aunits);
                    //$fields['http://qudt.org/schema/qudt/applicableUnit']='units';
                }
                if(isset($kind['http://qudt.org/schema/qudt/applicableSIUnit'])) {
                    foreach($kind['http://qudt.org/schema/qudt/applicableSIUnit'] as $sunit) {
                        $sunits[]=str_replace('http://qudt.org/vocab/unit/','',$sunit['value']);
                    }
                    unset($kind['http://qudt.org/schema/qudt/applicableSIUnit']);
                    $data['siunits']=json_encode($sunits);
                    $fields['http://qudt.org/schema/qudt/applicableSIUnit']='siunits';
                }
                // dimension vector related
                if(isset($kind['http://qudt.org/schema/qudt/hasDimensionVector'])) {
                    $data['dimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$kind['http://qudt.org/schema/qudt/hasDimensionVector'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/hasDimensionVector']);
                    $fields['http://qudt.org/schema/qudt/hasDimensionVector']='dimvector';
                }
                if(isset($kind['http://qudt.org/schema/qudt/qkdvDenominator'])) {
                    $data['ddimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$kind['http://qudt.org/schema/qudt/qkdvDenominator'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/qkdvDenominator']);
                    $fields['http://qudt.org/schema/qudt/qkdvDenominator']='ddimvector';
                }
                if(isset($kind['http://qudt.org/schema/qudt/qkdvNumerator'])) {
                    $data['ndimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$kind['http://qudt.org/schema/qudt/qkdvNumerator'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/qkdvNumerator']);
                    $fields['http://qudt.org/schema/qudt/qkdvNumerator']='ndimvector';
                }
                // informative references
                if(isset($kind['http://qudt.org/schema/qudt/informativeReference'])) {
                    foreach($kind['http://qudt.org/schema/qudt/informativeReference'] as $iref) { $inrefs[]=$iref['value']; }
                    $data['infrefs']=json_encode($inrefs);
                    unset($kind['http://qudt.org/schema/qudt/informativeReference']);
                    $fields['http://qudt.org/schema/qudt/informativeReference']='infrefs';
                }
                // iso normative references
                if(isset($kind['http://qudt.org/schema/qudt/isoNormativeReference'])) {
                    foreach($kind['http://qudt.org/schema/qudt/isoNormativeReference'] as $isref) { $isrefs[]=$isref['value']; }
                    $data['isorefs']=json_encode($isrefs);
                    unset($kind['http://qudt.org/schema/qudt/isoNormativeReference']);
                    $fields['http://qudt.org/schema/qudt/isoNormativeReference']='isorefs';
                }
                // normative references
                if(isset($kind['http://qudt.org/schema/qudt/normativeReference'])) {
                    foreach($kind['http://qudt.org/schema/qudt/normativeReference'] as $nref) { $nrefs[]=$nref['value']; }
                    $data['nrefs']=json_encode($nrefs);
                    unset($kind['http://qudt.org/schema/qudt/normativeReference']);
                    $fields['http://qudt.org/schema/qudt/normativeReference']='nrefs';
                }
                // urls
                if(isset($kind['http://qudt.org/schema/qudt/url'])) {
                    foreach($kind['http://qudt.org/schema/qudt/url'] as $url) { $urls[]=$url['value']; }
                    $data['urls']=json_encode($urls);
                    unset($kind['http://qudt.org/schema/qudt/url']);
                    $fields['http://qudt.org/schema/qudt/url']='urls';
                }
                // latex definition
                if(isset($kind['http://qudt.org/schema/qudt/latexDefinition'])) {
                    $data['latexdefn']=str_replace('http://qudt.org/schema/qudt/latexDefinition','',$kind['http://qudt.org/schema/qudt/latexDefinition'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/latexDefinition']);
                    $fields['http://qudt.org/schema/qudt/latexDefinition']='latexdefn';
                }
                // latex symbol
                if(isset($kind['http://qudt.org/schema/qudt/latexSymbol'])) {
                    $data['latexsymb']=str_replace('http://qudt.org/schema/qudt/latexSymbol','',$kind['http://qudt.org/schema/qudt/latexSymbol'][0]['value']);
                    unset($kind['http://qudt.org/schema/qudt/latexSymbol']);
                    $fields['http://qudt.org/schema/qudt/latexSymbol']='latexsymb';
                }
                // symbol
                if(isset($kind['http://qudt.org/schema/qudt/symbol'])) {
                    foreach ($kind['http://qudt.org/schema/qudt/symbol'] as $sym) { $syms[]=$sym['value']; }
                    unset($kind['http://qudt.org/schema/qudt/symbol']);
                    $data['symbols']=json_encode($syms);
                    $fields['http://qudt.org/schema/qudt/symbol']='symbols';
                }
                // see also
                if(isset($kind['http://www.w3.org/2000/01/rdf-schema#seeAlso'])) {
                    foreach ($kind['http://www.w3.org/2000/01/rdf-schema#seeAlso'] as $see) { $sees[] = $see['value']; }
                    unset($kind['http://www.w3.org/2000/01/rdf-schema#seeAlso']);
                    $data['seealso']=json_encode($sees);
                    $fields['http://www.w3.org/2000/01/rdf-schema#seeAlso']='seealso';
                }
                // comments
                if(isset($kind['http://www.w3.org/2000/01/rdf-schema#comment'])) {
                    foreach ($kind['http://www.w3.org/2000/01/rdf-schema#comment'] as $com) { $coms[] = $com['value']; }
                    unset($kind['http://www.w3.org/2000/01/rdf-schema#comment']);
                    $data['comments']=json_encode($coms);
                    $fields['http://www.w3.org/2000/01/rdf-schema#comment']='comments';
                }
                // dbpedia
                if(isset($kind['http://qudt.org/schema/qudt/dbpediaMatch'])) {
                    foreach ($kind['http://qudt.org/schema/qudt/dbpediaMatch'] as $dbp) { $dbps[] = $dbp['value']; }
                    unset($kind['http://qudt.org/schema/qudt/dbpediaMatch']);
                    $data['dbpedia']=json_encode($dbps);
                    $fields['http://www.w3.org/2000/01/rdf-schema#comment']='comments';
                }
                // labels
                if(isset($kind['http://www.w3.org/2000/01/rdf-schema#label'])) {
                    foreach($kind['http://www.w3.org/2000/01/rdf-schema#label'] as $lbl) { $lbls[]=$lbl['value']; }
                    unset($kind['http://www.w3.org/2000/01/rdf-schema#label']);
                    $data['labels']=json_encode($lbls);
                    $fields['http://www.w3.org/2000/01/rdf-schema#label']='labels';
                }
                // broader
                if(isset($kind['http://www.w3.org/2004/02/skos/core#broader'])) {
                    foreach($kind['http://www.w3.org/2004/02/skos/core#broader'] as $brd) { $brds[]=$brd['value']; }
                    $data['broader']=json_encode($brds);
                    unset($kind['http://www.w3.org/2004/02/skos/core#broader']);
                    $fields['http://www.w3.org/2004/02/skos/core#broader']='broader';
                }
                // dimension related
                if(isset($kind['http://qudt.org/schema/qudt/baseUnitDimensions'])) {
                    foreach($kind['http://qudt.org/schema/qudt/baseUnitDimensions'] as $dim) { $dims[]=$dim['value']; }
                    $data['basedims']=json_encode($dims);
                    unset($kind['http://qudt.org/schema/qudt/baseUnitDimensions']);
                    $fields['http://qudt.org/schema/qudt/baseUnitDimensions']='basedims';
                }
                if(isset($kind['http://qudt.org/schema/qudt/baseCGSUnitDimensions'])) {
                    foreach($kind['http://qudt.org/schema/qudt/baseCGSUnitDimensions'] as $cdim) { $cdims[]=$cdim['value']; }
                    $data['basecgsdims']=json_encode($cdims);
                    unset($kind['http://qudt.org/schema/qudt/baseCGSUnitDimensions']);
                    $fields['http://qudt.org/schema/qudt/baseCGSUnitDimensions']='basecgsdims';
                }
                if(isset($kind['http://qudt.org/schema/qudt/baseSIUnitDimensions'])) {
                    foreach($kind['http://qudt.org/schema/qudt/baseSIUnitDimensions'] as $sdim) { $sdims[]=$sdim['value']; }
                    $data['basesidims']=json_encode($sdims);
                    unset($kind['http://qudt.org/schema/qudt/baseSIUnitDimensions']);
                    $fields['http://qudt.org/schema/qudt/baseSIUnitDimensions']='basesidims';
                }
                if(isset($kind['http://qudt.org/schema/qudt/baseImperialUnitDimensions'])) {
                    foreach($kind['http://qudt.org/schema/qudt/baseImperialUnitDimensions'] as $idim) { $idims[]=$idim['value']; }
                    $data['baseimpdims']=json_encode($idims);
                    unset($kind['http://qudt.org/schema/qudt/baseImperialUnitDimensions']);
                    $fields['http://qudt.org/schema/qudt/baseImperialUnitDimensions']='baseimpdims';
                }
                if(isset($kind['http://qudt.org/schema/qudt/baseUSCustomaryUnitDimensions'])) {
                    foreach($kind['http://qudt.org/schema/qudt/baseUSCustomaryUnitDimensions'] as $udim) { $udims[]=$udim['value']; }
                    $data['baseusdims']=json_encode($udims);
                    unset($kind['http://qudt.org/schema/qudt/baseUSCustomaryUnitDimensions']);
                    $fields['http://qudt.org/schema/qudt/baseUSCustomaryUnitDimensions']='baseusdims';
                }
                if(isset($kind['http://qudt.org/schema/qudt/baseISOUnitDimensions'])) {
                    foreach($kind['http://qudt.org/schema/qudt/baseISOUnitDimensions'] as $iudim) { $iudims[]=$iudim['value']; }
                    $data['baseisodims']=json_encode($iudims);
                    unset($kind['http://qudt.org/schema/qudt/baseISOUnitDimensions']);
                    $fields['http://qudt.org/schema/qudt/baseISOUnitDimensions']='baseisodims';
                }
                if(isset($kind['http://qudt.org/schema/qudt/hasDimension'])) {
                    foreach($kind['http://qudt.org/schema/qudt/hasDimension'] as $hdim) { $hdims[]=$hdim['value']; }
                    $data['dims']=json_encode($hdims);
                    unset($kind['http://qudt.org/schema/qudt/hasDimension']);
                    $fields['http://qudt.org/schema/qudt/hasDimension']='dims';
                }
                // abbreviations
                if(isset($kind['http://qudt.org/schema/qudt/abbreviation'])) {
                    foreach($kind['http://qudt.org/schema/qudt/abbreviation'] as $abb) { $abbs[]=$abb['value']; }
                    $data['abbrevs']=json_encode($abbs);
                    unset($kind['http://qudt.org/schema/qudt/abbreviation']);
                    $fields['http://qudt.org/schema/qudt/abbreviation']='abbrevs';
                }
                // exact matches
                if(isset($kind['http://www.w3.org/2004/02/skos/core#exactMatch'])) {
                    foreach($kind['http://www.w3.org/2004/02/skos/core#exactMatch'] as $mat) { $mats[]=$mat['value']; }
                    $data['matches']=json_encode($mats);
                    unset($kind['http://www.w3.org/2004/02/skos/core#exactMatch']);
                    $fields['http://www.w3.org/2004/02/skos/core#exactMatch']='matches';
                }
                if(isset($kind['http://qudt.org/schema/qudt/exactMatch'])) {
                    foreach($kind['http://qudt.org/schema/qudt/exactMatch'] as $mat) { $mats[]=$mat['value']; }
                    $data['matches']=json_encode($mats);
                    unset($kind['http://qudt.org/schema/qudt/exactMatch']);
                    $fields['http://qudt.org/schema/qudt/exactMatch']='matches';
                }
                // close matches
                if(isset($kind['http://www.w3.org/2004/02/skos/core#closeMatch'])) {
                    foreach($kind['http://www.w3.org/2004/02/skos/core#closeMatch'] as $cmat) { $cmats[]=$cmat['value']; }
                    $data['cmatches']=json_encode($mats);
                    unset($kind['http://www.w3.org/2004/02/skos/core#closeMatch']);
                    $fields['http://www.w3.org/2004/02/skos/core#closeMatch']='cmatches';
                }
                // alt labels
                if(isset($kind['http://www.w3.org/2004/02/skos/core#altLabel'])) {
                    foreach($kind['http://www.w3.org/2004/02/skos/core#altLabel'] as $alt) { $alts[]=$alt['value']; }
                    $data['altlabels']=json_encode($alts);
                    unset($kind['http://www.w3.org/2004/02/skos/core#altLabel']);
                    $fields['http://www.w3.org/2004/02/skos/core#altLabel']='altlabels';
                }
                // sameas
                if(isset($kind['http://www.w3.org/2002/07/owl#sameAs'])) {
                    foreach($kind['http://www.w3.org/2002/07/owl#sameAs'] as $sam) { $sams[]=$sam['value']; }
                    $data['sameas']=json_encode($sams);
                    unset($kind['http://www.w3.org/2002/07/owl#sameAs']);
                    $fields['http://www.w3.org/2002/07/owl#sameAs']='sameas';
                }
                // derived from
                if(isset($kind['http://www.w3.org/ns/prov#wasDerivedFrom'])) {
                    foreach($kind['http://www.w3.org/ns/prov#wasDerivedFrom'] as $prv) { $prvs[]=$prv['value']; }
                    $data['comesfrom']=json_encode($prvs);
                    unset($kind['http://www.w3.org/ns/prov#wasDerivedFrom']);
                    $fields['http://www.w3.org/ns/prov#wasDerivedFrom']='comesfrom';
                }
                // deletions needed...
                unset($kind['http://www.w3.org/1999/02/22-rdf-syntax-ns#type']);
                unset($kind['http://www.w3.org/2000/01/rdf-schema#isDefinedBy']);
                unset($kind['http://qudt.org/schema/qudt/systemDerivedQuantityKind']);
                unset($kind['http://www.linkedmodel.org/schema/vaem#todo']);
                unset($kind['http://qudt.org/schema/qudt/expression']);
                unset($kind['http://qudt.org/schema/qudt/outOfScope']);
                unset($kind['http://qudt.org/schema/qudt/id']);
                //debug($aunits);debug($quids);exit;
                $this->Qudtqkind->create();
                $saved=$this->Qudtqkind->save(['Qudtqkind'=>$data]);
                if($saved) {
                    //debug($saved);debug($aunits);debug($quids);exit;
                    $qkid=$this->Qudtqkind->id;
                    foreach($aunits as $aunit) {
                        $quid=array_search($aunit,$quids);
                        if($quid=='') { echo 'Unit '.$aunit." not found!<br/>";continue; }
                        $this->QudtqkindsQudtunit->create();
                        $opts=['qudtqkind_id'=>$qkid,'qudtunit_id'=>$quid];
                        $this->QudtqkindsQudtunit->save(['QudtqkindsQudtunit'=>$opts]);
                        $this->QudtqkindsQudtunit->clear();
                    }
                }
                echo "Done ".$data['code']."<br/>";
                //debug($data);exit;
                if(!empty($kind)) { debug($kind);exit; }
            } else {
                continue;
            }
        }
        ksort($fcnts);debug(count($fcnts));debug($fcnts);
        ksort($fields);debug(count($fields));debug($fields);
        $ufields=array_unique($fields);sort($ufields);debug(count($ufields));debug($ufields);exit;
    }

    public function temp()
    {
        $unit = $this->Qudtunit->find('first',['contain'=>['Unece','Nerc','Iec','Wiki'],'recursive'=>-1]);
        debug($unit);exit;
    }
}
