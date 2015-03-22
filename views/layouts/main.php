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
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable = no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-startup-image" href="/img/xd42.png">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="/img/xd422.png">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
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
                            <li><a href="/site/index">Выполнить запрос</a></li>
                            <li><a href="/site/history">История запросов</a></li>
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

        <div class="container" ng-cloak>
            <?= $content ?>
        </div>
    </div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
