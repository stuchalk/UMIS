<?php

/**
 * Class QuantitykindsController
 */
class QuantitykindsController extends AppController
{

	public $uses=['Quantitykind','Quantity','Domain'];
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('index','view','qklist');
	}
	
	/**
	 * List all quantitykinds
	 */
	public function index()
    {
        $data=$this->Quantitykind->find('list',['fields'=>['id','name'],'order'=>['name']]);
        $this->set('data',$data);
    }
	
	/**
	 * View a quantitykind
	 * @param $id
	 */
    public function view($id)
    {
        $c=['Quantitysystem','Dimensionvector','Quantity','Nym'=>['Domain']];
        //$c=['Quantitykind'=>['Quantity','Dimensionvector','Nym'],'Representation'=>['Repsystem']];
        $data=$this->Quantitykind->find('first',['conditions'=>['Quantitykind.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $this->set('data',$data);
    }
	
	/**
	 * Add a quantitykind
	 */
    public function add()
	{
		if(!empty($this->request->data)) {
			//debug($this->request->data);exit;
			$this->Quantitykind->create();
			if($this->Quantitykind->save($this->request->data)) {
				$this->Flash->set('The quantity kind has been added');
				$this->redirect(['action' => 'index']);
			} else {
				$this->Flash->set('The quantity kind could not be added.');
			}} else {
			$quans=$this->Quantity->find('list',['fields'=>['id','name'],'recursive'=>-1]);
			$domains=$this->Domain->find('list',['fields'=>['id','title'],'recursive'=>-1]);
			$this->set('quans',$quans);
			$this->set('domains',$domains);
		}
	}
	
	/**
	 * Get list of quantities one quantity kind and return as JSON
	 * @param $qkid
	 */
	public function qklist($qkid)
	{
		$qks=$this->Quantity->find('list',['conditions'=>['quantitykind_id'=>$qkid],'recursive'=>-1]);
		header('Content-Type: application/json');
		echo "[".json_encode($qks)."]";exit;
	}
}

?>