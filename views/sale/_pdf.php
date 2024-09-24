<?php
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCssFile('@web/css/report.css');

?>

<h1>Relatório de Vendas</h1>

<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%; text-align: left;">
    <thead>
        <tr>
            <th style="width: 15%;">Vendedor</th>
            <th style="width: 15%;">Preço Total</th>
            <th style="width: 10%;">Parcelas</th>
            <?php

                $maxInstallments = 0;
                foreach ($dataProvider->getModels() as $model) {
                    $maxInstallments = max($maxInstallments, $model->installments);
                }
                
                for ($i = 1; $i <= $maxInstallments; $i++) {
                    echo "<th style='width: 10%;'>Parcela $i</th>";
                }
            ?>
            <th style="width: 10%;">Data da Venda</th>
            <?php
                // Obter o número máximo de produtos e criar cabeçalhos dinâmicos
                $maxProducts = 0;
                foreach ($dataProvider->getModels() as $model) {
                    $maxProducts = max($maxProducts, count($model->products));
                }

                for ($i = 1; $i <= $maxProducts; $i++) {
                    echo "<th style='width: 10%;'>Produto $i</th>";
                    echo "<th style='width: 10%;'>Quant.</th>";
                }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $previousSaleId = null;

        foreach ($dataProvider->getModels() as $model): 
            if ($model->id !== $previousSaleId):
                if ($previousSaleId !== null) {
                    echo "</tr>";
                }
                $previousSaleId = $model->id;
                ?>
                <tr>
                    <td><?= Html::encode($model->user->name) ?></td>
                    <td>$<?= Html::encode(number_format($model->total_price, 2, '.', ',')) ?></td>
                    <td><?= Html::encode($model->installments) ?></td>
                    <?php
                    
                    for ($j = 1; $j <= $maxInstallments; $j++) {
                        $installmentValue = $model->getInstallmentsValue($j);
                        echo "<td>" . ($installmentValue !== null ? '$' . Html::encode(number_format($installmentValue, 2, '.', ',')) : '') . "</td>";
                    }
                    ?>
                    <td><?= Html::encode($model->sale_date) ?></td>
                    <?php
                    
                    if (!empty($model->products)):
                        foreach ($model->products as $product):
                            ?>
                            <td style="padding-left: 40px;"><?= Html::encode($product->name) ?></td>
                            <td><?= Html::encode($product->quantity) ?></td>
                            <?php
                        endforeach;
                    else:
                        
                        for ($k = 1; $k <= $maxProducts; $k++) {
                            echo "<td></td><td></td>";
                        }
                    endif;
                    ?>
                </tr>
                <?php
            endif;
        endforeach; ?>
        <?php if ($previousSaleId !== null) echo "</tr>";?>
    </tbody>
</table>
