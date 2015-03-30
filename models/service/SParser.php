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
            $url = str_replace('/','',$url);

            $XMLParser = new Xml();
            $XMLParser->region = $currentProject->region;
            $XMLParser->siteUrl = $url;
            $XMLParser->siteUrlOrig = $url;

            print "Parsing project $url id($pid)\r\n";
            $queriesList = $projectsManager->getProjectAllQueries($pid,SParser::SYSTEM_USER);

            foreach($queriesList as $query){
                $result = $XMLParser->makeYandexQuery($query->text);
                print $query->text."\r\n";

                //TODO Определять частотность запроса - проблема
                $freq = 0;
                $commandInsert = Yii::$app->db->createCommand("INSERT INTO history(qid,position,url,frequency,date) VALUES (:qid,:position,:url,:freq,:date)");
                $commandInsert->bindParam(":qid",$query->id);
                $commandInsert->bindParam(":position",$result['position']);
                $commandInsert->bindParam(":url",$result['uri']);
                $commandInsert->bindParam(":freq",$freq);
                $commandInsert->bindParam(":date",$updateDate);
                $commandInsert->execute();
                $commandUpdate = Yii::$app->db->createCommand("UPDATE queries SET new_query=0 WHERE id=:qid");
                $commandUpdate->bindParam(":qid",$query->id);
                $commandUpdate->execute();
            }

            // Парсим изменения данных о проекте (объект Project)
            $project = $XMLParser->fetchProjectData($url);
            $projectsManager->updateProject($pid,$project->tic,$project->pr,$project->yc,$project->dmoz,$updateDate);
        }
    }




}