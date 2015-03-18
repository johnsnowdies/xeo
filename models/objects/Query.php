<?php
namespace app\models\objects;

class Query {
    public $text;
    public $url;
    public $position = [];
    public $frequency;
    public $diff = null;
    public $top;
}