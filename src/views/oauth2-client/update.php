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

$this->title = 'Aggiorna';
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/socialauth']];
$this->params['breadcrumbs'][] = ['label' => 'Oauth2client', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => strip_tags($model), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="oauth2-client-update">

    <?= $this->render('_form', [
        'model' => $model,
        'fid' => NULL,
        'dataField' => NULL,
        'dataEntity' => NULL,
    ]) ?>

</div>
