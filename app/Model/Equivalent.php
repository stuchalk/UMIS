<?php

/**
 * Class Equivalent
 * Equivalent model
 */
class Equivalent extends AppModel
{

    public $belongsTo=['Prefix','Constant',
        'To'=>[
            'className' => 'Unit',
            'foreignKey' => 'id',
            'targetForeignKey' => 'to_unit'
        ],
        'From'=>[
            'className' => 'Unit',
            'foreignKey' => 'id',
            'targetForeignKey' => 'from_unit'
        ]];

    /**
     * General function to add a new equivalent
     * @param array $data
     * @return integer
     * @throws
     */
    public function add($data)
    {
        $model = 'Equivalent';
        $this->create();
        $ret = $this->save([$model => $data]);
        $this->clear();
        return $ret[$model];
    }

}