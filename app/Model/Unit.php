<?php

/**
 * Class Unit
 * Unit model
 */
class Unit extends AppModel
{
	public $hasMany=['Representation','Nym',
        'FromEUnit'=>[
            'className' => 'Equivalent',
            'foreignKey' => 'from_unit',
            'targetForeignKey' => 'id'
        ],
        'ToEUnit'=>[
            'className' => 'Equivalent',
            'foreignKey' => 'to_unit',
            'targetForeignKey' => 'id'
        ],
		'FromCUnit'=>[
			'className' => 'Correspondent',
			'foreignKey' => 'from_unit',
			'targetForeignKey' => 'id'
		],
		'ToCUnit'=>[
			'className' => 'Correspondent',
			'foreignKey' => 'to_unit',
			'targetForeignKey' => 'id'
		]];
	
	public $belongsTo=['Quantitykind','Unitsystem','Prefix','Factor'];
	
	/**
	 * General function to add a new Unit
	 * @param array $data
	 * @return integer
	 * @throws
	 */
	public function add($data)
	{
		$model = 'Unit';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}