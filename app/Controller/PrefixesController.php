<?php

/**
 * Class PrefixesController
 */
class PrefixesController extends AppController
{
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view','plist');
	}
	
	/**
	 * Get list of prefixes for a quantity system and return as JSON
	 * @param $qsid
	 */
	public function plist($qsid)
	{
		$pres=$this->Prefix->find('list',['fields'=>['id','title'],'conditions'=>['quantitysystem_id'=>$qsid],'recursive'=>-1]);
		header('Content-Type: application/json');
		echo "[".json_encode($pres)."]";exit;
	}

    /**
     * view a prefix
     * @param $id
     */
    public function view($id)
    {
        $data=$this->Prefix->find('first',['conditions'=>['id'=>$id],'recursive'=>-1]);
        $this->set('data',$data);
    }
}

?>