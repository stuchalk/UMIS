<?php

/**
 * Class Prefix
 * Prefix model
 */
class Prefix extends AppModel
{
	
	public $belongsTo=['Unitsystem'];
	
	public $hasMany=['Unit'];
	
	public $virtualFields=['title'=>"concat(Prefix.name,' (',Prefix.value,')')"];
	
	/**
	 * General function to add a new prefix
	 * @param array $data
	 * @return integer
	 * @throws
	 */
	public function add($data)
	{
		$model = 'Prefix';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}