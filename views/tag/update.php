<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = isset($tag->id) ? 'Update Tag' : 'Create Tag';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($tag, 'name')->textInput(['maxlength' => true]) ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
