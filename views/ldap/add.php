<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->beginPage();
$this->head();
$this->beginBody();
?>

<div class="admin-user-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-id',
        'enableAjaxValidation' => true,
        'validationUrl' => Url::toRoute(['validate-form']),
    ]); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'second_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'company')
        ->dropDownList(yii::$app->params['ldap_company'],['prompt'=>'--请选择--']) ?>
    <?= $form->field($model, 'depar')
        ->dropDownList(yii::$app->params['department'],['prompt'=>'--请选择--']) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common','Create') : Yii::t('common','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
    $this->endBody();
    $this->endPage();

?>