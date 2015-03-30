<?php
use app\models\User;

$this->title = 'Проекты';
?>
    <script type="text/javascript">

        function UpdateStatusController($scope){
            $scope.status = <?= json_encode($updateData)?>;
        }

        function ManageUsersController($scope, $http, $window) {
            $scope.toDelete = [];
            $scope.users = <?= json_encode(User::getUserList()) ?>;
            $scope.addNewUser = false;
            $scope.isAdmin = 0;
            $scope.isSendmail = 0;
            $scope.currentUser = <?= Yii::$app->user->getId()?>;

            $scope.genPassword = function () {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                for (var i = 0; i < 7; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                $scope.password = text;
            };

            $scope.addUser = function () {
                $('#userList').attr('class', 'col-sm-6');
                $('#addUser').fadeIn(0);
                $('#addUserBtn').toggle();
                $scope.addNewUser = true;
            };

            $scope.addUserCancel = function () {
                $('#addUser').fadeOut(0, function () {
                    $('#addUserBtn').toggle();
                    $('#userList').attr('class', 'col-sm-12');
                });

                $scope.addNewUser = false;
            };

            $scope.deleteUser = function ($index) {
                $scope.toDelete.push($scope.users[$index].id);
                $scope.users.splice($index, 1);
            };

            $scope.validToDelete = function (uid) {
                return !($scope.currentUser == uid)
            };

            $scope.saveUserChanges = function () {

                // Удаляем пользователей отмеченых "на удаление"
                function outputItem(uid, i, toDelete) {
                    $http.get('/ajax/delete-user?uid=' + uid).
                        success(function (data, status, headers, config) {

                        }).
                        error(function (data, status, headers, config) {
                            //TODO ошибка при удалении
                        });
                }

                $scope.toDelete.forEach(outputItem);


                // Добавляем нового пользователя

                if ($scope.admin)
                    $scope.isAdmin = 1;

                if ($scope.sendmail)
                    $scope.isSendmail = 1;


                if ($scope.addNewUser && $scope.name && $scope.password && $scope.firstname && $scope.lastname) {
                    $http.get('/ajax/add-user?username=' + $scope.name + '&password=' + $scope.password + '&firstname=' + $scope.firstname + '&lastname=' + $scope.lastname + '&isadmin=' + $scope.isAdmin + '&sendmail=' + $scope.isSendmail).
                        success(function (data, status, headers, config) {

                        }).
                        error(function (data, status, headers, config) {
                            //TODO ошибка при удалении
                        });
                }

                $window.location.reload();

            }

        }

        function AddProjectController($scope, $http, $window) {
            $scope.users = <?= json_encode(User::getUserList()) ?>;
            $scope.createProject = function () {
                // Валидация - ок
                if ($scope.name && $scope.user && $scope.newQueries && $scope.seoRegion) {
                    $http.post('/ajax/new-project', {
                        'name': $scope.name,
                        'user': $scope.user,
                        'newQueries': $scope.newQueries,
                        'region': $scope.seoRegion
                    }).
                        success(function (data, status, headers, config) {

                        }).
                        error(function (data, status, headers, config) {
                            //TODO ошибка при удаление
                        });
                    $window.location.reload();
                }
            }
        }

        function ProjectsController($scope) {
            $scope.projects = <?= json_encode($projects) ?>;
            $scope.newProjects = <?= json_encode($newProjects) ?>;
        }
    </script>

<? if ($userRole == 'A'): ?>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Администрирование</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="#" data-toggle="modal" data-target="#addProject"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;&nbsp;Добавить проект</a></li>
                    <li><a href="#" data-toggle="modal" data-target="#manageUsers">
                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;Управление пользователями</a></li>
                    <li><a href="#" data-toggle="modal" data-target="#updateStatus"><span class="glyphicon glyphicon-time "></span>&nbsp;&nbsp;Статус апдейта</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
<? endif; ?>

    <div ng-init ng-controller="ProjectsController">
        <? if (empty($projects) == 1 && empty($newProjects) == 1): ?>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-4">
                    <img src="/img/xd42.png"/><img src="/img/cat_laptop.png">

                    <h3><?= User::getUsername(Yii::$app->user->getId()) ?>, у Вас нет проектов!<br>
                        <small>Обратитесь, пожалуйста к администратору :(</small>
                    </h3>

                </div>
            </div>

        <? else: ?>
            <h1>Проекты</h1>
        <? endif; ?>

        <? if (empty($newProjects) == 0): ?>
            <!-- Новые проекты -->
            <div class="alert alert-info" role="alert">Позиции по новым проектам будут доступны после следующего
                апдейта
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Новые проекты</div>
                <table class="table table-striped  table-hover ">
                    <thead>
                    <tr>
                        <th>Проект</th>
                        <th>Количество <br/>запросов</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="newProject in newProjects">
                        <td><a href="/projects/show?pid={{newProject.id}}">{{ newProject.name }}</a></td>
                        <td>{{ newProject.queriesCnt }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <? endif; ?>

        <? if (empty($projects) == 0): ?>
            <!-- Старые проекты-->
            <div class="panel panel-default">
                <div class="panel-heading">Проекты на продвижении</div>

                <table class="table table-striped  table-hover ">
                    <thead>
                    <tr>
                        <th rowspan=2>Проект</th>
                        <th rowspan=2 class="col-md-1">Количество <br/>запросов</th>
                        <td colspan="4"><strong>В топе:</strong></td>
                        <th rowspan=2 class="col-md-1">Регион<br/>продвижения</th>
                        <th class=" col-md-1" rowspan=2>тИЦ</th>
                        <th class="col-md-1" rowspan=2>PR</th>
                        <th class=" col-md-1" rowspan=2>ЯК</th>
                        <th class="col-md-1" rowspan=2>DMOZ</th>
                    </tr>
                    <tr class="warning">
                        <th rowspan=2>3</th>
                        <th rowspan=2>5</th>
                        <th rowspan=2>10</th>
                        <th rowspan=2>20</th>
                    </tr>

                    </thead>

                    <tbody>
                    <tr ng-repeat="project in projects">
                        <td><a href="/projects/show?pid={{project.id}}">{{ project.name }}</a></td>
                        <td>{{ project.queriesCnt }}</td>

                        <td class="warning" ng-repeat="t in project.queriesTop">{{t}}</td>
                        <td>{{ project.region}}</td>
                        <td>{{ project.tic}}</td>
                        <td>{{ project.pr}}</td>
                        <td>
                            <span class="label label-success" ng-hide="!project.yc">Да</span>
                            <span class="label label-danger" ng-hide="project.yc">Нет</span>
                        </td>
                        <td>
                            <span class="label label-success" ng-hide="!project.dmoz">Да</span>
                            <span class="label label-danger" ng-hide="project.dmoz">Нет</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div><!-- panel -->
        <? endif; ?>

    </div>
<? if ($userRole == 'A'): ?>
    <!-- Admin modal windows -->

    <!-- Modal -->
    <div ng-init ng-controller="AddProjectController" class="modal fade" id="addProject" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;&nbsp;Добавить проект</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">Внимание! В качестве имении проекта указывается полный
                        URL продвигаемого сайта,
                        например http://www.site.ru/
                    </div>

                    <form name="newProject">
                        <div class="form-group">
                            <label for="inputName">Имя проекта
                                <span class="error" style="color:red;" ng-show="newProject.inputName.$error.required"> - поле обязательно</span>
                                <span class="error" style="color:red;" ng-show="newProject.inputName.$error.url"> - имя должно быть в формате URL</span>
                            </label>
                            <input type="url" required="" class="form-control" name="inputName" id="inputName"
                                   ng-model="name" placeholder="http://www.site.ru/">
                        </div>

                        <div class="form-group">
                            <label for="inputUser">Оптимизатор
                                <span class="error" style="color:red;" ng-show="newProject.user.$error.required"> - поле обязательно</span>
                            </label>

                            <select class="form-control" required="" name="user" ng-model="user">
                                <option ng-repeat="user in users" value="{{user.id}}">
                                    {{user.firstname}}&nbsp;{{user.lastname}}
                                    &lt;{{user.username}}&gt;
                                </option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="region">Регион продвижения
                                <span class="error" style="color:red;" ng-show="newProject.region.$error.required"> - поле обязательно</span>
                            </label>
                            <input type="text" required="" class="form-control" name="region" id="region"
                                   ng-model="seoRegion" placeholder="213">


                        </div>

                        <h4>Стартовые запросы
                            <small>&nbsp;запрос на строку<span class="error" style="color:red;"
                                                               ng-show="newProject.newQueries.$error.required"> - поле обязательно</span>
                            </small>
                        </h4>
                        <textarea class="form-control" name="newQueries" required="" ng-model="newQueries"
                                  rows="3"></textarea>
                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" ng-click="createProject()">Сохранить изменения
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div ng-init ng-controller="ManageUsersController" class="modal fade" id="manageUsers" tabindex="-1" role="dialog"
         aria-labelledby="manageUsersLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-user"
                                                                    aria-hidden="true"></span> Управление пользователями
                    </h4>
                </div>

                <div class="modal-body" style="padding:0 15px;">
                    <div class="row">
                        <div class="col-sm-12" id="userList"
                             style="border-right: 1px solid #ccc;min-height: 456px; padding:0 15px;">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Пользователь</th>
                                    <th>Действие</th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="user in users">
                                    <td> {{user.firstname}}&nbsp;{{user.lastname}}&lt;{{user.username}}&gt;</td>
                                    <td>
                                        <button ng-show="validToDelete({{user.id}})" type="button"
                                                ng-click="deleteUser($index)" class="btn btn-danger">
                                            <span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>&nbsp;&nbsp;
                                            Удалить
                                        </button>
                                    </td>
                                </tr>
                                </tbody>

                            </table>
                            <button class="btn btn-success" id="addUserBtn" ng-click="addUser()"><span
                                    class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>&nbsp;&nbsp;Добавить
                                пользователя
                            </button>
                        </div>
                        <div class="col-sm-6" id="addUser" style="display: none;">
                            <h3>Новый пользователь</h3>

                            <form name="newUser" novalidate>
                                <div class="form-group">
                                    <label for="inputName">Логин
                                        <span class="error" style="color:red;"
                                              ng-show="newUser.inputLogin.$dirty && newUser.inputLogin.$error.required"> - поле обязательно</span>
                                        <span class="error" style="color:red;"
                                              ng-show="newUser.inputLogin.$dirty && newUser.inputLogin.$error.email"> - логин должен быть в формате Email</span>
                                    </label>
                                    <input type="email" required="" class="form-control" name="inputLogin"
                                           id="inputLogin"
                                           ng-model="name" placeholder="user@site.ru">
                                </div>

                                <div class="form-group">
                                    <label for="inputName">Пароль
                                        <span class="error" style="color:red;"
                                              ng-show="newUser.inputPassword.$dirty && newUser.inputPassword.$error.required"> - поле обязательно</span>
                                    </label>

                                    <div class="input-group">

                                        <input type="text" required="" class="form-control" name="inputPassword"
                                               id="inputPassword" ng-model="password" placeholder="">
                                        <span style="cursor: pointer" ng-click="genPassword()"
                                              class="input-group-addon"><span class="glyphicon glyphicon-random"
                                                                              aria-hidden="true"></span></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputName">Имя
                                        <span class="error" style="color:red;"
                                              ng-show="newUser.inputFirstname.$dirty && newUser.inputFirstname.$error.required"> - поле обязательно</span>
                                    </label>
                                    <input type="text" required="" class="form-control" name="inputFirstname"
                                           id="inputFirstname"
                                           ng-model="firstname" placeholder="Иван">
                                </div>

                                <div class="form-group">
                                    <label for="inputName">Фамилия
                                        <span class="error" style="color:red;"
                                              ng-show="newUser.inputLastname.$dirty && newUser.inputLastname.$error.required"> - поле обязательно</span>

                                    </label>
                                    <input type="text" required="" class="form-control" name="inputLastname"
                                           id="inputLastname"
                                           ng-model="lastname" placeholder="Иванов">
                                </div>

                                <div class="checkbox">
                                    <label>
                                        <input ng-model="admin" type="checkbox" value="">
                                        Администратор
                                    </label>
                                </div>

                                <div class="checkbox">
                                    <label>
                                        <input disabled ng-model="sendmail" type="checkbox" value="">
                                        Отправить новому пользователю письмо с доступами
                                    </label>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-warning" id="addUserCancelBtn" ng-click="addUserCancel()">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;&nbsp;Отмена
                                    </button>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" ng-click="saveUserChanges()">Сохранить изменения
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div ng-init ng-controller="UpdateStatusController" class="modal fade" id="updateStatus" tabindex="-1"
         role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-time "></span>&nbsp;&nbsp;Статус апдейта</h4>
                </div>
                <div class="modal-body">
                    <p>Последний апдейт: <strong>{{status.last_update_date}}</strong></p>
                    <p>Последняя проверка: <strong>{{status.last_check_date}}</strong></p>

                    <!--<p>Апдейт по seopult:
                        <span ng-show="{{status.update_seopult}}"><span class="label label-success">Да</span></span>
                        <span ng-hide="{{status.update_seopult}}"><span class="label label-danger">Нет</span></span>
                    </p>

                    <p>Апдейт по promosite:
                        <span ng-show="{{status.update_promosite}}"><span class="label label-success">Да</span></span>
                        <span ng-hide="{{status.update_promosite}}"><span class="label label-danger">Нет</span></span>
                    </p>
-->
                    <p>Сейчас происходит апдейт:
                        <span ng-show="{{status.update_runing}}"><span class="label label-success">Да</span></span>
                        <span ng-hide="{{status.update_runing}}"><span class="label label-danger">Нет</span></span>
                    </p>

                </div>
            </div>
        </div>
    </div>
<? endif; ?>