<?php

use lispa\amos\socialauth\Module;
use \yii\helpers\Html;

/**
 * @var $userProfile UserProfile
 */
?>

<div class="loginContainerFullsize">
    <div class="login-block social-auth-spid ask-signup col-xs-12 nop">
        <div class="login-body">
            <h2 class="title-login"><?= Module::t('amossocialauth', 'spid_signup_welcome', ['nome' => $userDatas['nome'], 'cognome' => $userDatas['cognome']]) ?></h2>
            <h3 class="title-login"><?= Module::t('amossocialauth', 'spid_signup_subtitle', ['cf' => $userDatas['codiceFiscale'], 'email' => $userDatas['emailAddress']]) ?></h3>
            <hr>
            <div class="action">
                <div>
                    <p><strong><?= Module::t('amossocialauth', 'spid_signup_already_registered') ?></strong></p>
                    <p><?= Module::t('amossocialauth', 'spid_signup_already_registered2') ?></p>
                    <?= Html::a(\Yii::t('amossocialauth', 'spid_signup_already_registered_btn'), ['/admin/security/login', 'confirm' => true], ['class' => 'btn btn-administration-primary']); ?>
                </div>
                <div>
                    <p><strong><?= Module::t('amossocialauth', 'spid_signup_register') ?></strong></p>
                    <p><?= Module::t('amossocialauth', 'spid_signup_register2') ?></p>
                    <?= Html::a(\Yii::t('amossocialauth', 'spid_signup_register_btn'), ['/admin/security/register', 'confirm' => true], ['class' => 'btn btn-administration-primary']); ?>
                </div>
            </div>

        </div>
    </div>
</div>

