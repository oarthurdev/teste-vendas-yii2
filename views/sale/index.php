<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Product;
use app\models\User;

$this->title = 'Vendas';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/styles.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
$this->registerJsFile('@web/js/confirm-delete.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/delete-multiple.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/export-pdf.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$products = Product::find()->all();
$users = User::find()->all();

?>

<div class="sales-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'w0-pjax']); ?>

    <?= $this->render('_search', [
        'model' => $searchModel, 
        'products' => $products,
        'users' => $users,
    ]); ?>

    <hr />

    <div class="button-group">
        <?= Html::button('Deletar Selecionados', [
            'class' => 'btn btn-danger',
            'id' => 'delete-multiple',
            'disabled' => true,
        ]) ?>
        <?= Html::button('Exportar PDF', [
            'class' => 'btn btn-primary',
            'id' => 'export-pdf',
            'disabled' => true,
        ]) ?>
    </div>

    <?= GridView::widget([
        'id' => 'w0',
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['style' => 'text-align: center;'],
                'checkboxOptions' => function ($model) {
                    return ['value' => $model->id, 'class' => 'item-checkbox'];
                },
                'header' => Html::checkbox('select-on-check-all', false, [
                    'class' => 'select-on-check-all',
                ]),
            ],
            [
                'attribute' => 'user.name',
                'label' => 'Vendedor',
                'value' => function($model) {
                    return $model->user->name;
                },
            ],
            [
                'label' => 'Produto / Quantidade',
                'format' => 'raw',
                'value' => function($model) {
                    $productList = '';
                    foreach ($model->products as $product) {
                        $productList .= Html::tag('div', $product->name . ' / ' . $product->quantity, ['class' => 'product-item']);
                    }
                    return $productList;
                },
            ],
            'total_price',
            'sale_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Ações',
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'text-align: center;'],
                'headerOptions' => ['style' => 'text-align: center;'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-edit"></i>', $url, [
                            'title' => 'Atualizar',
                            'aria-label' => 'Atualizar',
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash-alt"></i>', '#', [
                            'title' => 'Excluir',
                            'aria-label' => 'Excluir',
                            'onclick' => "confirmDelete('$url', '" . Html::encode($model->user->name) . "'); return false;",
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir a venda de <strong class="user-name"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .button-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .btn {
        flex: 1;
        min-width: 150px;
    }

    .product-item {
        padding: 5px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin: 2px 0;
    }

    @media (max-width: 768px) {
        .button-group {
            flex-direction: column;
        }
    }
</style>
