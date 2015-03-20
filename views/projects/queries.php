<?php
$this->title = 'Запросы';
?>
    <style>
        .modal-pane {
            max-height: 200px;
            overflow: scroll;
            overflow-x: hidden;
        }
    </style>

    <script type="text/javascript">
        function UpdateQueriesController($scope, $http, $window) {
            $scope.queries = <?= json_encode($queries)?>;
            $scope.pid = <?= $pid ?>;
            $scope.toDelete = [];


            $scope.deleteQuery = function ($index) {
                $scope.toDelete.push($scope.queries[$index].id);
                $scope.queries.splice($index, 1);
            };

            $scope.saveChanges = function () {
                // Удаляем запросы отмечены "на удалание"

                function outputItem(qid, i, toDelete) {
                    $http.get('/ajax/delete-query?qid=' + qid).
                        success(function (data, status, headers, config) {

                        }).
                        error(function (data, status, headers, config) {
                            //TODO ошибка при удаление
                        });
                }

                $scope.toDelete.forEach(outputItem);

                // Добавляем новые запросы в разбивку
                if ($scope.newQueries) {
                    $http.post('/ajax/add-new-queries', {'queriesList': $scope.newQueries, 'pid': $scope.pid}).
                        success(function (data, status, headers, config) {
                            // this callback will be called asynchronously
                            // when the response is available
                        }).
                        error(function (data, status, headers, config) {
                            //TODO ошибка при добавлении новых запросов
                        });
                }
                $scope.newQueries = "";
                $scope.toDelete = [];
                $window.location.reload();
            };
        }


        function QueriesController($scope, $http) {
            $scope.pid = <?= $pid ?>;
            $scope.queries = <?= json_encode($queries)?>;
            $scope.newQueries = <?= json_encode($newQueries)?>;
            $scope.updates = <?= json_encode($updates)?>;
            $scope.currentUpdate = $scope.updates[0];

            $scope.loadUpdate = function () {
                $http.get('/ajax/get-queries-for-period?pid=' + $scope.pid + '&date=' + $scope.currentUpdate).
                    success(function (data, status, headers, config) {
                        $scope.queries = data;
                    }).
                    error(function (data, status, headers, config) {

                    });
            };

            $scope.selected = 'all';
            $scope.changeName = function (index) {
                $scope.selected = index;
            };

            $scope.isShown = function (top) {
                if ($scope.selected == 'all') {
                    return true;
                }
                return (parseInt($scope.selected, 10) >= top && top != '') ;
            }
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
                    <li><a href="#" data-toggle="modal" data-target="#editUrls">Редактировать разбивку</a></li>
                    <li><a href="#">Сменить оптимизатора</a></li>
                    <li><a href="#">Удалить проект</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
<? endif; ?>

    <div class="row">
        <div class="col-sm-4">
            <a href="<?= $info['name'] ?>"><h3><?= $info['name'] ?></h3></a>

            <p><a href="/" class="btn btn-default"> < Список проектов </a></p>
        </div>


    </div>
    <div ng-init ng-controller="QueriesController">
        <? if (!empty($queries)):?>
        <div class="panel panel-default">
            <div class="panel-heading">Запросы проекта</div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <form class="form-inline">
                            <div class="form-group">
                                Апдейт:
                                <select class="form-control selectpicker" ng-click="loadUpdate()" ng-model="currentUpdate">
                                    <option ng-repeat="update in updates">{{update}}</option>

                                </select>

                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6">
                        <div class="btn-group" role="group" aria-label="...">
                            <button type="button" ng-click="changeName('all')" class="btn btn-default"
                                    ng-class="{'btn-primary': selected == 'all'}">Все запросы
                            </button>
                            <button type="button" ng-click="changeName('3')" class="btn btn-default"
                                    ng-class="{'btn-primary': selected == '3'}">Топ 3
                            </button>
                            <button type="button" ng-click="changeName('5')" class="btn btn-default"
                                    ng-class="{'btn-primary': selected == '5'}">Топ 5
                            </button>
                            <button type="button" ng-click="changeName('10')" class="btn btn-default"
                                    ng-class="{'btn-primary': selected == '10'}">Топ 10
                            </button>
                            <button type="button" ng-click="changeName('20')" class="btn btn-default"
                                    ng-class="{'btn-primary': selected == '20'}">Топ 20
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-hover ">
                <thead>
                <tr>
                    <th>Запрос</th>
                    <th>URL</th>
                    <th>Частотность</th>
                    <th ng-repeat="pos in queries[0].position">
                    <span ng-repeat="(key,value) in pos">
                        {{key}}
                    </span>
                    </th>
                    <th>+/-</th>

                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="query in queries" ng-show="isShown('{{query.top}}')">
                    <td>{{ query.text}}</td>
                    <td>{{ query.url }}</td>
                    <td>{{ query.frequency }}</td>
                    <td ng-repeat="pos in query.position">
                    <span ng-repeat="(key,value) in pos">
                        {{value}}
                    </span>
                    </td>
                    <td>
                    <span ng-hide="query.diff == null">
                        <span class="label label-success" ng-hide="query.diff <= 0"> {{ query.diff }}</span>
                        <span class="label label-danger" ng-hide="query.diff > 0"> {{ query.diff }}</span>
                    </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?else:?>
            <div class="alert alert-warning" role="alert">Позиций по запросам еще нет, возможно это новый проект?</div>
        <?endif;?>


        <div class="row">
            <? if (!empty($newQueries)):?>
            <div class="col-md-4">
                <div class="alert alert-info" role="alert">Запросы добавленные после последнего апдейта будут посчитаны в следующий</div>
                <div class="panel panel-default">
                    <div class="panel-heading">Новые запросы</div>

                    <table class="table table-hover ">
                        <thead>
                        <tr>
                            <th>Запрос</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="newQuery in newQueries">
                            <td>{{ newQuery.text}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <? endif;?>

            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Информация по проекту</div>
                    <div class="panel-body">
                        <p>Оптимизатор: <strong data-toggle="tooltip" data-placement="right"
                                                title="<?= $info['email'] ?>"><?= $info['firstname'] ?>
                                &nbsp;<?= $info['lastname'] ?></strong></p>

                        <p>Регион продвижения: <strong><?= $info['reg_name'] ?></strong></p>

                        <p>Старт проекта: <strong><?= $info['start_date'] ?></strong></p>

                    </div>
                </div>
            </div>

        </div>


    </div>

<? if ($userRole == 'A'): ?>
    <!-- Admin modal windows -->

    <!-- Modal -->
    <div ng-init ng-controller="UpdateQueriesController" class="modal fade" id="editUrls" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Редактировать разбивку</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">Внимание! При удалении запросов из разбивки будет
                        удалена история апдейтов по этим запросам!
                    </div>
                    <h4>Текущие запросы</h4>

                    <div class="modal-pane">
                        <table class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Запрос</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="query in queries"
                            ">
                            <td>{{ query.text}}</td>
                            <td>
                                <button type="button" ng-click="deleteQuery($index)" class="btn btn-danger">Удалить
                                </button>
                            </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4>Добавть запросы
                        <small>&nbsp;запрос на строку</small>
                    </h4>
                    <textarea class="form-control" ng-model="newQueries" rows="3"></textarea>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" ng-click="saveChanges()">Сохранить изменения</button>
                </div>
            </div>
        </div>
    </div>
<? endif; ?>