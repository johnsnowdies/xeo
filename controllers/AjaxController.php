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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        echo "ss";
    }

    public function actionGetQueriesForPeriod($pid, $date)
    {
        $model = new Project();
        $queries = $model->getProjectQueries($pid, Yii::$app->user->getId(), $date);
        return $this->render('result', ["result" => $queries]);
    }

    public function actionDeleteQuery($qid)
    {
        $model = new Project();
        return $this->render('result', ["result" => $model->deleteQuery($qid)]);
    }

    public function actionAddNewQueries()
    {
        $postData = Yii::$app->request->post();
        $pid = $postData['pid'];
        $queriesList = explode("\n", $postData['queriesList']);
        $model = new Project();
        return $this->render('result', ["result" => $model->addQueries($queriesList, $pid)]);
    }

    public function actionNewProject(){
        $model = new Project();
        $postData = Yii::$app->request->post();
        $name = $postData['name'];
        $user = $postData['user'];
        $queries = $postData['newQueries'];
        $region = $postData['region'];



        return $this->render('result', ["result" => $model->createProjcet($name,$user,$queries,$region)]);
    }
}