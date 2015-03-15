<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 13.03.2015
 * Time: 13:18
 */

namespace app\models\objects;


use Yii;

class Project
{
    public $id;
    public $name;
    public $oid;
    public $queries;
    public $queriesTop;
    public $queriesCnt;
    public $region;
    public $tic;
    public $pr;
    public $yc;
    public $dmoz;
    public $startDate;
    public $updateDate;


    /**
     * Получить список проектов для оптимизатора
     * @param $oid
     */
    public function getProjectsList($oid)
    {
        $projectList = [];

        $selectQuery = "SELECT p.id, p.name,p.oid,p.queries_top, p.tic,p.pr,p.yc,p.dmoz,p.start_date,p.update_date,
            r.title as reg_name, COUNT(q.id) as pqueries_cnt
            FROM projects p
            LEFT JOIN geo_regions r ON p.region = r.id
            LEFT JOIN queries q ON p.id = q.pid
            GROUP BY p.id HAVING p.oid = :oid;";

        $command = Yii::$app->db->createCommand($selectQuery);
        $command->bindParam(":oid", $oid);

        $dataReader = $command->query();

        while (($row = $dataReader->read()) !== false) {
            $rowProject = new Project();
            $rowProject->id = $row['id'];
            $rowProject->name = $row['name'];
            $rowProject->oid = $row['oid'];
            $rowProject->queriesTop = unserialize($row['queries_top']);
            $rowProject->queriesCnt = $row['pqueries_cnt'];
            $rowProject->region = $row['reg_name'];
            $rowProject->tic = $row['tic'];
            $rowProject->pr = $row['pr'];
            $row['yc'] == 1 ? $rowProject->yc = true : $rowProject->yc = false;
            $row['dmoz'] == 1 ? $rowProject->dmoz = true : $rowProject->dmoz = false;
            $rowProject->startDate = $row['start_date'];
            $rowProject->updateDate = $row['update_date'];

            $projectList[] = $rowProject;
        }

        return $projectList;
    }

    /* Получить запросы проекта
     * */
    public function getProjectQueries($pid,$oid){
        $queriesList = [];
        $selectQuery = "SELECT * FROM queries q
        LEFT JOIN projects p ON p.id = q.pid
        WHERE q.pid = :pid AND p.oid = :oid";

        $command = Yii::$app->db->createCommand($selectQuery);
        $command->bindParam(":oid", $oid);
        $command->bindParam(":pid", $pid);

        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $rowQuery = new Query();
            $rowQuery->text = $row['text'];
            $rowQuery->url = $row['url'];
            $queriesList[] = $rowQuery;

        }

        return $queriesList;
    }

    public function getProjectInfo($pid){
        $selectQuery = "SELECT p.name, p.update_date, p.start_date,
        u.username as email,
        u.firstname,
        u.lastname,
        r.title as reg_name
        FROM projects p
        LEFT JOIN users u ON u.id = p.oid
        LEFT JOIN geo_regions r ON r.id = p.region
        WHERE p.id = :pid";
        $command = Yii::$app->db->createCommand($selectQuery);
        $command->bindParam(":pid", $pid);
        $dataReader = $command->query();

        $projectInfo = [];
        while (($row = $dataReader->read()) !== false) {
            $projectInfo['name'] = $row['name'];
            $projectInfo['update_date'] = $row['update_date'];
            $projectInfo['start_date'] = $row['start_date'];
            $projectInfo['email'] = $row['email'];
            $projectInfo['firstname'] = $row['firstname'];
            $projectInfo['lastname'] = $row['lastname'];
            $projectInfo['reg_name'] = $row['reg_name'];
        }

        return $projectInfo;
    }

    public function createTest()
    {
        for ($prj = 0; $prj < rand(10, 15); $prj++) {
            $createProjectQuery = "INSERT INTO projects(name,oid,queries_top,region,tic,pr,yc,dmoz,start_date,update_dat
            VALUES(:name,:oid,:queries_top,:region,:tic,:pr,:yc,:dmoz,NOW(), NOW());";

            $cmdProject = Yii::$app->db->createCommand($createProjectQuery);

            $name = "http://www.site-$pr.ru";
            $oid = 1;
            $queries_top = serialize([rand(1, 3), rand(3, 5), rand(5, 10), rand(10, 20)]);
            $region = 213;
            $tic = rand(10, 200);
            $pr = rand(0, 10);
            $yc = rand(0, 1);
            $dmoz = rand(0, 1);

            $cmdProject->bindParam(":name", $name);
            $cmdProject->bindParam(":oid", $oid);
            $cmdProject->bindParam(":queries_top", $queries_top);
            $cmdProject->bindParam(":region", $region);
            $cmdProject->bindParam(":tic", $tic);
            $cmdProject->bindParam(":pr", $pr);
            $cmdProject->bindParam(":yc", $yc);
            $cmdProject->bindParam(":dmoz", $dmoz);

            $dataReader = $cmdProject->execute();
            $pid = Yii::$app->db->getLastInsertID();

            for ($q = 0; $q < rand(30, 50); $q++) {
                $text = "Тестовый запрос $q проекта $prj";
                $url = "http://www.site-$prj.ru/$q/";
                $createQuery = "INSERT INTO queries(pid,text,url) VALUES(:pid,:text,:url);";
                $cmdQuery = Yii::$app->db->createCommand($createQuery);
                $cmdQuery->bindParam(":pid", $pid);
                $cmdQuery->bindParam(":text", $text);
                $cmdQuery->bindParam(":url", $url);
                $cmdQuery->execute();
            }


        }
    }
}