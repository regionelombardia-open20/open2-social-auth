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

<div class="loginContainer">
    <div class="body col-xs-12">
        <h2 class="title-login"><?= Module::t('amossocialauth', 'spid_login_welcome') ?></h2>
        <h3 class="subtitle-login"><?= $subtitleSpidOrCnsString; ?></h3>
        <p class="text-center"><strong><?= Module::t('amossocialauth', 'spid_login_user_data') ?></strong></p>
        <p class="text-center"><?= Module::t('amossocialauth', 'spid_login_name') . ': ' . $userProfile->nome ?></p>
        <p class="text-center"><?= Module::t('amossocialauth', 'spid_login_surname') . ': ' . $userProfile->cognome ?></p>
        <p class="text-center"><?= Module::t('amossocialauth', 'spid_login_email') . ': ' . $userProfile->user->email ?></p>
    </div>
    <div class="col-lg-12 col-sm-12 col-xs-12 footer-link text-center">
        <?= Html::a(\Yii::t('amossocialauth', 'spid_login_confirm_btn'), ['/socialauth/shibboleth/endpoint', 'confirm' => true], ['class' => 'btn btn-administration-primary']); ?>
        <?= Html::a(\Yii::t('amossocialauth', 'spid_login_deny_btn'), '/', ['class' => 'btn btn-administration-primary']); ?>
    </div>
</div>
