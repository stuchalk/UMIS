<?php

/**
 * Class Representation
 * Representation model
 */
class Representation extends AppModel
{

    public $hasAndBelongsToMany=['Usecase'];
	public $belongsTo=['Unitsystem','Repsystem','Unit','Strng'];
	
	/**
	 * General function to add a new Quantitysystem
	 * @param array $data
	 * @return integer
     * @throws
	 */
	public function add($data)
	{
		$model = 'Representation';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}