<?php

/**
 * Class Iec
 * Iec model
 */
class Iec extends AppModel
{

    /**
     * General function to add a new nym
     * @param array $data
     * @return integer
     * @throws
     */
    public function add($data)
    {
        $model = 'Iec';
        $this->create();
        $ret = $this->save([$model => $data]);
        $this->clear();
        return $ret[$model];
    }

    /**
     * ingest IEC codes from dump of list of unit pages
     * files in folder files/iec
     */
    public function ingest()
    {
        $dir = new Folder(WWW_ROOT.'/files/iec');
        $files = $dir->find('.*\.html');
        // add html file data
        if(false) {
            foreach($files as $file) {
                $html = file_get_contents(WWW_ROOT.'files/iec/'.$file);
                // separate out the english section of document
                $chunks = explode('<div align="center">LIST OF UNITS</div></td></tr>',$html);
                $text = $chunks[1];
                // get the preferred name for the quantity
                preg_match('/Preferred\sname:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
                $q = $m[1];
                // get the SI coherent derived unit
                preg_match('/SI coherent derived unit: .+?<br>\s<.+?>(.+?)<\/td><\/tr>/',$text,$m);
                $c = [0=>null,1=>null];
                if(isset($m[1])&&!empty($m[1])) {
                    $c = explode(' - ',str_replace('</a>','',$m[1])); // code and symbol
                }
                // get the SI coherent derived unit
                preg_match('/Codes of units: .+?<br>\s(?:<a.+?>(.+?)<\/a>)+<\/td><\/tr>/',$text,$m);
                if(!isset($m[1])||empty($m[1])) { continue; }
                $units=explode('</a><br>',$m[1]);
                foreach($units as $uidx=>$unit) {
                    if(stristr($unit,'>')) {
                        $temp=explode('>',$unit);
                        $units[$uidx]=$temp[1];
                    }
                }
                // save units
                $all=$this->find('list',['fields'=>['id','code']]);
                foreach($units as $unit) {
                    list($code,$name) = explode(' - ',$unit);
                    if(in_array($code,$all)) {
                        echo "Unit '".$name."' already added</br>";
                        continue;
                    }
                    $this->create();
                    $opts = ['quantity'=>$q,'coherent_code'=>$c[0],'coherent_symb'=>$c[1],'code'=>$code,'name'=>$name];
                    $saved = $this->save(['Iec'=>$opts]);
                    if($saved) {
                        echo "Saved unit '".$name."'</br>";
                    } else {
                        echo "Unit not saved...";debug($opts);exit;
                    }
                }
            }
        }
        // using the DB table go suck out other metadata from the unit pages
        $units = $this->find('list',['fields'=>['id','code']]);
        $done = $this->find('list',['fields'=>['id','code'],'conditions'=>['NOT'=>['prefname'=>null]]]);
        foreach($units as $uid=>$unit) {
            if(in_array($unit,$done)) { echo "Unit ".$unit." complete<br/>";continue; }
            $flds = ['prefname'=>null,'synonym'=>null,'shortname'=>null,'defn'=>null,'defnsrc'=>null,'unececode'=>null];
            $html = file_get_contents('https://cdd.iec.ch/cdd/iec61360/iec61360.nsf/Units/'.str_replace(['/','#'],['-','%23'],$unit));
            // get english chunk
            $chunks = explode('<div align="center">UNIT</div></td></tr>',$html);
            $text = $chunks[1];
            // preffered name
            preg_match('/Preferred\sname:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['prefname'] = $m[1]; }
            // synonymous name (optional)
            preg_match('/Synonymous\sname:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['synonym'] = $m[1]; }
            // short name
            preg_match('/Short\sname:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['shortname'] = $m[1]; }
            // definition
            preg_match('/Definition:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['defn'] = $m[1]; }
            // unece
            preg_match('/Remark:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['unececode'] = str_replace('UN/ECE code: ','',$m[1]); }
            // definition
            preg_match('/Definition source:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['defnsrc'] = $m[1]; }
            // unit in text
            preg_match('/Unit\sin\stext:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['unittext'] = $m[1]; }
            // unit structure
            preg_match('/Unit\sstructure:\s.+?<br>\s(.+?)<\/td><\/tr>/',$text,$m);
            if(isset($m[1])&&$m[1]!='') { $flds['unitstruc'] = $m[1]; }
            // save data
            $this->id=$uid;
            $saved = $this->save(['Iec'=>$flds]);
            if($saved) { echo "Unit ".$unit." saved<br/>"; }
        }
    }
}