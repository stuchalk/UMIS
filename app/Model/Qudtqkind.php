<?php

/**
 * Class Qudtqkind
 * Qudt quantity kind model
 */
class Qudtqkind extends AppModel
{

    public $hasAndBelongsToMany=['Qudtunit'];

    public function ingest()
    {
        $Unit = ClassRegistry::Init('Qudtunit');
        $QUnit = ClassRegistry::Init('QudtqkindsQudtunit');
        $file = simplexml_load_file(WWW_ROOT.'files/qudt2/qudt21qkindsrdf.xml','SimpleXMLElement',0,'rdf');
        $ns=$file->getNamespaces(true);
        $done=$this->find('list',['fields'=>['id','code']]);
        foreach($file->xpath('//rdf:Description') as $entry) {
            $about=(string)$entry->attributes('rdf',TRUE)->about;
            if(!stristr($about,'/quantitykind')) { continue; }
            $code=str_replace('http://qudt.org/vocab/quantitykind/','',$about);
            if(in_array($code,$done)) { echo "Quantitykind ".$code." already added<br/>";continue; }
            // variables
            $e=$buds=$inrefs=$isorefs=$syms=$ucs=$lbls=[];
            // code
            $e['code']=$code;
            // dimensionvector
            if(isset($entry->xpath('qudt:hasDimensionVector')[0])) {
                $dv = (string)$entry->xpath('qudt:hasDimensionVector')[0]->attributes('rdf', TRUE)[0];
                $e['dimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$dv);
            }
            // qkdvDenominator
            if(isset($entry->xpath('qudt:qkdvDenominator')[0])) {
                $dv = (string)$entry->xpath('qudt:hasDimensionVector')[0]->attributes('rdf', TRUE)[0];
                $e['ddimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$dv);
            }
            // qkdvNumerator
            if(isset($entry->xpath('qudt:qkdvNumerator')[0])) {
                $dv = (string)$entry->xpath('qudt:qkdvNumerator')[0]->attributes('rdf', TRUE)[0];
                $e['ndimvector']=str_replace('http://qudt.org/vocab/dimensionvector/','',$dv);
            }
            // baseUnitDimensions
            foreach($entry->xpath('qudt:baseUnitDimensions') as $bud) {
                $buds[]=(string)$bud;
            }
            $e['baseunitdims']=json_encode($buds);
            // dbpedia
            if(isset($entry->xpath('qudt:dbpediaMatch')[0])) {
                $e['dbpedia'] = (string)$entry->xpath('qudt:dbpediaMatch')[0];
            }
            // informativeReferences
            foreach($entry->xpath('qudt:informativeReference') as $inref) {
                $ref=(string)$inref;
                if(stristr($ref,'iso.org/')) {
                    $isorefs[]=(string)$inref;
                } else {
                    $inrefs[]=(string)$inref;
                }
            }
            $e['infrefs']=json_encode($inrefs);
            // isoNormativeReferences
            foreach($entry->xpath('qudt:isoNormativeReference') as $isoref) { $isorefs[]=(string)$isoref; }
            foreach($entry->xpath('qudt:normativeReference') as $isoref) { $isorefs[]=(string)$isoref; }
            $e['normfrefs']=json_encode($isorefs);

            // symbols
            foreach($entry->xpath('qudt:symbol') as $sym) { $syms[]=(string)$sym; }
            foreach($entry->xpath('qudt:latexSymbol') as $sym) { $syms[]=(string)$sym; }
            foreach($entry->xpath('qudt:expression') as $sym) { $syms[]=(string)$sym; }
            $e['symbols']=json_encode($syms);

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

            // latex definition
            if(isset($entry->xpath('qudt:latexDefinition')[0])) {
                $e['latexdefn']=(string)$entry->xpath('qudt:latexDefinition')[0];
            }

            // labels
            foreach($entry->xpath('rdfs:label') as $lbl) {
                $lbls[]=str_replace('http://qudt.org/vocab/sou/','',(string)$lbl);
            }
            foreach($entry->xpath('skos:altLabel') as $lbl) {
                $lbls[]=str_replace('http://qudt.org/vocab/sou/','',(string)$lbl);
            }
            $e['labels']=json_encode($lbls);
            //debug($entry->asXML());debug($e);exit;

            $this->create();
            $this->save(['Qudtqkind'=>$e]);
            $qkid=$this->id;
            if($qkid) {
                echo "QKind ".$about." added<br/>";
            } else {
                echo "QKind ".$about." not added";exit;
            }
            if(isset($entry->xpath('qudt:applicableUnit')[0])) {
                foreach($entry->xpath('qudt:applicableUnit') as $au) {
                    $rsrc = (string)$au->attributes('rdf', TRUE)[0];
                    $code=str_replace('http://qudt.org/vocab/unit/','',$rsrc);
                    // get unitid
                    $uid=$Unit->find('list',['fields'=>['code','id'],'conditions'=>['code'=>$code]]);
                    $conds=['qudtunit_id'=>$uid[$code],'qudtqkind_id'=>$qkid];
                    $QUnit->create();
                    $QUnit->save(['QudtqkindsQudtunit'=>$conds]);
                    $QUnit->clear();
                }
            }
        }
        exit;
    }
}