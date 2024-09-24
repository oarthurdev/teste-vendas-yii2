<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = 'Atualizar Venda: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Vendas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Carregar os produtos e os preços
$products = Product::find()->all();
$productPrices = ArrayHelper::map($products, 'id', 'price');

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("$(document).ready(function(){
    $('#sales-total_price').inputmask('decimal', {
        radixPoint: '.',
        groupSeparator: ',',
        digits: 2,
        autoGroup: true,
        prefix: '$ ',
        placeholder: '0.00'
    });
});");
?>

<div class="sales-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="sales-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'product_id')->hiddenInput(['id' => 'product-select', 'value' => $model->product_id])->label(false) ?>
        <?= $form->field($model, 'user_id')->dropDownList($users, ['prompt' => 'Selecione um Vendedor'])->label('Vendedor') ?>
        <?= $form->field($model, 'quantity')->input('number', ['min' => 0, 'id' => 'quantity-input', 'value' => $model->quantity]) ?>
        <?= $form->field($model, 'total_price')->textInput(['id' => 'sales-total_price', 'readonly' => true, 'value' => number_format($model->total_price, 2, '.', '')]) ?>
        <?= $form->field($model, 'installments')->textInput(['type' => 'number', 'min' => 1, 'id' => 'installments-input']) ?>

        <div id="installment-fields"></div>

        <?= $form->field($model, 'sale_date')->input('date', ['value' => $model->sale_date ? date('Y-m-d', strtotime($model->sale_date)) : '']) ?>

        <div class="form-group">
            <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
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
        var totalPriceInput = document.getElementById('sales-total_price');
        var installmentsInput = document.getElementById('installments-input');
        var installmentFields = document.getElementById('installment-fields');

        var productPrices = <?= json_encode($productPrices) ?>;

        function updateTotalPrice() {
            var productId = productSelect.value;
            var quantity = parseFloat(quantityInput.value) || 0;
            var price = productPrices[productId] || 0;
            var totalPrice = quantity * price;
            totalPriceInput.value = totalPrice.toFixed(2);
        }

        updateTotalPrice();

        quantityInput.addEventListener('input', updateTotalPrice);

        if (installmentsInput) {
            installmentsInput.addEventListener('change', function() {
                var count = parseInt(this.value);
                installmentFields.innerHTML = '';
                for (var i = 0; i < count; i++) {
                    installmentFields.innerHTML += '<div class="form-group"><label>Parcelamento ' + (i + 1) + '</label><input type="number" name="Sales[installmentValues][' + i + ']" class="form-control" step="0.01" required></div>';
                }
            });
        }
    });
</script>
