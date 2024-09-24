<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Product;
use app\models\User;

$this->title = 'Atualizar Venda';
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

    <div class="form-group">
        <?= $form->field($model, 'user_id')->label("Vendedor")->dropDownList(
            ArrayHelper::map($users, 'id', 'name'), 
            ['prompt' => 'Selecione um Usuário']
        ) ?>
    </div>

    <div id="product-list">
        <h4>Produtos</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="product-items">
                <?php foreach ($model->salesProducts as $salesProduct): ?>
                    <tr class="product-item">
                        <td>
                            <?= $form->field($model, 'product_id[]')->dropDownList(
                                ArrayHelper::map($products, 'id', 'name'), 
                                ['prompt' => 'Selecione um Produto', 'options' => [$salesProduct->product_id => ['Selected' => true]]]
                            )->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'quantity[]')->textInput(['type' => 'number', 'min' => 1, 'value' => $salesProduct->quantity])->label(false) ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-product">Remover</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <button type="button" id="add-product" class="btn btn-secondary">Adicionar Produto</button>

    <div class="form-group">
        <?= $form->field($model, 'total_price')->textInput(['id' => 'total-price-input', 'readonly' => true]) ?>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'installments')->textInput(['type' => 'number', 'min' => 1, 'id' => 'installments-input']) ?>
    </div>

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
        var totalPriceInput = document.getElementById('total-price-input');
        var productItems = document.getElementById('product-items');
        var installmentsInput = document.getElementById('installments-input');
        var installmentFields = document.getElementById('installment-fields');

        function updateTotalPrice() {
            var totalPrice = 0;

            var productElements = productItems.getElementsByClassName('product-item');
            for (var i = 0; i < productElements.length; i++) {
                var productId = productElements[i].querySelector('select[name="Sales[product_id][]"]').value;
                var quantity = productElements[i].querySelector('input[name="Sales[quantity][]"]').value;
                var price = <?= json_encode(ArrayHelper::map($products, 'id', 'price')) ?>[productId] || 0;
                totalPrice += quantity * price;
            }

            totalPriceInput.value = totalPrice.toFixed(2);
            updateInstallments();
        }

        function updateInstallments() {
            var totalPrice = parseFloat(totalPriceInput.value.replace('$ ', '').replace(',', '')) || 0;
            var installmentsCount = parseInt(installmentsInput.value) || 0;
            var installmentAmount = (installmentsCount > 0) ? (totalPrice / installmentsCount).toFixed(2) : 0;

            // Limpa os campos de parcelas
            installmentFields.innerHTML = '';
            for (var i = 0; i < installmentsCount; i++) {
                installmentFields.innerHTML += '<div class="form-group"><label>Parcelamento ' + (i + 1) + '</label><input type="text" class="form-control" value="$ ' + installmentAmount + '"></div>';
            }
        }

        productItems.addEventListener('change', updateTotalPrice);

        document.getElementById('add-product').addEventListener('click', function() {
            var newProductRow = document.createElement('tr');
            newProductRow.className = 'product-item';
            newProductRow.innerHTML = `
                <td>
                    <?= $form->field($model, 'product_id[]')->dropDownList(ArrayHelper::map($products, 'id', 'name'), ['prompt' => 'Selecione um Produto'])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'quantity[]')->textInput(['type' => 'number', 'min' => 1])->label(false) ?>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-product">Remover</button>
                </td>
            `;
            productItems.appendChild(newProductRow);

            // Adiciona o evento de atualização de preço para o novo produto
            newProductRow.addEventListener('change', updateTotalPrice);

            // Adiciona o evento de remover produto
            newProductRow.querySelector('.remove-product').addEventListener('click', function() {
                productItems.removeChild(newProductRow);
                updateTotalPrice();
            });
        });

        if (installmentsInput) {
            installmentsInput.addEventListener('change', updateInstallments);
        }

        document.querySelectorAll('.remove-product').forEach(function(button) {
            button.addEventListener('click', function() {
                var row = this.closest('tr');
                productItems.removeChild(row);
                updateTotalPrice();
            });
        });
    });
</script>
