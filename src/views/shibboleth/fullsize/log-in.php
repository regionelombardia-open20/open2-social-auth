<?php

use lispa\amos\socialauth\Module;
use \yii\helpers\Html;

/**
 * @var $userProfile UserProfile
 */
?>

<div class="loginContainerFullsize">
    <div class="login-block social-auth-spid col-xs-12 nop">
        <div class="login-body">
            <h2 class="title-login"><?= Module::t('amossocialauth', 'spid_login_welcome') ?></h2>
            <h3 class="title-login"><?= Module::t('amossocialauth', 'spid_login_subtitle', ['email' => $userDatas['emailAddress']]) ?></h3>
            <p class="text-center"><strong><?= Module::t('amossocialauth', 'spid_login_user_data') ?></strong></p>
            <p class="text-center"><?= Module::t('amossocialauth', 'spid_login_name') . ': ' . $userProfile->nome ?></p>
            <p class="text-center"><?= Module::t('amossocialauth', 'spid_login_surname') . ': ' . $userProfile->cognome ?></p>
            <p class="text-center"><?= Module::t('amossocialauth', 'spid_login_email') . ': ' . $userProfile->user->email ?></p>
        </div>
        <div class="col-xs-12 action">
            <?= Html::a(\Yii::t('amossocialauth', 'spid_login_confirm_btn'), ['/socialauth/shibboleth/endpoint', 'confirm' => true], ['class' => 'btn btn-administration-primary']); ?>
            <?= Html::a(\Yii::t('amossocialauth', 'spid_login_deny_btn'), '/', ['class' => 'btn btn-administration-primary']); ?>
        </div>
    </div>
</div>
