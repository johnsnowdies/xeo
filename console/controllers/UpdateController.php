<?php

namespace app\console\controllers;

use app\models\service\SParser;
use app\models\service\SUpdate;
use yii\console\Controller;

class UpdateController extends Controller {

    public function actionRun() {
        $model = new SUpdate();
        $model->checkUpdate();
    }
}