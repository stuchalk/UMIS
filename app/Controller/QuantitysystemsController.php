<?php

/**
 * Class QuantitysystemsController
 */
class QuantitysystemsController extends AppController
{
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view');
	}
	
	/**
	 * List all quantity systems
	 * @return mixed
	 */
	public function index()
	{
		$data=$this->Quantitysystem->find('list',['fields'=>['id','title'],'order'=>['title']]);
		$this->set('data',$data);
		// Return data if called as requestAction
		if($this->request->is('requested')) { return $data; }
	}
	
	/**
	 * Add a quantity system
	 */
	public function add()
    {
        if($this->request->is('post')) {
            $resp=$this->File->upload($this->request->data['File']['file']);
            debug($resp);
            exit;
        }
    }
	
	/**
	 * View a quantity system
	 * @param $id
	 */
    public function view($id)
	{
		if(is_numeric($id)) {
            $data=$this->Quantitysystem->find('first',['conditions'=>['id'=>$id],'contain'=>['Dimension'=>['order'=>'type']],'recursive'=>-1]);
		} else {
            $data=$this->Quantitysystem->find('first',['conditions'=>['abbrev'=>$id],'contain'=>['Dimension'=>['order'=>'type']],'recursive'=>-1]);
		}
        $this->set('data',$data);
    }
}
