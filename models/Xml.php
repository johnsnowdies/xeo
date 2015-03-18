<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\lib\Punycode;
use app\models\objects\Project;
/**
 * ContactForm is the model behind the contact form.
 */
class Xml extends Model
{
    public $queries;
    public $siteUrl;
    public $siteUrlOrig;
    private $result;
    public $lastWrittenFile;
    public $top1000;
    public $region;


    public function getResult() {
        return $this->result;
    }
   

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        [['queries', 'siteUrl', 'top1000', 'region'], 'required'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
        //    'verifyCode' => 'Verification Code',
        ];
    }

    public function load($model) {
        parent::load($model);
        $this->queries = explode("\n", $this->queries);
        mb_internal_encoding('utf-8');
        $Punycode = new Punycode();
        $this->siteUrlOrig = $this->siteUrl;
        $this->siteUrl = $Punycode->encode($this->siteUrl);
    }


    public function getXml() {

        $this->result = array();
        foreach ($this->queries as $q) {
            $this->result[] = array(
                'query' => $q,
                'data' => $this->makeYandexQuery($q),
            );
        }

        return $this->result;


    }

    public function writeResultToCSC($fName) {

        @mkdir("results/".$this->siteUrlOrig);
        $fH = fopen("results/{$this->siteUrlOrig}/$fName", 'w');

        foreach ($this->result as $val) {
           

            
            fputcsv($fH, array($val['query'], $val['data']['position'], $val['data']['uri']));
        }
        
        
        
        fclose($fH);
        $this->lastWrittenFile = "{$this->siteUrlOrig}/$fName";
    }

    public function makeYandexQuery($q) {
        $query = "http://xmlsearch.yandex.ru/xmlsearch?".
        "user=active-seo-steam".
        "&key=03.281498801:8d74d0c282393baee75dc20d8aa7681e".
        "&query=" . urlencode($q) .
        "&l10n=ru".
        "&sortby=rlv".
        "&filter=moderate".
        "&lr=".$this->region.
        "&groupby=attr%3D%22%22.mode%3Dflat.groups-on-page%3D100.docs-in-group%3D1";

        $strXml = file_get_contents($query);

        $pos = $this->searchXmlUrl($strXml, $this->siteUrl);

        $result = array(
                'response' => '',
                'position' => $pos['pos'],
                'uri' => $pos['uri'],
            );
        return $result;
    }

    /**
     * Получение тИЦ, PR, Наличия в ЯК и DMOZ
     */
    public function fetchProjectData($url){
        $project = new Project();
        $project->tic = $this->getTIC($url);
        $project->pr = $this->getPR($url);

        if ($this->getYC($url)){
            $project->yc = 1;
        }else{
            $project->yc = 0;
        }

        if ($this->getDMOZ($url)){
            $project->dmoz = 1;
        }else{
            $project->dmoz = 0;
        }

        return $project;
    }


    private function getTIC($url){
        // Из информеров
        return true;
    }

    private function getPR($url){
        // Из информеров
        return true;
    }

    private function getYC($url){
        //https://yaca.yandex.ru/yca/cy/ch/www.my-page.ru/
        //ресурс не описан в Яндекс.Каталоге => false

        return true;
    }

    private function getDMOZ($url){
        //http://www.dmoz.org/search?q=www.my-page.ru
        // DMOZ Sites => true
        return true;
    }

    private function searchXmlUrl($strXml, $url2Find) {
        $xml = simplexml_load_string($strXml);
        $result = $xml->xpath('//doc/url');
        $i = 0;
        $isFound = false;
        while((list( , $node) = each($result)) && (!$isFound)) {
            $i++;
            $isFound = ($this->getSecondDomain($node)===$this->getSecondDomain($url2Find));
            if ($isFound) {
                $fNode = $node;
            }
        }
        return ($isFound?
            array('pos'=>$i, 'uri'=>$fNode)
            :
            array('pos'=>null, 'uri'=>null));
    }

    private function getSecondDomain($url) {

        return strtolower(preg_replace('#http[s]{0,1}:\/\/([^\/]+)\/.*#', '$1', str_replace('www.', '', $url)));
    }
}
