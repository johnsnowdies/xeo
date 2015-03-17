<?php
$this->title = 'Запросы';
?>

<script src="http://code.angularjs.org/1.1.4/angular.min.js"></script>


<script type="text/javascript">
    function QueriesController($scope, $http) {

        $scope.pid = <?= $pid ?>;
        $scope.queries = <?= json_encode($queries)?>;
        $scope.updates = <?= json_encode($updates)?>;
        $scope.currentUpdate = $scope.updates[0];

        $scope.loadUpdate = function(){

            // Simple GET request example :
            $http.get('/ajax/update?pid='+$scope.pid+'&date=' + $scope.currentUpdate).
                success(function(data, status, headers, config) {
                    $scope.queries = data;
                }).
                error(function(data, status, headers, config) {

                });

        };

        $scope.selected = 'all';
        $scope.changeName = function(index) {
            $scope.selected = index;
        };

        $scope.isShown = function(top) {
            if ($scope.selected == 'all') {
                return true;
            }
            return parseInt($scope.selected,10) >= top;
        }
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("body").tooltip({selector: '[data-toggle=tooltip]'});
    });
</script>

<div class="row">
    <div class="col-sm-4">
        <a href="<?= $info['name'] ?>"><h1><?= $info['name'] ?></h1></a>

        <p><a href="/" class="btn btn-default"> < Список проектов </a></p>
    </div>


    <div class="col-sm-4">
        <?if ($userRole == 'A'):?>
        <div class="panel panel-default">
            <div class="panel-heading">Администрирование</div>
            <div class="panel-body">
                <p><a href="#" class="btn btn-default" data-toggle="modal" data-target="#myModal">Редактировать разбивку</a></p>
                <p><a href="#" class="btn btn-default">Сменить оптимизатора</a></p>
                <p><a href="#" class="btn btn-danger">Удалить проект</a></p>
            </div>
        </div>
        <?endif;?>
    </div>

    <div class="col-sm-4">
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

<div class="panel panel-default">
    <div class="panel-heading">Запросы проекта</div>
    <div ng-init ng-controller="QueriesController">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4">
                <form class="form-inline">
                    <div class="form-group">
                        Апдейт:
                        <select class="selectpicker" ng-click="loadUpdate()" ng-model="currentUpdate">
                            <option ng-repeat="update in updates">{{update}}</option>

                        </select>

                    </div>
                </form>
            </div>
            <div class="col-sm-6">
                <div class="btn-group" role="group"  aria-label="...">
                    <button type="button" ng-click="changeName('all')" class="btn btn-default" ng-class="{'btn-primary': selected == 'all'}" >Все запросы</button>
                    <button type="button" ng-click="changeName('3')"  class="btn btn-default" ng-class="{'btn-primary': selected == '3'}" >Топ 3</button>
                    <button type="button" ng-click="changeName('5')"  class="btn btn-default" ng-class="{'btn-primary': selected == '5'}" >Топ 5</button>
                    <button type="button" ng-click="changeName('10')" class="btn btn-default" ng-class="{'btn-primary': selected == '10'}" >Топ 10</button>
                    <button type="button" ng-click="changeName('20')" class="btn btn-default" ng-class="{'btn-primary': selected == '20'}" >Топ 20</button>
                </div>
            </div>
            <!--<div class="col-sm-2">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-default">Добавить колонку</button>

                </div>
            </div>-->
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
                    <span class="label label-success" ng-hide="query.diff <= 0"> {{ query.diff }}</span>
                    <span class="label label-danger"  ng-hide="query.diff > 0"> {{ query.diff }}</span>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>


