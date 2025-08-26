<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 350px;">
        <h3 class="card-title text-center mb-3"><?= Html::encode($this->title) ?></h3>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php if (Yii::$app->session->hasFlash('success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?= Yii::$app->session->getFlash('success') ?>'
    });
</script>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('info')): ?>
<script>
    Swal.fire({
        icon: 'info',
        title: 'Info',
        text: '<?= Yii::$app->session->getFlash('info') ?>'
    });
</script>
<?php endif; ?>
