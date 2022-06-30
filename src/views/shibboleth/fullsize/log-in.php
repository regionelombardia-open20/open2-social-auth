<?php

use open20\amos\admin\models\UserProfile;
use open20\amos\socialauth\Module;
use yii\helpers\Html;

/**
 * @var array $userDatas
 * @var $userProfile UserProfile
 * @var string $authType
 */

$subtitleSpidOrCnsString = Module::t('amossocialauth', 'spid_login_subtitle', ['email' => $userDatas['emailAddress']]);
if ($authType == 'IDPC_AUTHENTICATION_SMARTCARD') {
    $subtitleSpidOrCnsString = Module::t('amossocialauth', 'spid_login_subtitle_cns', ['email' => $userDatas['emailAddress']]);
} elseif (($authType == 'IDPC_SPID_L1') || ($authType == 'IDPC_SPID_L2') || ($authType == 'IDPC_SPID_L3')) {
    $subtitleSpidOrCnsString = Module::t('amossocialauth', 'spid_login_subtitle', ['email' => $userDatas['emailAddress']]);
}

?>

<div class="loginContainerFullsize">
    <div class="login-block social-auth-spid col-xs-12 nop">
        <div class="login-body">
            <h2 class="title-login"><?= Module::t('amossocialauth', 'spid_login_welcome') ?></h2>
            <h3 class="title-login"><?= $subtitleSpidOrCnsString; ?></h3>
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
