<?php
namespace app\controllers;

use app\models\objects\Project;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class TestController  extends Controller{

    public function behaviors()
    {
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    // Проверка пользователя
    public function beforeAction($event){
        if (\Yii::$app->user->isGuest && \Yii::$app->controller->action->id != 'login') {
            return $this->redirect('/projects/login',302);
        }
        return parent::beforeAction($event);
    }

    // Общая страница проектов
    public function actionIndex()
    {
        $model = new Project();
        $model->createTest();
        return $this->render('index');
    }
}