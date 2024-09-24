<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerCssFile('@web/css/styles.css');
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

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="product-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'price')->textInput(['id' => 'product-price']) ?>
    <?= $form->field($model, 'quantity')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'product_image')->fileInput([
        'class' => 'custom-file-input',
        'id' => 'product-image',
        'accept' => 'image/*'
    ])->label('Imagem do Produto', ['class' => 'custom-file-label']) ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <hr>

    <div id="image-preview" style="text-align: center; margin-top: 20px;"></div>

    <script>
        document.getElementById('product-image').onchange = function (event) {
            const input = event.target;
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '200px';
                img.style.marginTop = '20px';

                // Limpa a pré-visualização anterior
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML = '';
                previewContainer.appendChild(img);
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        };
    </script>

    <?php ActiveForm::end(); ?>
</div>
