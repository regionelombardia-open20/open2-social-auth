<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\socialauth
 * @category   CategoryName
 */

use yii\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\socialauth\Module;
?>
<div class="social-auth-bar">
    <?php
    foreach ($providers as $providerName=>$config) {
        $lowCaseName = strtolower($providerName);

        echo Html::a(
                AmosIcons::show($lowCaseName) . Module::t('amosadmin', 'Connect'),
                '/socialauth/social-auth/link-user?provider=' . $lowCaseName,
                ['class' => 'btn btn-navigation-primary']);
    }
    ?>
</div>
