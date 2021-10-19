<?php

/**
 * Class UnitsystemsController
 */
class UnitsystemsController extends AppController
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
     * Repsystems index
     * @return mixed
     */
    public function index()
    {
        $data=$this->Unitsystem->find('list',['fields'=>['id','title'],'order'=>['name']]);
        $this->set('data',$data);

        // Return data if called as requestAction
        if($this->request->is('requested')) { return $data; }
    }

    /**
     * Repsystems view
     * @param int $id
     * @return mixed
     */
    public function view($id)
    {
        $c=['Unit','Quantitysystem'];
        $data=$this->Unitsystem->find('first',['conditions'=>['Unitsystem.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        $this->set('data',$data);
    }

}

?>