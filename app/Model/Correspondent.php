<?php

/**
 * Class Correspondent
 * Correspondent model
 */
class Correspondent extends AppModel
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
	 * General function to add a new correspondent
	 * @param array $data
	 * @return integer
	 * @throws
	 */
	public function add($data)
	{
		$model = 'Correspondent';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}