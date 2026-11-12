<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\socialauth
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

use yii\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\socialauth\models\SocialAuthUsers;

?>
<div class="social-auth-bar">
    <?php
    foreach ($providers as $providerName => $config) {
        $lowCaseName = strtolower($providerName);

        /**
         * @var $socialAccount SocialAuthUsers
         */
        $socialAccount = SocialAuthUsers::findOne([
            'provider' => $lowCaseName,
            'user_id' => Yii::$app->user->id
        ]);

        /**
         * Is this social linked
         */
        $socialLinked = (!$socialAccount || !$socialAccount->id);
        ?>
        <div class="social_checkws">
            <div class="header">
                <span style="float:left;">
                    <h4><?= $providerName ?></h4>
                </span>
                <div class="social-buttons-container">
                    <?php if(!$socialLinked) : ?>
                        <?= Html::a(
                                AmosIcons::show($lowCaseName),
                                '/socialauth/social-auth/link-user?provider=' . $lowCaseName, ['class' => 'btn btn-default']); ?>
                        <button type="button" class="btn btn-social collega-social" data-type="<?= $lowCaseName ?>">
                            <span class="fa fa-<?= $lowCaseName ?>"></span>
                            <span>
                                <?= \open20\amos\socialauth\Module::t('amossocialauth', 'Collega il tuo profilo') ?>
                            </span>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="floatclear"></div>
            </div>

            <?php if($socialLinked) : ?>
                <div class="media">
                    <div class="media-left">
                        <label>Foto</label>
                        <img src="https://lh4.googleusercontent.com/-97Zgh4BV3rk/AAAAAAAAAAI/AAAAAAAAAaY/N6HnrvhKCio/photo.jpg?sz=200" alt="immagine" width="80px">
                    </div>
                    <div class="media-body">
                        <div class="field-media">
                            <label>Nome</label>
                            <span>Damian</span>
                        </div>
                        <div class="field-media">
                            <label>Cognome</label>
                            <span>Gomez</span>
                        </div>
                        <div class="field-media">
                            <label>Email</label>
                            <span>damian.gomez@open20.it</span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="validatore_container checkws">
                    <br>
                    <?= \open20\amos\socialauth\Module::t('amossocialauth', 'Nessun profilo collegato.') ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    ?>
</div>
