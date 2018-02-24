<?php
/* @var $this yii\web\View */
$this->title = 'XEO';
use yii\helpers\Url;
?>
<div class="site-index">






    <div class="body-content">
        <h1>Выполнить запрос</h1>

        <div class="row">
            <div class="col-md-5">
                <form action="<?= Url::toRoute('/ajax/parseposition');?>" id="xml-query-form">
                    <div class="form-group">
                    <label for="siteUrl">URL сайта</label>
                        <input class="form-control" type="text" name="Xml[siteUrl]" id="siteUrl" placeholder="www.site.ru" />
                    </div>


                    <div class="form-group">
                    <label for="region">Регион <a href="http://search.yaca.yandex.ru/geo.c2n" target="_blank" title="Эта шта такое?"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></label>
                        <input class="form-control" type="text" name="Xml[region]" id="region" placeholder="введите регион" value="213"/>
                    </div>
                    
                    <div class="form-group">
                        <label for="queries">Список запросов</label>
                        <textarea name="Xml[queries]" id="queries" cols="60" rows="20"  class="form-control" placeholder="пластиковые окна Москва"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="top1000">Искать в топ-1000
                        <input type="checkbox" name="Xml[top1000]" id="top1000"  />
                        </label>
                    </div>

                    
                    <div class="form-group">
                        <input type="submit" class="btn btn-success" value="Начать ипать моск яндексу"/>
                    </div>
                </form>
            </div>

            <div class="col-md-7">
            <div style="display:none" id="ajax-loading"><img src="/img/ajax-loader.gif" /></div>
            <div id="output1"></div>

            </div>


        </div>

    </div>
</div>
