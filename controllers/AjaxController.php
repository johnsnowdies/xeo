<?php

namespace app\controllers;

use app\models\objects\Project;
use app\models\User;
use app\models\Xml;
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


    public function actionParseposition()
    {
        // echo 'ok';
        $model = new Xml();
        $model->load(Yii::$app->request->post());
        // var_dump(Yii::$app->request->post());
        // var_dump($model->queries);
        // die('ok');
        $model->getXml();

        //$model->writeResultToCSC($model->siteUrlOrig . '_' . date('d-m-Y_H:i:s') . '.csv');
        return $this->render('xml-result', ['model' => $model]);
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

    public function actionDeleteUser($uid){
        $model = new User();
        return $this->render('result', ["result" => $model->deleteUser($uid)]);

    }

    public function actionAddUser($username,$password,$firstname,$lastname,$isadmin, $sendmail){
        $model = new User();
        return $this->render('result', ["result" => $model->addUser($username,$password,$firstname,$lastname,$isadmin, $sendmail)]);

    }

    public function actionChangeProjectUser($pid,$uid){
        $model = new Project();
        return $this->render('result', ["result" => $model->changeProjectUser($pid,$uid)]);
    }

    public function actionDeleteProject($pid){
        $model = new Project();
        return $this->render('result', ["result" => $model->deleteProject($pid)]);
    }
}