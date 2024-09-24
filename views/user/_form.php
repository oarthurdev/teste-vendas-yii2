<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->registerCssFile('@web/css/styles.css');

?>

<div class="user-form">
    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password_hash')->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success ml-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
