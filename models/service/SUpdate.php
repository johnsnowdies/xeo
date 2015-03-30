<?php

namespace app\models\service;

use SimpleXMLElement;
use Yii;
use yii\base\Model;

class SUpdate extends Model{

    private function seoBudgetUpdate($dateNow){
        $content = file_get_contents("https://seobudget.ru/downloads/updates.xml");
        $updates = new SimpleXMLElement($content);
        $updateDate =  (string) $updates->update[1]->date[0]['timestamp'];
        return (date( 'Y-m-d', $updateDate) == $dateNow)? true: false;
    }

    public function checkUpdate(){
        $dateNow = date( 'Y-m-d');
        $lastUpdate = $this->getLastUpdateDate();
        $this->setCheckDate($dateNow);

        // Если сегодня уже был апдейт - не запускаем
        if ($lastUpdate != $dateNow){
            if($this->seoBudgetUpdate($dateNow)){
                print "UPDATE DETECTED AND STARTED AT $dateNow\r\n";
                $this->setUpdateInfo($dateNow);
                $this->setUpdateRuning();
                $parser = new SParser();
                $parser->run();
                $this->setUpdateStop();
            }
        }else{
            return false;
        }

        return true;
    }

    public function getLastUpdateDate(){
        $command = Yii::$app->db->createCommand("SELECT MAX(date) AS last_update FROM history");
        $lastUpdate = $command->queryOne();
        return $lastUpdate;
    }

    private function setUpdateInfo($updateDate){
        $lastUpdateQuery = "UPDATE cron_services SET value = :updateDate WHERE param = 'last_update_date'";
        $command = Yii::$app->db->createCommand($lastUpdateQuery);
        $command->bindParam(":updateDate",$updateDate);
        $command->execute();
    }

    private function setUpdateRuning(){
        $updateRuningQuery = "UPDATE cron_services SET value = 'true' WHERE param = 'update_runing'";
        $command = Yii::$app->db->createCommand($updateRuningQuery);
        $command->execute();
    }

    private function setUpdateStop(){
        $updateRuningQuery = "UPDATE cron_services SET value = 'false' WHERE param = 'update_runing'";
        $command = Yii::$app->db->createCommand($updateRuningQuery);
        $command->execute();
    }

    private function setCheckDate($checkDate){
        $lastCheckQuery = "UPDATE cron_services SET value = :checkDate WHERE param = 'last_check_date'";
        $command = Yii::$app->db->createCommand($lastCheckQuery);
        $command->bindParam(":checkDate",$checkDate);
        $command->execute();
    }

}