<?php

use open20\amos\core\forms\ActiveForm;
use yii\helpers\Html;

?>

<div class="oauth2-client-search element-to-toggle" data-toggle-element="form-search">

    <?php $form = ActiveForm::begin([
        'action' => (isset($originAction) ? [$originAction] : ['index']),
        'method' => 'get',
        'options' => [
            'class' => 'default-form'
        ]
    ]);
    ?>

    <div class="col-md-6">
        <?= $form->field($model, 'client_id')->textInput(); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'redirect_uri')->textInput(); ?>
    </div>


    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(Yii::t('amoscore', 'Reset'),
                \yii\helpers\Url::to(\Yii::$app->controller->action->id),
                [
                    'class' => 'btn btn-secondary'
                ]
            ) ?>
            <?= Html::submitButton(Yii::t('amoscore', 'Search'), [
                    'class' => 'btn btn-navigation-primary'
            ]) ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>
</div>
