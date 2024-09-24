<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Listagem de Produtos';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/styles.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
$this->registerJsFile('@web/js/confirm-delete.js', ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<div class="product-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php Pjax::begin(['id' => 'product-grid']); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'headerRowOptions' => ['class' => 'header-row'],
        'filterRowOptions' => ['class' => 'filter-row'],
        'columns' => [
            [
                'attribute' => 'product_image',
                'format' => 'raw',
                'value' => function ($model) {
                    $imageUrl = Yii::getAlias('@web/uploads/' . $model->product_image);
                    $defaultImageUrl = 'https://placehold.co/90x90';
                    $localImagePath = Yii::getAlias('@uploads/' . $model->product_image);
                    
                    return Html::tag('div', 
                        !empty($model->product_image) && file_exists($localImagePath) // Verifica se o campo não está vazio e se o arquivo existe
                            ? Html::img($imageUrl, [
                                'width' => '100', 
                                'alt' => 'Product Image', 
                                'class' => 'img-thumbnail', 
                                'data-toggle' => 'modal', 
                                'data-target' => '#imageModal', 
                                'data-image' => $imageUrl
                            ]) 
                            : Html::img($defaultImageUrl, [ // Usando a imagem de placeholder
                                'width' => '100', 
                                'alt' => 'Placeholder Image', 
                                'class' => 'img-thumbnail', 
                                'data-toggle' => 'modal', 
                                'data-target' => '#imageModal', 
                                'data-image' => $defaultImageUrl
                            ]),
                        ['style' => 'text-align: center;']
                    );
                },
            ],
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'column-1']
            ],
            [
                'attribute' => 'description',
                'headerOptions' => ['class' => 'column-2']
            ],
            'price',
            'quantity',
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
                            'onclick' => "confirmDelete('$url', '" . Html::encode($model->name) . "'); return false;",
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Imagem do Produto</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" alt="Produto">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal de Confirmação -->
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
                    Você tem certeza que deseja excluir o produto <strong class="user-name"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$this->registerJs("
$(document).ready(function() {
    $(document).on('click', '.img-thumbnail', function() {
        var imageUrl = $(this).data('image');
        $('#modalImage').attr('src', imageUrl);
        $('#imageModal').modal('show'); // Mostra o modal
    });
});
");
?>
