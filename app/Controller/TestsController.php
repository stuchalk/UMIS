<?php

/**
 * Class TestsController
 */
class TestsController extends AppController
{
    public $uses=['Mixf','Ivoa','Ucum'];

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('convert');
    }

    public function convert()
    {

        $unit="Meter squared";
        $mixf=$this->Mixf->makesymbol($unit);
        debug($mixf);
        $ivoa=$this->Ivoa->makesymbol($unit);
        debug($ivoa);
        $ucum=$this->Ucum->makesymbol($unit);
        debug($ucum);exit;

    }

}

