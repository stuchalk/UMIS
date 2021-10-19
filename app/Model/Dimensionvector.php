<?php

/**
 * Class Dimensionvector
 * Dimensionvector model
 */
class Dimensionvector extends AppModel
{
	
	public $hasMany=['Quantitykind'];
	
	public $belongsTo=['Quantitysystem'];
	
	/**
	 * General function to add a new Dimensionvector
	 * @param array $data
	 * @return integer
     * @throws
	 */
	public function add($data)
	{
		$model = 'Dimensionvector';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}