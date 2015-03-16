<?php
namespace app\models\objects;


use Yii;
use app\models\User;

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

        $users = new User();
        // Разграничение пользователей
        $permitions = "";
        if ($users->getUserRole($oid) == 'U'){
            $permitions = "HAVING p.oid = {$oid}";
        }

        $selectQuery = "SELECT p.id, p.name,p.oid,p.queries_top, p.tic,p.pr,p.yc,p.dmoz,p.start_date,p.update_date,
            r.title as reg_name, COUNT(q.id) as pqueries_cnt
            FROM projects p
            LEFT JOIN geo_regions r ON p.region = r.id
            LEFT JOIN queries q ON p.id = q.pid
            GROUP BY p.id $permitions";

        $command = Yii::$app->db->createCommand($selectQuery);

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

    /**
     * Получить запросы проекта
     * @param $pid
     * @param $oid
     * @param null $date
     * @return array
     */
    public function getProjectQueries($pid, $oid, $date = null)
    {
        $users = new User();

        // Разграничение пользователей
        $permitions = "";
        if ($users->getUserRole($oid) == 'U'){
            $permitions = "AND p.oid = {$oid}";
        }

        $queriesList = [];
        // Если не указана дата, получаем за последний апдейт
        if ($date == null) {
            $command = Yii::$app->db->createCommand("SELECT q.text,q.url, h.position as up_new, h2.position as up_old, h.date as date_new, h2.date as date_old
              FROM queries q
              LEFT JOIN projects p ON p.id = q.pid
              LEFT JOIN history h ON h.qid = q.id
              LEFT JOIN history h2 ON h2.qid = q.id
              WHERE q.pid = :pid $permitions AND h.date = (SELECT MAX(date) FROM history)
              AND h2.date = (SELECT date FROM history WHERE date < (SELECT MAX(date) FROM history) ORDER BY date DESC LIMIT 1)");
        }
        else{
            $command = Yii::$app->db->createCommand("SELECT q.text,q.url, h.position as up_new, h2.position as up_old, h.date as date_new, h2.date as date_old
              FROM queries q
              LEFT JOIN projects p ON p.id = q.pid
              LEFT JOIN history h ON h.qid = q.id
              LEFT JOIN history h2 ON h2.qid = q.id
              WHERE q.pid = :pid $permitions AND h.date = :date
              AND h2.date = (SELECT date FROM history WHERE date < :date ORDER BY date DESC LIMIT 1)");
            $command->bindParam(":date", $date);
        }

        $command->bindParam(":pid", $pid);

        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $rowQuery = new Query();
            $rowQuery->text = $row['text'];
            $rowQuery->url = $row['url'];
            $rowQuery->positions = [$row['date_new'] => $row['up_new'], $row['date_old'] => $row['up_old']];

            $queriesList[] = $rowQuery;
        }

        return $queriesList;
    }

    /*
     * Получить даты апдейтов
     * */
    public function getUpdateDates()
    {
        $updates = [];
        $command = Yii::$app->db->createCommand("SELECT DISTINCT date FROM history ORDER date DESC");
        $dataReader = $command->query();

        while (($row = $dataReader->read()) !== false) {
            $updates[] = $row['date'];
        }

        return $updates;
    }

    public function getProjectInfo($pid)
    {
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

            $name = "http://www.site-$prj.ru";
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