<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $products array */
/* @var $users array */

?>

<div class="sales-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'product_id')->dropDownList($products, ['prompt' => 'Selecione um Produto'])->label('Produto') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'user_id')->dropDownList($users, ['prompt' => 'Selecione um Usuário'])->label('Usuário') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantidade'])->label('Quantidade') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'total_price')->textInput(['placeholder' => 'Preço Total'])->label('Preço Total') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'sale_date')->textInput(['type' => 'date'])->label('Data da Venda') ?>
        </div>

        <div class="col-md-4">
            <div class="form-group" style="margin-top: 24px;">
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Limpar', ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
