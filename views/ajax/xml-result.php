
<?php //var_dump($model->queries);?>
<table class="table table-hover " id="results-table"> 
            <tr>
                <th>#</th>
                <th>Запрос</th>
                <th>Позиция</th>
                <th>Найденный урл</th>
            </tr>
            <tbody>
            <?php $i=1; ?>
            <?php foreach ($model->result as $q => $pos): ?>
            <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $pos['query']?></td>
            <td><?php echo $pos['data']['position']?></td>
            <td><a href="http://<?php echo $pos['data']['uri']?>;?>"><?php echo $pos['data']['uri']?></a></td>
            </tr>
            <?php $i++; ?>
            <?php endforeach; ?>
            <tbody>

            </table>
            <a href="results/<?php echo $model->lastWrittenFile?>">Скачать CSV</a>