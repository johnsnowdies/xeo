<?php
$this->title = 'Запросы';
?>

<script src="http://code.angularjs.org/1.1.4/angular.min.js"></script>

<script type="text/javascript">
    function QueriesController($scope) {
        $scope.queries = <?= json_encode($queries)?>;

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
        <div class="panel panel-default">
            <div class="panel-heading">Администрирование</div>
            <div class="panel-body">
                <p><a href="#" class="btn btn-default">Редактировать разбивку</a></p>

                <p><a href="#" class="btn btn-default">Сменить оптимизатора</a></p>

                <p><a href="#" class="btn btn-danger">Удалить проект</a></p>
            </div>
        </div>
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

                <p>Последния апдейт: <strong><?= $info['update_date'] ?></strong></p>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Запросы проекта</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4">
                <form class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control" id="exampleInputName2" placeholder="06/03/2015">
                    </div>
                </form>
            </div>
            <div class="col-sm-6">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-primary">Все запросы</button>
                    <button type="button" class="btn btn-default">Топ 3</button>
                    <button type="button" class="btn btn-default">Топ 5</button>
                    <button type="button" class="btn btn-default">Топ 10</button>
                    <button type="button" class="btn btn-default">Топ 20</button>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-default">Добавить колонку</button>

                </div>
            </div>
        </div>
    </div>
    <div ng-init ng-controller="QueriesController">
        <table class="table table-striped  table-hover ">
            <thead>
            <tr>
                <th>Запрос</th>
                <th>URL</th>
                <th>Частотность</th>
                <th>6.03</th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="query in queries">
                <td>{{ query.text}}</td>
                <td>{{ query.url }}</td>
                <td>0.0</td>
                <td>5(-2)</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>