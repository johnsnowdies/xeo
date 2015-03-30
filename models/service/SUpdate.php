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

        // Если сегодня уже был апдейт - не запускаем
        if ($lastUpdate != $dateNow){
            if($this->seoBudgetUpdate($dateNow)){
                print "UPDATE DETECTED AND STARTED AT $dateNow\r\n";
                $parser = new SParser();
                $parser->run();
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
}