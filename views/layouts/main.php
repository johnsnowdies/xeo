<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;
use app\models\User;
AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-app="xd42">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <style>
        th{
            vertical-align: top!important;
        }


    </style>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- TODO Все библиотеки разместить в проекте! -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <!-- TODO Все библиотеки разместить в проекте! -->

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="http://code.angularjs.org/1.1.4/angular.min.js"></script>

    <script src="/js/xd42.js"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div class="wrap">
        <?php
        if (!\Yii::$app->user->isGuest):?>

            <!-- Навигация -->
            <nav id="w0" class="navbar-inverse navbar-fixed-top navbar" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w0-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <a class="navbar-brand" href="/"><img height="24" src="/img/xd422.png"></a>
                    </div>

                    <div id="w0-collapse" class="collapse navbar-collapse">
                        <ul id="w1" class="navbar-nav navbar-right nav">
                            <li  class="dropdown">
                                <a href="#" class="dropdown-toggle white" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-user white" aria-hidden="true"></span>
                                    <?=User::getUsername(Yii::$app->user->getId())?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/logout" data-method="post">Выход</a></li>
                                </ul>

                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        <?php endif; ?>

        <div class="container">
            <?= $content ?>
        </div>
    </div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
