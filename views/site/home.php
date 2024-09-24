<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
$this->title = 'Home';
?>
<div class="site-home">
    <div class="jumbotron text-center bg-primary text-white py-5">
        <h1 class="display-4"><?= Html::encode($this->title) ?></h1>
        <p class="lead">Gerencie seus produtos e vendas de maneira eficiente e prática!</p>
    </div>

    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-lg-6 mb-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="card-title">Explorar Produtos</h2>
                        <p class="card-text">Descubra todos os produtos disponíveis e gerencie seu estoque facilmente.</p>
                        <?= Html::a('Ver Produtos', ['/product/index'], ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="card-title">Gerenciar Vendas</h2>
                        <p class="card-text">Acompanhe suas vendas, crie novas transações e maximize suas oportunidades de negócios.</p>
                        <?= Html::a('Fazer uma Venda', ['/sale/create'], ['class' => 'btn btn-success btn-block']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
