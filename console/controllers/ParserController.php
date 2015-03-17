<?php

namespace app\console\controllers;

use app\models\service\SParser;
use yii\console\Controller;

class ParserController extends Controller {

    public function actionRun() {
        print "Parser started\r\n";
        $parser = new SParser();
        $parser->run();
    }
}