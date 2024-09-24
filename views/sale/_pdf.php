<?php
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCssFile('@web/css/report.css');

?>
<h1>Relatório de Vendas</h1>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Vendedor</th>
            <th>Quantidade</th>
            <th>Preço Total</th>
            <th>Parcelas</th>
            <?php
                $maxInstallments = 0;
                foreach ($dataProvider->getModels() as $model) {
                    $maxInstallments = max($maxInstallments, $model->installments);
                }
                
                for ($i = 1; $i <= $maxInstallments; $i++) {
                    echo "<th>Parcela $i</th>";
                }
            ?>
            <th>Data da Venda</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <tr>
                <td><?= Html::encode($model->product->name) ?></td>
                <td><?= Html::encode($model->user->name) ?></td>
                <td><?= Html::encode($model->quantity) ?></td>
                <td><?= Html::encode($model->total_price) ?></td>
                <td><?= Html::encode($model->installments) ?></td>
                <?php
                for ($j = 1; $j <= $maxInstallments; $j++) {
                    $installmentValue = $model->getInstallmentsValue($j);
                    echo "<td>" . ($installmentValue !== null ? Html::encode($installmentValue) : '') . "</td>";
                }
                ?>
                <td><?= Html::encode($model->sale_date) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
