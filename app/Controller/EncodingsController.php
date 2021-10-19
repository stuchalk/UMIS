<?php
/**
 * Class EncodingsController
 */
class EncodingsController extends AppController
{

    public $uses = ['Encoding','Strng'];

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    /**
     * Encodings index
     * @return mixed
     */
    public function index()
    {
        $f=['Encoding.id','Encoding.string'];
        $c=['Strng'];$o=['Encoding.string'];
        $data=$this->Encoding->find('list',['fields'=>$f,'contain'=>$c,'order'=>$o]);
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
            $rstr=$this->Encoding->find('first',['conditions'=>['string'=>$id]]);
            $id=$rstr['Strng']['strng_id'];
        }

        $c=['Encoding'=>['Format']];
        $data=$this->Strng->find('first',['conditions'=>['Strng.id'=>$id],'contain'=>$c,'recursive'=>-1]);
        //debug($data);exit;
        $this->set('data',$data);
    }

}
