<?php

/**
 * Class Dimension
 * Dimension model
 */
class Dimension extends AppModel
{
	
	public $belongsTo=['Quantitysystem'];
	
	/**
	 * General function to add a new Dimension
	 * @param array $data
	 * @return integer
	 * @throws
	 */
	public function add($data)
	{
		$model = 'Dimension';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}