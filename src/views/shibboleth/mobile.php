<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
$userProfile = json_encode($user->toArray([
    'id',
    'username',
    'email',
    'accessToken',
    'fcmToken',
    'slimProfile',
    'userImage',
]));
?>

<h1><?= Yii::t('socialauth', 'Caricamento....'); ?></h1>
<script type="text/javascript">
    if(typeof webViewCallback == 'function') {
        webViewCallback(<?= $userProfile; ?>);
    }
</script>