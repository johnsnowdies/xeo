<?php

namespace app\controllers;

use app\models\objects\Project;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;


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

    public function actionIndex(){
        echo "ss";
    }

    public function actionUpdate($pid,$date)
    {
        $model = new Project();
        $queries = $model->getProjectQueries($pid,Yii::$app->user->getId(),$date);
        return $this->render('result',["queries"=>$queries]);
    }
}