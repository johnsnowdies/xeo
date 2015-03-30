<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CronServices extends Model
{
    public function getCronState(){
        $cmdSelect = Yii::$app->db->createCommand("SELECT * FROM cron_services");
        $dataReader = $cmdSelect->query();

        $cronState = [];
        while( ($row = $dataReader->read()) !== false){
            $cronState[$row['param']] = $row['value'];
        }

        return $cronState;
    }
}
