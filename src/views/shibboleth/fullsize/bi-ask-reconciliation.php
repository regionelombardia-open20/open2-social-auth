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

use open20\amos\admin\AmosAdmin;
use open20\amos\socialauth\Module;
use yii\helpers\Html;

/**
 * @var array $userDatas
 * @var string $authType
 * @var array $registerLink
 * @var array $loginLink
 */

/** @var AmosAdmin $adminModule */
$adminModule = AmosAdmin::getInstance();
$adminModuleName = \open20\amos\admin\AmosAdmin::getModuleName();


$loginWithSpidOrCnsString = Module::t('amossocialauth', 'spid_signup_already_registered2');
if ($authType == 'IDPC_AUTHENTICATION_SMARTCARD') {
    $loginWithSpidOrCnsString = Module::t('amossocialauth', 'spid_signup_already_registered3');
} elseif (($authType == 'IDPC_SPID_L1') || ($authType == 'IDPC_SPID_L2') || ($authType == 'IDPC_SPID_L3')) {
    $loginWithSpidOrCnsString = Module::t('amossocialauth', 'spid_signup_already_registered2');
}

/** @var Module $socialAuthModule */
$socialAuthModule = Module::instance();
$spidSignupSubtitle = (($socialAuthModule->checkOnlyFiscalCode === false) ?
    Module::t('amossocialauth', 'spid_signup_subtitle', ['cf' => $userDatas['codiceFiscale'], 'email' => $userDatas['emailAddress']]) :
    Module::t('amossocialauth', 'spid_signup_subtitle_only_cf', ['cf' => $userDatas['codiceFiscale']])
);

$registerLink = [\open20\amos\socialauth\utility\SocialAuthUtility::getRegisterLink(), 'confirm' => true, 'from-shibboleth' => true];
$loginLink = [\open20\amos\socialauth\utility\SocialAuthUtility::getLoginLink(), 'confirm' => true, 'reconciliation' => true];
?>

<div class="m-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-md text-center text-md-left px-5 pb-5">
                <h2 class=""><?= Module::t('amossocialauth', 'spid_signup_welcome', ['nome' => $userDatas['nome'], 'cognome' => $userDatas['cognome']]) ?></h2>
                <h4 class="title-login"><?= Module::t('amossocialauth', "Grazie di aver effettuato l'accesso con la tua identità digitale. Se hai un’utenza già registrata su {appName}, clicca per riconciliare il tuo profilo esistente con l'Identità Digitale. Se invece sei un nuovo utente, finalizza la registrazione", ['cf' => $userDatas['codiceFiscale'], 'appName' => Yii::$app->name]) ?></h4>            </div>
            <div class="col-md text-center text-md-left px-5">
                <div>
                    <p><strong><?= Module::t('amossocialauth', 'Sei già registrato alla piattaforma?') ?></strong></p>
                    <p><?=  Module::t('amossocialauth', 'Clicca qui per riconciliare il tuo profilo') ?></p>
                    <?= Html::a(Module::t('amossocialauth', 'Riconcilia il tuo profilo'), $loginLink, ['class' => 'btn btn-icon rounded-0 btn-primary text-uppercase']); ?>
                </div>
                <div class="mt-4">
                    <p class="py-3" ><strong><?= Module::t('amossocialauth', 'Sei un nuovo utente?') ?></strong></p>
                    <p><?= Module::t('amossocialauth', 'Clicca qui per finalizzare la registrazione') ?></p>
                    <hr>
                    <?= Html::a(Module::t('amossocialauth', 'spid_signup_register_btn'), $registerLink, ['class' => 'btn btn-icon rounded-0 btn-primary text-uppercase']); ?>
                </div>
            </div>
        </div>
    </div>
</div>
