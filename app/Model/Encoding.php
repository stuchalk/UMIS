<?php

/**
 * Class Encoding
 * String model
 */
class Encoding extends AppModel
{
    public $belongsTo = ['Strng'];

    public $hasAndBelongsToMany = ['Format'];
}