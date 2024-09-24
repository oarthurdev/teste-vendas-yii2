<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <strong>Ops, algo deu errado!</strong><br>
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Parece que um erro ocorreu enquanto o servidor estava processando sua solicitação. Isso pode ter ocorrido devido a um problema temporário ou algum erro nos dados fornecidos.
    </p>

    <h3>O que você pode fazer:</h3>
    <ul>
        <li><strong>Verificar novamente:</strong> Confirme se as informações que você forneceu estão corretas.</li>
        <li><strong>Atualize a página:</strong> Talvez o erro tenha sido temporário.</li>
        <li><strong>Contate o suporte:</strong> Se o problema persistir, entre em contato com nossa equipe de suporte e informe o código do erro abaixo.</li>
    </ul>

    <h4>Código do erro:</h4>
    <pre><?= Html::encode($exception->statusCode ?? 'Erro Desconhecido') ?></pre>

    <p>Obrigado pela sua paciência.</p>

    <div>
        <?= Html::a('Voltar à página inicial', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Tentar novamente', Yii::$app->request->referrer ?: Yii::$app->homeUrl, ['class' => 'btn btn-outline-secondary']) ?>
    </div>

</div>
