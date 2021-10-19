<?php

/**
 * Class Nym
 * Nym model
 */
class Nym extends AppModel
{
	
	public $belongsTo=['Domain'];
	
	/**
	 * General function to add a new nym
	 * @param array $data
	 * @return integer
	 * @throws
	 */
	public function add($data)
	{
		$model = 'Nym';
		$this->create();
		$ret = $this->save([$model => $data]);
		$this->clear();
		return $ret[$model];
	}
	
}