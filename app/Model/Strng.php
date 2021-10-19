<?php

/**
 * Class Strng
 * String model
 */
class Strng extends AppModel
{
    public $hasMany = ['Encoding','Representation'];

    /**
     * find or add a new strng
     */
    public function add($opts=[])
    {
        $str=$opts['string'];$strid = null;
        $found = $this->find('list',['fields'=>['string','id'],'conditions'=>['string'=>$str]]);
        if(!$found) {
            $this->create();
            $opts = ['name'=>'upright '.$str,'string'=>$str,'status'=>'current'];
            $saved = $this->save(['Strng'=>$opts]);
            if(!$saved) { echo "Error! Strng entry did not save";debug($opts);exit; }
            $strid = $this->id;
        } else {
            $strid = $found[$str];
        }
        return $strid;
    }
}