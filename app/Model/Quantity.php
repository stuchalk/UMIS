<?php

/**
 * Class Quantity
 * Quantity model
 */
class Quantity extends AppModel
{

    public $belongsTo=['Quantitykind','Quantitysystem','Domain'];

    public $hasMany=['Nym'];

    /**
     * General function to add a new Quantity
     * @param array $data
     * @return integer
     * @throws
     */
    public function add($data)
    {
        $model = 'Quantity';
        $this->create();
        $ret = $this->save([$model => $data]);
        $this->clear();
        return $ret[$model];
    }

}