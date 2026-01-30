<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-social-auth/src/views
 */
/**
 * @var yii\web\View $this
 * @var open20\amos\socialauth\models\Oauth2Client $model
 */

$this->title = 'Crea';
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/socialauth']];
$this->params['breadcrumbs'][] = ['label' => 'Oauth2Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="oauth2-client-create">
    <?= $this->render('_form', [
        'model' => $model,
        'fid' => NULL,
        'dataField' => NULL,
        'dataEntity' => NULL,
    ]) ?>

</div>
