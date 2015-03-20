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
        $url = "http://bar-navig.yandex.ru/u?ver=2&show=32&url=http://" .$url;
        $cont = file_get_contents($url);
        $cont = preg_replace ( "'(.+?)value=\"'si","",$cont);
        $cont = preg_replace ( "'\"/>(.+?)'si","",$cont);
        return $cont;
    }

    private function strToNum($str, $check, $magic)
    {
        $int32Unit = 4294967296;  // 2^32
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++)
        {
            $check *= $magic;
            if ($check >= $int32Unit)
            {
                $check = ($check - $int32Unit * (int) ($check / $int32Unit));
                $check = ($check < -2147483648) ? ($check + $int32Unit) : $check;
            }
            $check += ord($str{$i});
        }
        return $check;
    }

    private function hashUrl($string)
    {
        $check1 = $this->strToNum($string, 0x1505, 0x21);
        $check2 = $this->strToNum($string, 0, 0x1003F);
        $check1 >>= 2;
        $check1 = (($check1 >> 4) & 0x3FFFFC0 ) | ($check1 & 0x3F);
        $check1 = (($check1 >> 4) & 0x3FFC00 ) | ($check1 & 0x3FF);
        $check1 = (($check1 >> 4) & 0x3C000 ) | ($check1 & 0x3FFF);
        $T1 = (((($check1 & 0x3C0) << 4) | ($check1 & 0x3C)) <<2 ) | ($check2 & 0xF0F );
        $T2 = (((($check1 & 0xFFFFC000) << 4) | ($check1 & 0x3C00)) << 0xA) | ($check2 & 0xF0F0000 );
        return ($T1 | $T2);
    }

    private  function checkHash($hashNum)
    {
        $checkByte = 0;
        $flag = 0;
        $hashStr = sprintf('%u', $hashNum) ;
        $length = strlen($hashStr);
        for ($i = $length - 1;  $i >= 0;  $i --)
        {
            $re = $hashStr{$i};
            if (1 === ($flag % 2))
            {
                $re += $re;
                $re = (int)($re / 10) + ($re % 10);
            }
            $checkByte += $re;
            $flag ++;
        }
        $checkByte %= 10;
        if (0 !== $checkByte)
        {
            $checkByte = 10 - $checkByte;
            if (1 === ($flag % 2) )
            {
                if (1 === ($checkByte % 2))
                {
                    $checkByte += 9;
                }
                $checkByte >>= 1;
            }
        }
        return '7' . $checkByte . $hashStr;
    }


    private function getPR($url){
        // Из информеров

            if ( ! preg_match('/^(http:\/\/)(.*)/i', $url)) {
                $url = 'http://' .$url;
            }
            $googlehost = 'toolbarqueries.google.com';
            $googleua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5';
            $ch = $this->checkHash($this->hashUrl($url));
            $fp = fsockopen($googlehost, 80, $errno, $errstr, 30);
            if ($fp)
            {
                $out = "GET /tbr?features=Rank&sourceid=navclient-ff&client=navclient-auto-ff&ch=$ch&q=info:$url HTTP/1.1\r\n";
                $out .= "User-Agent: $googleua\r\n";
                $out .= "Host: $googlehost\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                $data = '';
                while ( ! feof($fp))
                {
                    $data = $data .fgets($fp, 1024);
                }
                fclose($fp);
                $pos = strpos($data, "Rank_");
                if($pos)
                {
                    $pr=substr($data, $pos + 9);
                    $pr=str_replace("\n",'',$pr);
                    $pr=trim($pr);
                }
            }
            if($pr=='')$pr='0';
            return $pr;
    }

    private function getYC($url){
        //https://yaca.yandex.ru/yca/cy/ch/www.my-page.ru/
        //ресурс не описан в Яндекс.Каталоге => false

        $yaca = file_get_contents("https://yaca.yandex.ru/yca/cy/ch/$url");
        return !preg_match("#ресурс не описан в Яндекс.Каталоге#siU",$yaca);
    }

    private function getDMOZ($url){
        //http://www.dmoz.org/search?q=www.my-page.ru
        // DMOZ Sites => true

        $dmoz = file_get_contents("http://www.dmoz.org/search?q=$url");
        return preg_match("#DMOZ Sites#siU",$dmoz);
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
