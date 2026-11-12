<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-social-auth
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

use amos\userauth\frontend\Module;
use open20\amos\admin\AmosAdmin;
use open20\design\assets\BootstrapItaliaDesignAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\bootstrap\ActiveForm $form
 * @var open20\amos\admin\models\RegisterForm $model
 */

$theModule = Module::instance();

$referrer = \Yii::$app->request->referrer;
if ((strpos($referrer, 'javascript') !== false) || (strpos($referrer, \Yii::$app->params['backendUrl']) == false)) {
    $referrer = null;
}

//$currentAsset = BootstrapItaliaDesignAsset::register($this);

?>
<div class="container py-4 my-5">
    <div class="row nop">
        <div class="col-md-6 mx-auto">
            <h2><?= Module::t('Registrati inserendo i tuoi dati') ?></h2>
            <div class="form-rounded">

                <?php
                $form = ActiveForm::begin(
                    [
                        'options' =>
                            [
                                'class' => 'userauth-credential-request-form needs-validation form-rounded'
                            ]
                    ]
                )
                ?>

                <?= $form->field($model, 'nome')->textInput(['readonly' => (!empty($model->nome))]); ?>

                <?= $form->field($model, 'cognome')->textInput(['readonly' => (!empty($model->cognome))]); ?>

                <?= $form->field($model, 'email')->textInput(); ?>

                <div class="col-xs-12"><?= $form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha::className())->label('') ?></div>
                <div class="col-xs-12 text-bottom">
                    <div>

                        <?=
                        Html::a(AmosAdmin::t('amosadmin', '#cookie_policy_message'), '/site/privacy',
                            ['title' => AmosAdmin::t('amosadmin', '#cookie_policy_title'), 'target' => '_blank'])
                        ?>
                        <?= Html::tag('p', AmosAdmin::t('amosadmin', '#cookie_policy_content')) ?>
                        <?= $form->field($model, 'privacy')->radioList([
                            1 => AmosAdmin::t('amosadmin', '#cookie_policy_ok'),
                            0 => AmosAdmin::t('amosadmin', '#cookie_policy_not_ok')
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <?= Html::a(Module::t('Annulla'),
                    (strip_tags($referrer) ?: ['/login']),
                    ['class' => 'btn btn-outline-primary pull-left',
                        'title' => Module::t('#go_to_login_title')])
                ?>
                <?= Html::submitButton(Module::t('#reset_pwd_send'),
                    ['class' => 'btn btn-primary btn-administration-primary pull-right',
                        'name' => 'login-button',
                        'title' => Module::t('#forgot_pwd_send_title')])
                ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>



