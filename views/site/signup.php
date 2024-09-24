<?php
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Sign Up';

// Link para o arquivo de estilo
$this->registerCssFile('@web/css/styles.css');
?>

<div class="site-signup">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <?php $form = ActiveForm::begin(['id' => 'signup-form']); ?>

            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Username'])->label(false) ?>

            <?= $form->field($model, 'name')->textInput(['placeholder' => 'Name'])->label(false) ?>

            <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email'])->label(false) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="text-center">
        <?= Html::a('Already have an account? Login', ['site/login']) ?>
    </div>
</div>
