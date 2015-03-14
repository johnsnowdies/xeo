<?php
$this->title = 'Проекты';
?>
<h1>Проекты</h1>
<script src="http://code.angularjs.org/1.1.4/angular.min.js"></script>
<script type="text/javascript">
    function ProjectsController($scope) {
        $scope.projects = <?= json_encode($projects)?>;

    }
</script>

<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">Проекты на продвижении</div>
    <div class="panel-body">

<div ng-init ng-controller="ProjectsController">
    <table class="table table-striped  table-hover " >
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
            <td >{{ project.region}}</td>
            <td >{{ project.tic}}</td>
            <td>{{ project.pr}}</td>
            <td >
                <span class="label label-success" ng-hide="!project.yc">Да</span>
                <span class="label label-danger"ng-hide="project.yc">Нет</span>
            </td>
            <td>
                <span class="label label-success" ng-hide="!project.dmoz">Да</span>
                <span class="label label-danger"ng-hide="project.dmoz">Нет</span>
            </td>
        </tr>
        </tbody>


        </table>
</div>

</div><!-- panel-body -->
    </div><!-- panel -->
