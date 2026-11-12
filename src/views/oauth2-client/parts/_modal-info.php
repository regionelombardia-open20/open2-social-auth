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


$platformUrl = Yii::$app->params['platform']['backendUrl'];


$jsCopyText = <<<JS
    // Copy text inside the modal to the clipboard
    $('#copy-link-btn').on('click', function (e) {
        var textToCopy = $('#oauth2ModalInfo .modal-body').text();
        copyToClipboard(textToCopy);
        $(".copy-success").show().fadeOut(1000);
    });

    /**
    * Copy text to clipboard
    * @param text
    */
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(function () {
                console.log('Text copied to clipboard');
            })
            .catch(function (err) {
                console.error('Could not copy text: ', err);
            });
    }
JS;

$this->registerJs($jsCopyText);

?>

<style>
    #oauth2ModalInfo .copy-button-container {
        float: right;
    }
</style>


<!-- Modal -->
<div class="modal fade" id="oauth2ModalInfo" tabindex="-1" role="dialog" aria-labelledby="oauth2ModalInfo"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="oauth2ModalInfoLabel"><?= Module::t('amossocialauth', 'Funzionamento API Federazione SSO') ?></h5>
            </div>
            <div class="modal-body">
                <div class="row m-b-15">
                    <div class="col-md-12">
                        <div class="copy-button-container">
                            <span class="m-r-5 copy-success" style="display: none">
                                <small class="text-uppercase"><strong><?= Module::t('amossocialauth', 'Copiato') ?></strong></small>
                            </span>

                            <?= Html::button('<span class="mdi mdi-content-copy">', [
                                'title' => Module::t('amoscore', 'Copia funzionamento API Federazione SSO'),
                                'id' => 'copy-link-btn',
                                'class' => 'btn btn-default',
                            ]); ?>
                        </div>
                        <h4>1. Authorize</h4>
                        <h5><?= $platformUrl ?>/socialauth/oauth2/auth</h5>
                        <p>Questa chiamata serve per autenticare l'utente sulla piattaforma Open2.0 e ottenere un codice
                            di autorizzazione.</p>
                        <h6>Metodo: GET</h6>
                        <h6>Parametri:</h6>
                        <ul>
                            <li><code>client_id</code> = [CAMPO_CLIENT_ID]</li>
                            <li><code>redirect_uri</code> = [CAMPO_REDIRECT_URI]</li>
                            <li><code>response_type</code> = code</li>
                            <li><code>access_type</code> = offline</li>
                            <li><code>scope</code> = userinfo.profile</li>
                        </ul>
                        <h6>Response:</h6>
                        <p>La risposta a questa chiamata sarà un codice di autorizzazione.</p>
                        <p>Esempio:</p>
                        <code>code</code> = mEIxCCC3JasetgkJ7iFdyOvGEguH31Zg-QkU_j0x
                    </div>
                </div>

                <hr style="border-top: 1px solid #CCC">

                <div class="row m-b-15">
                    <div class="col-md-12">
                        <h4>2. Token</h4>
                        <h5><?= $platformUrl ?>/socialauth/oauth2/token</h5>
                        <p>Questa chiamata utilizza il codice di autorizzazione ottenuto dalla chiamata precedente per
                            ottenere un token di accesso.</p>
                        <h6>Metodo: GET</h6>
                        <h6>Parametri:</h6>
                        <ul>
                            <li><code>code</code> = [RESPONSE_CODE_CHIAMATA_AUTHORIZE]</li>
                            <li><code>grant_type</code> = authorization_code</li>
                            <li><code>client_id</code> = helpdesk_client_001</li>
                        </ul>
                        <h6>Response:</h6>
                        <p>La risposta a questa chiamata sarà un token di accesso.</p>
                        <p>Esempio:</p>
                        <code>access_token</code> = b5GcJl_5apAAErgbFhC_2fqCXd4ISke4B5tCZTXV
                    </div>
                </div>

                <hr style="border-top: 1px solid #CCC">

                <div class="row">
                    <div class="col-md-12">
                        <h4>3. User Info</h4>
                        <h5><?= $platformUrl ?>/socialauth/oauth2/userinfo</h5>
                        <p>Questa chiamata utilizza il token di accesso per ottenere le informazioni sull'utente
                            autenticato.</p>
                        <h6>Metodo: GET</h6>
                        <h6>Parametri:</h6>
                        <ul>
                            <li><code>access_token</code> = [RESPONSE_ACCESS_TOKEN_CHIAMATA_TOKEN]</li>
                        </ul>
                        <h6>Response:</h6>
                        <p>La risposta a questa chiamata sarà un oggetto JSON con le informazioni sull'utente
                            autenticato.</p>
                        <p>Esempio:</p>
                        <pre>{
    "cognome": "Rossi",
    "nome": "Mario",
    "email": "mario.rossi@mariorossi.com"
}</pre>
                    </div>
                </div>
            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" title="<?= Module::t('amoscore', 'Chiudi') ?>"
                        data-dismiss="modal">
                    <?= Module::t('amoscore', 'Chiudi') ?>
                </button>

            </div>

        </div>
    </div>
</div>