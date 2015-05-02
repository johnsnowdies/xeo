<?php
namespace app\controllers;

use app\models\CronServices;
use app\models\objects\Project;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class ProjectsController  extends Controller{

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

    // Авторизация пользователя
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    // Выход из системы

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    // Общая страница проектов
    public function actionIndex()
    {
        $model = new Project();

        $userRole = User::getUserRole(Yii::$app->user->getId());

        $cron = new CronServices();
        $updateData = $cron->getCronState();
        $updateData['today'] = date( 'Y-m-d');


        return $this->render('index',["projects" => $model->getProjectsList(Yii::$app->user->getId()),"userRole" => $userRole,
        "newProjects" => $model->getNewProjectsList(Yii::$app->user->getId()),"updateData" => $updateData]);
    }

    // Детали проекта
    public function actionShow($pid = null){
        if (!$pid){
            return $this->goHome();
        }

        $userRole = User::getUserRole(Yii::$app->user->getId());
        $model = new Project();
        $info = $model->getProjectInfo($pid);
        $queries = $model->getProjectQueries($pid,Yii::$app->user->getId());
        $newQueries = $model->getProjectNewQueries($pid,Yii::$app->user->getId());

        $updates = $model->getUpdateDates($pid);

        return $this->render('queries',["pid"=>$pid,"info"=>$info,"queries" => $queries,"updates" => $updates,
            "userRole" => $userRole,"newQueries" => $newQueries]);
    }
}