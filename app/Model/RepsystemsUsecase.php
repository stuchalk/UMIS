<?php

/**
 * Class Repsystem
 * Repsystem model
 */
class RepsystemsUsecase extends AppModel
{

    public $belongsTo=['Repsystem','Usecase','Unit','Representation'];

}