<?php

/**
 * Class Dbpedia
 * Dbpedia model
 */
class Dbpedia extends AppModel
{

    // database table contains all entries referenced as units under
    // http://dbpedia.org/page/Category:Units_of_measurement

    public function ingest()
    {
        $json = file_get_contents('http://dbpedia.org/data/Category:Units_of_measurement.json');
        $parts = json_decode($json,true);
        $subjects=$classes=[];
        foreach($parts as $resource=>$part) {
            foreach($part as $rel=>$junk) {
                if($rel=='http://purl.org/dc/terms/subject') {
                    $subjects[]=$resource;
                }
                if($rel=='http://www.w3.org/2004/02/skos/core#broader') {
                    $classes[]=$resource;
                }
            }
        }
        sort($classes);
        //debug($classes);
        $classes2=[];
        foreach($classes as $class) {
            $json = file_get_contents(str_replace('resource','data',$class).'.json');
            $parts = json_decode($json,true);
            foreach($parts as $resource=>$part) {
                foreach($part as $rel=>$junk) {
                    if($rel=='http://purl.org/dc/terms/subject') {
                        if(!in_array($resource,$subjects)) {
                            $subjects[]=$resource;
                        }
                    }
                    if($rel=='http://www.w3.org/2004/02/skos/core#broader') {
                        if(!in_array($resource,$classes2)&&!in_array($resource,$classes)) {
                            $classes2[]=$resource;
                        }
                   }
                }
            }
        }
        //debug($classes2);
        $classes3=[];
        foreach($classes2 as $class) {
            $json = file_get_contents(str_replace('resource','data',$class).'.json');
            $parts = json_decode($json,true);
            foreach($parts as $resource=>$part) {
                foreach($part as $rel=>$junk) {
                    if($rel=='http://purl.org/dc/terms/subject') {
                        if(!in_array($resource,$subjects)) {
                            $subjects[]=$resource;
                        }
                    }
                    if($rel=='http://www.w3.org/2004/02/skos/core#broader') {
                        if(!in_array($resource,$classes3)&&!in_array($resource,$classes2)&&!in_array($resource,$classes)) {
                            $classes3[]=$resource;
                        }
                    }
                }
            }
        }
        //debug($classes3);

        // add categories
        $all=array_merge($classes,$classes2);
        foreach($all as $cat) {
            $this->create();
            $this->save(['Dbpedia'=>['resource'=>$cat,'type'=>'category']]);
        }
        //debug($all);debug($classes3);exit;

        // data in subjects is a unit or some other resource related to units
        foreach($subjects as $subject) {
            $json = file_get_contents(str_replace('resource','data',$subject).'.json');
            $parts = json_decode($json,true);
            $subs=$parts[$subject]['http://purl.org/dc/terms/subject'];
            $db=[];
            $db['resource']=$subject;
            foreach($subs as $sub) {
                if($sub['value']=='http://dbpedia.org/resource/Category:Units_of_measurement') {
                    $db['type']='unit';
                } elseif(stristr($sub['value'],'http://dbpedia.org/resource/Category:Units_of_')) {
                    $db['category']=str_replace(['http://dbpedia.org/resource/Category:Units_of_','_'],['',' '],$sub['value']);
                }
            }
            debug($db);//exit;
            $this->create();
            $this->save(['Dbpedia'=>$db]);
            //debug($parts);exit;
        }
        debug($subjects);exit;
    }

}