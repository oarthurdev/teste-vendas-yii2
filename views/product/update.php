<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Atualizar Produto: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("
    $(document).ready(function(){
        $('#product-price').inputmask('decimal', {
            radixPoint: '.',
            groupSeparator: ',',
            digits: 2,
            autoGroup: true,
            prefix: '$ ',
            placeholder: '0.00'
        });
    });
");

?>

<div class="product-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="product-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        <?= $form->field($model, 'price')->textInput(['id' => 'product-price']) ?>
        <?= $form->field($model, 'quantity')->input('number', ['min' => 0]) ?>

        <div class="form-group">
            <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
