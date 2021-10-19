<?php

/**
 * Class Udunit
 * Udunit model
 */
class Udunit extends AppModel
{

    /**
     * get a list of the udunit units
     */
    public function list()
    {
        return $this->find('list',['fields'=>['symbol','name'],'conditions'=>['NOT'=>['symbol'=>null]]]);
    }

    /**
     * ingest the udunits specfic in XML (four files)
     * @throws Exception
     */
    public function ingest()
    {
        $file = simplexml_load_file(WWW_ROOT.'files/udunits/udunits2-derived.xml');
        foreach($file->xpath('//unit') as $unit) {
            $p = []; $ud = json_decode(json_encode($unit),true);
            if(isset($ud['type']))              { $p['type']=$ud['type']; unset($ud['type']); }
            if(isset($ud['base']))              { $p['base']='yes'; unset($ud['base']); }
            if(isset($ud['name']))              { $p['name']=json_encode($ud['name']); unset($ud['name']); }
            if(isset($ud['symbol']))            { $p['symbol'] = json_encode($ud['symbol']); unset($ud['symbol']); }
            if(isset($ud['aliases']))           { $p['aliases'] = json_encode($ud['aliases']); unset($ud['aliases']); }
            if(isset($ud['definition']))        { $p['definition'] = $ud['definition']; unset($ud['definition']); }
            if(isset($ud['comment']))           { $p['comment'] = $ud['comment']; unset($ud['comment']); }
            if(isset($ud['dimensionless']))     { $p['dimless'] = 'yes'; unset($ud['dimensionless']); }
            if(isset($ud['def']))               { $p['equiv'] = $ud['def']; unset($ud['def']); }
            if(!empty($ud)) { debug($ud);exit; }
            //debug($p);debug($ud);exit;
            $this->create();
            $this->save(['Udunit'=>$p]);
            $this->clear();
        }
        exit;
    }

    /**
     * get/verify the udunit string using the command line application 'udunits2'
     * @param $str
     * @return false|mixed|string
     */
    public function getstr($str)
    {
        // get ud string
        $command="/opt/bin/udunits2 -H ".$str." -W ".$str;$ud=false;
        $output=explode("\n",shell_exec($command));
        if(stristr($output[0],' = ')&&stristr($output[1],' = ')) {
            $ud=$output[0];
        }
        return $ud;
    }

}