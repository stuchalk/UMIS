<?php

/**
 * Class Unitsystem
 * Unitsystem model
 */
class Unitsystem extends AppModel
{

    public $hasMany=['Unit'];

    public $belongsTo=['Quantitysystem'];

    public $virtualFields=['title'=>"CONCAT(Unitsystem.name,' (',Unitsystem.abbrev,')')"];

    /**
     * General function to add a new Unitsystem
     * @param array $data
     * @return integer
     * @throws
     */
    public function add($data)
    {
        $model = 'Unitsystem';
        $this->create();
        $ret = $this->save([$model => $data]);
        $this->clear();
        return $ret[$model];
    }

}