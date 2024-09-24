<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Product;
use app\models\User;

$this->title = 'Cadastrar Venda';
$this->params['breadcrumbs'][] = ['label' => 'Vendas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Script para máscara de preço
$this->registerJs("
    $(document).ready(function() {
        $('#sales-total_price').inputmask('decimal', {
            radixPoint: '.',
            groupSeparator: ',',
            digits: 2,
            autoGroup: true,
            prefix: '$ ',
            placeholder: '0.00'
        });
    });
");

$products = Product::find()->all();
$users = User::find()->all();
?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id')->dropDownList(
        ArrayHelper::map($products, 'id', 'name'), 
        ['prompt' => 'Selecione um Produto', 'id' => 'product-select']
    ) ?>

    <?= $form->field($model, 'user_id')->dropDownList(
        ArrayHelper::map($users, 'id', 'name'), 
        ['prompt' => 'Selecione um Usuário']
    ) ?>

    <?= $form->field($model, 'quantity')->textInput(['id' => 'quantity-input', 'type' => 'number', 'min' => 1]) ?>

    <?= $form->field($model, 'total_price')->textInput(['id' => 'total-price-input']) ?>

    <?= $form->field($model, 'installments')->textInput(['type' => 'number', 'min' => 1, 'id' => 'installments-input']) ?>

    <div id="installment-fields"></div>

    <?= $form->field($model, 'sale_date')->textInput(['type' => 'date'])->label('Data da Venda') ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <strong>Erros de validação:</strong>
        <ul>
            <?php foreach ($model->getErrors() as $errors): ?>
                <?php foreach ($errors as $error): ?>
                    <li><?= Html::encode($error) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var productSelect = document.getElementById('product-select');
        var quantityInput = document.getElementById('quantity-input');
        var totalPriceInput = document.getElementById('total-price-input');
        var installmentsInput = document.getElementById('installments-input');
        var installmentFields = document.getElementById('installment-fields');

        // Verifica se os elementos existem antes de adicionar event listeners
        if (productSelect && quantityInput && totalPriceInput) {
            var productPrices = <?= json_encode(ArrayHelper::map($products, 'id', 'price')) ?>;

            function updateTotalPrice() {
                var productId = productSelect.value;
                var quantity = quantityInput.value;
                var price = productPrices[productId] || 0;
                var totalPrice = quantity * price;
                totalPriceInput.value = totalPrice.toFixed(2);
            }

            productSelect.addEventListener('change', updateTotalPrice);
            quantityInput.addEventListener('input', updateTotalPrice);
        }

        // Adiciona evento para a mudança do número de parcelas
        if (installmentsInput) {
            installmentsInput.addEventListener('change', function() {
                var count = parseInt(this.value);
                installmentFields.innerHTML = '';
                for (var i = 0; i < count; i++) {
                    installmentFields.innerHTML += '<div class=\"form-group\"><label>Installment ' + (i + 1) + '</label><input type=\"number\" name=\"Sales[installmentValues][' + i + ']\" class=\"form-control\" step=\"0.01\" required></div>';
                }
            });
        }
    });
</script>
