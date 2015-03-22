<?php
namespace app\models\objects;

class Query {
    public $text;
    public $url;
    public $url_old;
    public $position = [];
    public $frequency;
    public $diff = null;
    public $top;
    public $rel_change = false;
}