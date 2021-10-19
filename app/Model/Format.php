<?php

/**
 * Class Format
 * Format model
 */
class Format extends AppModel
{
    public $hasAndBelongsToMany = ['Encoding'];
}