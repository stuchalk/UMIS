<?php

/**
 * Class Quantitykind
 * Quantitykind model
 */
class Quantitykind extends AppModel
{
	
	public $belongsTo=['Dimensionvector','Quantitysystem',
        'BaseUnit'=>[
            'className' => 'Unit',
            'foreignKey' => 'baseunit_id'
        ]
    ];
	
	public $hasMany=['Quantity','Nym','Definition','Unit'];

	/**
	 * General function to add a new Unit
	 * @param array $data
	 * @return integer
     * @throws
	 */
	public function add($data)
	{
		$model = 'Quantitykind';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}