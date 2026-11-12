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

$adminModuleName = \open20\amos\admin\AmosAdmin::getModuleName();
?>

<div class="loginContainerFullsize">
    <div class="login-block social-auth-spid ask-signup col-xs-12 nop">
        <div class="login-body">
            <h2 class="title-login"><?= Module::t('amossocialauth', 'spid_signup_welcome', ['nome' => $userDatas['nome'], 'cognome' => $userDatas['cognome']]) ?></h2>
            <h3 class="title-login"><?= Module::t('amossocialauth', 'Non è stata trovata nessuna utenza corrispondente al codice fiscale <strong>{cf}</strong>', ['cf' => $userDatas['codiceFiscale']]) ?></h3>
            <hr>
            <div class="action">
                <div>
                    <p><strong><?= Module::t('amossocialauth', 'Sei già registrato alla piattaforma?') ?></strong></p>
                    <p><?=  Module::t('amossocialauth', 'Clicca qui per riconciliare il tuo profilo') ?></p>
                    <?= Html::a(Module::t('amossocialauth', 'Riconcilia il tuo profilo'), ['/'.$adminModuleName.'/security/login', 'reconciliation' => true], ['class' => 'btn btn-administration-primary']); ?>
                </div>
                <div>
                    <p><strong><?= Module::t('amossocialauth', 'Sei un nuovo utente?') ?></strong></p>
                    <p><?= Module::t('amossocialauth', 'clicca qui per finalizzare la registrazione') ?></p>
                    <?= Html::a(Module::t('amossocialauth', 'spid_signup_register_btn'), ['/'.$adminModuleName.'/security/register', 'confirm' => true, 'from-shibboleth' => true], ['class' => 'btn btn-administration-primary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>