<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'XD42';
?>

<div class="row">
    <div class="col-lg-offset-4 col-lg-4">
        <div class="row  text-center">
            <img src="/img/xd42.png"/>
            <p>&nbsp;</p>
        </div>
        <div class="panel panel-default">
            <div class="panel-body" style="padding: 40px">
                <div class="row  text-center">
                    <h1>Login</h1>
                </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]); ?>

                <?php $username = $form->field($model, 'username', ['template' => '<div class="col-lg-12 input-group">{input}<div class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div></div><div class="col-lg-offset-1 col-lg-8">{error}</div>'])->textInput(['placeholder' => 'Имя пользователя']) ?>
                <?php $password = $form->field($model, 'password', ['template' => '<div class="col-lg-12 input-group">{input}<div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div></div><div class="col-lg-offset-1 col-lg-8">{error}</div>'])->passwordInput(['placeholder' => 'Пароль']) ?>

                <?= $username->label('Логин') ?>
                <?= $password->label('Пароль') ?>

                <div class="form-group">
                    <label class="col-lg-1   control-label"></label>
                    <div class="col-lg-11 input-group">
                        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary ', 'name' => 'login-button']) ?>
                        &nbsp;
                        <input type="hidden" name="LoginForm[rememberMe]" value="0">
                        <input type="checkbox"
                               id="loginform-rememberme"
                               name="LoginForm[rememberMe]"
                               value="1" checked="">
                        Запомнить этот компьютер
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


