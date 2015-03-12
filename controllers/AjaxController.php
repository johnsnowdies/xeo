<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Xml;

class AjaxController extends Controller
{
    public function behaviors()
    {
        $this->layout = 'ajax';
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
         
        ];
    }


    public function actionParseposition()
    {
       // echo 'ok';
        $model = new Xml();
        $model->load(Yii::$app->request->post());
        // var_dump(Yii::$app->request->post());
        // var_dump($model->queries);
        // die('ok');
        $model->getXml();

        $model->writeResultToCSC($model->siteUrlOrig . '_' . date('d-m-Y_H:i:s') . '.csv');
        return $this->render('xml-result', ['model' => $model]);
    }
}
