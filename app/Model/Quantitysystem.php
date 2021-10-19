<?php

/**
 * Class Quantitysystem
 * Quantitysystem model
 */
class Quantitysystem extends AppModel
{
	
	public $hasMany=['Dimension','Unitsystem'];

	public $virtualFields=['title'=>"CONCAT(Quantitysystem.name,' (',Quantitysystem.abbrev,')')"];

	/**
	 * General function to add a new Quantitysystem
	 * @param array $data
	 * @return integer
     * @throws
	 */
	public function add($data)
	{
		$model = 'Quantitysystem';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}