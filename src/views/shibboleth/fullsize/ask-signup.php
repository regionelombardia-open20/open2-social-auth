<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */

use open20\amos\socialauth\Module;
use yii\helpers\Html;

/**
 * @var array $userDatas
 * @var string $authType
 */

$loginWithSpidOrCnsString = Module::t('amossocialauth', 'spid_signup_already_registered2');
if ($authType == 'IDPC_AUTHENTICATION_SMARTCARD') {
    $loginWithSpidOrCnsString = Module::t('amossocialauth', 'spid_signup_already_registered3');
} elseif (($authType == 'IDPC_SPID_L1') || ($authType == 'IDPC_SPID_L2') || ($authType == 'IDPC_SPID_L3')) {
    $loginWithSpidOrCnsString = Module::t('amossocialauth', 'spid_signup_already_registered2');
}

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
                    <p><?= $loginWithSpidOrCnsString ?></p>
                    <?= Html::a(\Yii::t('amossocialauth', 'spid_signup_already_registered_btn'), ['/admin/security/login', 'confirm' => true], ['class' => 'btn btn-administration-primary']); ?>
                </div>
                <div>
                    <p><strong><?= Module::t('amossocialauth', 'spid_signup_register') ?></strong></p>
                    <p><?= Module::t('amossocialauth', 'spid_signup_register2') ?></p>
                    <?= Html::a(\Yii::t('amossocialauth', 'spid_signup_register_btn'), ['/admin/security/register', 'confirm' => true, 'from-shibboleth' => true], ['class' => 'btn btn-administration-primary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>
