<?php

namespace app\models\service;

use app\models\objects\Project;
use app\models\Xml;
use Yii;
use yii\base\Model;

class SParser extends Model{

    const SYSTEM_USER = 1;

    public function run(){
        $projectsManager = new Project();
        $projectsList = $projectsManager->getAllProjectsList(SParser::SYSTEM_USER);
        $updateDate = date("Y-m-d");

        foreach($projectsList as $currentProject){

            $pid = $currentProject->id;
            $url = $currentProject->name;

            $url = str_replace('http://','',$url);

            $XMLParser = new Xml();
            $XMLParser->region = $currentProject->region;
            $XMLParser->siteUrl = $url;
            $XMLParser->siteUrlOrig = $url;



            print "Parsing project $url id($pid)\r\n";
            $queriesList = $projectsManager->getProjectAllQueries($pid,SParser::SYSTEM_USER);

            foreach($queriesList as $query){
                $result = $XMLParser->makeYandexQuery($query->text);
                print_r($result);

               /* $result = array(
                    'response' => '',
                    'position' => 10,
                    'uri' => $query->url,
                );*/

                //TODO Определять частотность запроса - проблема
                $freq = 0;

                $commandInsert = Yii::$app->db->createCommand("INSERT INTO history(qid,position,frequency,date) VALUES (:qid,:position,:freq,:date)");
                $commandInsert->bindParam(":qid",$query->id);
                $commandInsert->bindParam(":position",$result['position']);
                $commandInsert->bindParam(":freq",$freq);
                $commandInsert->bindParam(":date",$updateDate);
                $commandInsert->execute();

                $commandUpdate = Yii::$app->db->createCommand("UPDATE queries SET url = :url WHERE id=:qid");
                $commandUpdate->bindParam(":url",$result['uri']);
                $commandUpdate->bindParam(":qid",$query->id);
                $commandUpdate->execute();
            }

            // TODO Определить запросы в топе

            // Парсим изменения данных о проекте (объект типа Project)
            $project = $XMLParser->fetchProjectData($url);
            $projectsManager->updateProject($pid,$project->tic,$project->pr,$project->yc,$project->dmoz,$updateDate);
        }
    }




}