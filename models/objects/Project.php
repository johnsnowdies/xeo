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
            WHERE p.new_project = 0
            GROUP BY p.id $permitions ";

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
     * Получить список проектов для оптимизатора
     * @param $oid
     */
    public function getAllProjectsList($oid)
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
            GROUP BY p.id $permitions ";

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

    public function getNewProjectsList($oid){
        $newProjectList = [];

        $users = new User();
        // Разграничение пользователей
        $permitions = "";
        if ($users->getUserRole($oid) == 'U'){
            $permitions = "HAVING p.oid = {$oid}";
        }

        $selectQuery = "SELECT p.id, p.name,p.oid,COUNT(q.id) as pqueries_cnt,p.start_date,p.update_date
              FROM projects p LEFT JOIN queries q ON p.id = q.pid WHERE p.new_project = 1
              GROUP BY p.id $permitions";

        $selectCmd  = Yii::$app->db->createCommand($selectQuery);
        $dataReader = $selectCmd->query();
        while (($row = $dataReader->read()) !== false) {
            $rowProject = new Project();
            $rowProject->id = $row['id'];
            $rowProject->name = $row['name'];
            $rowProject->oid = $row['oid'];
            $rowProject->queriesCnt = $row['pqueries_cnt'];
            $rowProject->startDate = $row['start_date'];
            $rowProject->updateDate = $row['update_date'];

            $newProjectList[] = $rowProject;
        }
        return $newProjectList;
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
            $command = Yii::$app->db->createCommand("SELECT q.id,q.text,q.url, h.frequency, h.position as up_new, h2.position as up_old, h.date as date_new, h2.date as date_old
              FROM queries q
              LEFT JOIN projects p ON p.id = q.pid
              LEFT JOIN history h ON h.qid = q.id AND h.date = (SELECT MAX(date) FROM history)
              LEFT JOIN history h2 ON h2.qid = q.id  AND h2.date = (SELECT date FROM history WHERE date < (SELECT MAX(date) FROM history) ORDER BY date DESC LIMIT 1)
              WHERE q.pid = :pid $permitions AND q.new_query = 0 ORDER BY h.position DESC");
        }
        else{
            $command = Yii::$app->db->createCommand("SELECT q.id,q.text,q.url, h.frequency, h.position as up_new, h2.position as up_old, h.date as date_new, h2.date as date_old
              FROM queries q
              LEFT JOIN projects p ON p.id = q.pid
              LEFT JOIN history h ON h.qid = q.id AND h.date = :date
              LEFT JOIN history h2 ON h2.qid = q.id AND h2.date = (SELECT date FROM history WHERE date < :date ORDER BY date DESC LIMIT 1)
              WHERE q.pid = :pid $permitions AND q.new_query = 0 ORDER BY h.position DESC");
            $command->bindParam(":date", $date);
        }

        $command->bindParam(":pid", $pid);

        $dataReader = $command->query();

        while (($row = $dataReader->read()) !== false) {
            $rowQuery = new Query();
            $rowQuery->id = $row['id'];
            $rowQuery->text = $row['text'];
            $rowQuery->url = $row['url'];
            $rowQuery->frequency = $row['frequency'];

            if ($row['date_old']!=null){
                $rowQuery->position[] = [$row['date_old'] => $row['up_old']];
            }

            if ($row['date_new']!=null){
                $rowQuery->position[] = [$row['date_new'] => $row['up_new']];
                $rowQuery->top=$row['up_new'];
            }

            if ($row['up_new']!=null && $row['up_old']!=null){
                $rowQuery->diff = $row['up_old'] - $row['up_new'];
            }

            $queriesList[] = $rowQuery;
        }

        return $queriesList;
    }

    /**
     * Получить запросы проекта
     * @param $pid
     * @param $oid
     * @param null $date
     * @return array
     */
    public function getProjectAllQueries($pid, $oid, $date = null)
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
            $command = Yii::$app->db->createCommand("SELECT q.id,q.text,q.url, h.frequency, h.position as up_new, h2.position as up_old, h.date as date_new, h2.date as date_old
              FROM queries q
              LEFT JOIN projects p ON p.id = q.pid
              LEFT JOIN history h ON h.qid = q.id AND h.date = (SELECT MAX(date) FROM history)
              LEFT JOIN history h2 ON h2.qid = q.id  AND h2.date = (SELECT date FROM history WHERE date < (SELECT MAX(date) FROM history) ORDER BY date DESC LIMIT 1)
              WHERE q.pid = :pid $permitions");
        }
        else{
            $command = Yii::$app->db->createCommand("SELECT q.id,q.text,q.url, h.frequency, h.position as up_new, h2.position as up_old, h.date as date_new, h2.date as date_old
              FROM queries q
              LEFT JOIN projects p ON p.id = q.pid
              LEFT JOIN history h ON h.qid = q.id AND h.date = :date
              LEFT JOIN history h2 ON h2.qid = q.id AND h2.date = (SELECT date FROM history WHERE date < :date ORDER BY date DESC LIMIT 1)
              WHERE q.pid = :pid $permitions");
            $command->bindParam(":date", $date);
        }

        $command->bindParam(":pid", $pid);

        $dataReader = $command->query();

        while (($row = $dataReader->read()) !== false) {
            $rowQuery = new Query();
            $rowQuery->id = $row['id'];
            $rowQuery->text = $row['text'];
            $rowQuery->url = $row['url'];
            $rowQuery->frequency = $row['frequency'];

            if ($row['date_old']!=null){
                $rowQuery->position[] = [$row['date_old'] => $row['up_old']];
            }

            if ($row['date_new']!=null){
                $rowQuery->position[] = [$row['date_new'] => $row['up_new']];
                $rowQuery->top=$row['up_new'];
            }

            if ($row['up_new']!=null && $row['up_old']!=null){
                $rowQuery->diff = $row['up_old'] - $row['up_new'];
            }

            $queriesList[] = $rowQuery;
        }

        return $queriesList;
    }

    public function getProjectAllTextQueries($pid)
    {
        $selectCmd = Yii::$app->db->createCommand("SELECT id,text FROM queries WHERE pid = :pid");
        $selectCmd->bindParam(":pid",$pid);

        $dataReader = $selectCmd->query();
        $textQueries = [];
        while (($row = $dataReader->read()) !== false) {
            $rowQuery = new Query();
            $rowQuery->id = $row['id'];
            $rowQuery->text = $row['text'];
            $textQueries[] = $rowQuery;
        }

        return $textQueries;

    }

    /**
     * Получить запросы проекта
     * @param $pid
     * @param $oid
     * @param null $date
     * @return array
     */
    public function getProjectNewQueries($pid, $oid)
    {
        $users = new User();

        // Разграничение пользователей
        $permitions = "";
        if ($users->getUserRole($oid) == 'U'){
            $permitions = "AND p.oid = {$oid}";
        }

        $selectCmd = Yii::$app->db->createCommand("SELECT * FROM queries q
        LEFT JOIN projects p ON p.id = q.pid
        WHERE q.new_query = 1 AND q.pid = :pid $permitions");
        $selectCmd->bindParam(":pid",$pid);
        $dataReader = $selectCmd->query();

        $newQueriesList = [];

        while (($row = $dataReader->read()) !== false) {
            $rowQuery = new Query();
            $rowQuery->id = $row['id'];
            $rowQuery->text = $row['text'];
            $newQueriesList[] = $rowQuery;
        }

        return $newQueriesList;
    }

    public function deleteProject($pid){
        $command = Yii::$app->db->createCommand("DELETE FROM projects WHERE id = :id");
        $command->bindParam(":id",$pid);
        $command->execute();
        return true;
    }


    /*
     * Получить даты апдейтов
     * */
    public function getUpdateDates($pid)
    {
        $updates = [];
        $command = Yii::$app->db->createCommand("SELECT DISTINCT h.date
                              FROM history h
                              LEFT JOIN queries q ON q.id = h.qid
                              WHERE q.pid = :pid
                              ORDER BY date DESC");
        $command->bindParam(":pid",$pid);
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

    public function updateProject($pid,$tic,$pr,$yc,$dmoz,$updateDate){

        // TODO Обновление топа

        $top = [3,5,10,20];
        $topValues = [];
        foreach($top as $t){
            $query = "SELECT COUNT(h.id) as top FROM history h
LEFT JOIN queries q ON q.id = h.qid
LEFT JOIN projects p ON p.id = q.pid
WHERE p.id = :pid AND h.position <= :t AND h.date=:updateDate";
            $cmd = Yii::$app->db->createCommand($query);
            $cmd->bindParam(":pid",$pid);
                $cmd->bindParam(":t",$t);
                    $cmd->bindParam(":updateDate",$updateDate);
            $dataReader = $cmd->query();

            while (($row = $dataReader->read()) !== false) {
                $topValues[] = $row['top'];
            }

        }

        $topValues = serialize($topValues);

        $cmdUpdate = Yii::$app->db->createCommand("UPDATE projects SET new_project=0, queries_top = :top, tic = :tic, pr = :pr, yc = :yc, dmoz = :dmoz, update_date = :updateDate WHERE id = :pid");
        $cmdUpdate->bindParam(":tic",$tic);
        $cmdUpdate->bindParam(":pr",$pr);
        $cmdUpdate->bindParam(":yc",$yc);
        $cmdUpdate->bindParam(":dmoz",$dmoz);
        $cmdUpdate->bindParam(":updateDate",$updateDate);
        $cmdUpdate->bindParam(":pid",$pid);
        $cmdUpdate->bindParam(":top",$topValues);
        $cmdUpdate->execute();
    }

    public function deleteQuery($qid){
        $cmdDelete = Yii::$app->db->createCommand("DELETE FROM queries WHERE id = :id");
        $cmdDelete->bindParam(":id",$qid);
        $cmdDelete->execute();

        $cmdDelete = Yii::$app->db->createCommand("DELETE FROM history WHERE qid = :id");
        $cmdDelete->bindParam(":id",$qid);
        $cmdDelete->execute();
        return true;
    }

    public function addQueries($queries, $pid){
        foreach($queries as $text){
            $cmdQuery = Yii::$app->db->createCommand("INSERT INTO queries(pid,text) VALUES(:pid,:text);");
            $cmdQuery->bindParam(":pid", $pid);
            $cmdQuery->bindParam(":text", $text);
            $cmdQuery->execute();
        }

        return true;
    }

    public function createProjcet($name,$oid,$queries,$region){
        $createProjectQuery = "INSERT INTO projects(name,oid,region,start_date,update_date)
            VALUES(:name,:oid,:region,NOW(), NOW());";
        $cmdProject = Yii::$app->db->createCommand($createProjectQuery);
        $cmdProject->bindParam(":name", $name);
        $cmdProject->bindParam(":oid", $oid);
        $cmdProject->bindParam(":region", $region);
        $cmdProject->execute();

        $pid = Yii::$app->db->getLastInsertID();

        $queries = explode("\n", $queries);
        foreach($queries as $query){
            $createQuery = "INSERT INTO queries(pid,text) VALUES(:pid,:text);";
            $cmdQuery = Yii::$app->db->createCommand($createQuery);
            $cmdQuery->bindParam(":pid", $pid);
            $cmdQuery->bindParam(":text", $query);
            $cmdQuery->execute();
        }

        return true;
    }

    public function changeProjectUser($pid,$uid){
        $command = Yii::$app->db->createCommand("UPDATE projects SET oid=:uid WHERE id=:id");
        $command->bindParam(":uid",$uid);
        $command->bindParam(":id",$pid);
        $command->execute();
        return true;
    }

    public function createTest()
    {
        for ($prj = 0; $prj < rand(10, 15); $prj++) {
            $createProjectQuery = "INSERT INTO projects(name,oid,queries_top,region,tic,pr,yc,dmoz,start_date,update_date)
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

                $qid = Yii::$app->db->getLastInsertID();
                $updates = ['2015-03-08','2015-03-09','2015-03-10','2015-03-11','2015-03-12','2015-03-13','2015-03-14'];

                foreach($updates as $date){
                    $position = rand(1,30);
                    $freq = rand(1,50);

                    $createHistory = "INSERT INTO history(qid, position,frequency,date) VALUES(:qid,:pos, :freq, :date)";
                    $cmdHistory = Yii::$app->db->createCommand($createHistory);
                    $cmdHistory->bindParam(":qid", $qid);
                    $cmdHistory->bindParam(":pos", $position);
                    $cmdHistory->bindParam(":freq", $freq);
                    $cmdHistory->bindParam(":date", $date);
                    $cmdHistory->execute();

                }

            }
        }
    }
}