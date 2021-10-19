<?php

/**
 * Class Qudtunit
 * Qudt units model
 */
class Qudtunit extends AppModel
{

    public $hasAndBelongsToMany=['Qudtqkind'];
    public $belongsTo=['Unit'];
    public $hasOne=['Iec','Nerc','Unece','Wiki'];
}