<?php
/**
* Class StrngsController
*/
class StrngsController extends AppController
{

    public $uses = ['Strng', 'Quantitykind', 'Quantitysystem', 'Repsystem'];

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    /**
     * Repstrings index
     * @return mixed
     */
    public function index()
    {
        $f=['Repstring.id','Repstring.name'];
        $c=['Repstringencoding'];$o=['Repstring.name'];
        $data=$this->Repstring->find('list',['fields'=>$f,'contain'=>$c,'order'=>$o]);
        $this->set('data',$data);
        // Return data if called as requestAction
        if($this->params['requested']) { return $data; }
    }

    /**
     * Repstrings view
     * @param int $id
     * @return mixed
     */
    public function view($id)
    {
        if(!(is_numeric($id))) {
            // translate from symbol to string
            $rstr=$this->Strng->find('first',['conditions'=>['string'=>$id]]);
            $id=$rstr['Strng']['id'];
        }

        $c=['Encoding','Representation'=>['Repsystem','Unitsystem']];
        $data=$this->Strng->find('first',['conditions'=>['Repstring.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        debug($data);exit;
        $this->set('data',$data);
    }

}
