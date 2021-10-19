<?php

/**
 * Class QuantitiesController
 */
class QuantitiesController extends AppController
{
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view','getfield');
	}

    /**
     * List all quantities
     */
    public function index()
    {
        $c=['Quantitykind'];
        $data=$this->Quantity->find('list',['fields'=>['Quantity.id','Quantity.name','Quantitykind.name'],'order'=>['Quantity.name'],'contain'=>$c,'recursive'=>-1]);
        $this->set('data',$data);
    }

    /**
     * View a unit quantity
     * @param $id
     */
    public function view($id)
    {
        $c=['Quantitykind'=>['Unit','Quantitysystem'],'Nym'];
        $data=$this->Quantity->find('first',['conditions'=>['Quantity.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $this->set('data',$data);
    }

	public function getfield($id, $field='name')
	{
		$data=$this->Quantity->find('first',['conditions'=>['Quantity.id'=>$id],'recursive'=>-1]);
		header('Content-Type: text/plain');
		echo $data['Quantity'][$field];exit;
	}
	
}

?>