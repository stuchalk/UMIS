<?php

/**
 * Class DimensionsController
 */
class DimensionsController extends AppController
{
	
	/**
	 * beforeFilter function
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow();
	}


}

?>