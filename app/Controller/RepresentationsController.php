<?php

/**
 * Class RepresentationsController
 */
class RepresentationsController extends AppController
{

    public $uses=['Representation','Unit','Quantity','Unitsystem','Repsystem','Nerc','Dbpedia'];

    /**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow();
	}

    /**
     * List all representations
     */
    public function index()
    {
        $c=['Strng','Unit'];$f=['Representation.id','Strng.string','Unit.name'];$o=['Unit.name','Strng.string'];
        $data=$this->Representation->find('list',['conditions'=>['NOT'=>['unit_id'=>null]],'fields'=>$f,'order'=>$o,'contain'=>$c,'recursive'=>-1]);
        $this->set('data',$data);
    }

    /**
	 * View a unit representation
	 * @param $id
	 */
	public function view($id)
	{
		$c=['Strng'=>['Encoding'],'Unitsystem','Repsystem','Unit'];
		$data=$this->Representation->find('first',['conditions'=>['Representation.id'=>$id],'contain'=>$c,'recursive'=>-1]);
		$this->set('data',$data);
	}

	/**
     * Add a unit representation
     */
	public function add()
    {
        if(!empty($this->request->data)) {
            $data=$this->request->data;
            $this->Representation->create();
            if($this->Representation->save($data)) {
                $this->Flash->set('The representation has been added');
                $uid=$data['Representation']['unit_id'];
                $this->redirect('/units/view/'.$uid);
            } else {
                $this->Flash->set('The representation could not be added.');
            }
        } else {
            $units=$this->Unit->find('list',['fields'=>['id','name'],'order'=>'name','recursive'=>-1]);
            $qunts=$this->Quantity->find('list',['fields'=>['id','name'],'order'=>'name','recursive'=>-1]);
            $rsyss=$this->Repsystem->find('list',['fields'=>['id','name'],'order'=>'name','recursive'=>-1]);
            $usyss=$this->Unitsystem->find('list',['fields'=>['id','name'],'order'=>'name','recursive'=>-1]);
            $this->set('units',$units);
            $this->set('qunts',$qunts);
            $this->set('rsyss',$rsyss);
            $this->set('usyss',$usyss);
        }
    }


    public function temp()
    {
	    debug($this->Nerc->list());exit;
    }


}

?>