<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-social-auth/src/views
 */

use open20\amos\socialauth\models\Oauth2Client;
use open20\amos\socialauth\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;
use open20\amos\core\module\BaseAmosModule;

/**
 * @var yii\web\View $this
 * @var open20\amos\socialauth\models\Oauth2Client $model
 */

$this->title = $model->client_id;
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/socialauth']];
$this->params['breadcrumbs'][] = ['label' => 'Oauth2client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$authorizeUrl = Yii::$app->urlManager->createAbsoluteUrl([
    '/socialauth/oauth2/auth',
    'client_id' => $model->client_id,
    'response_type' => 'code',
    'access_type' => 'offline',
    'scope' => $model->scope,
    'redirect_uri' => $model->redirect_uri
]);

$accessButtonCss = '<style>
    @import url("https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;700&display=swap");
    #btnOpen20 {
        text-decoration: none;
        background-color: #2f7e14;
        color: #FFF;
        font-size: 18px;
        display: inline-block;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: bold;
        font-family: "Titillium Web", sans-serif;
    }
    #btnOpen20:hover,#btnOpen20:focus {
        box-shadow: 0 0 3px 3px rgb(60 195 37 / 30%);
    }
    #btnOpen20 span {
        display: block;
        font-size: 12px;
        font-weight: normal;
        opacity: .7;
    }
</style>';

$accessButton = $accessButtonCss . PHP_EOL . Html::a(Module::t('amossocialauth', 'Accedi con {platformName} <span>Powered by Open 2.0</span>', [
        'platformName' => Yii::$app->name
    ]),
    $authorizeUrl,
    [
        'id' => 'btnOpen20',
        'title' => Module::t('amossocialauth', 'Accedi con {platformName}', [
            'platformName' => Yii::$app->name
        ]),
    ]
);


$jsCopyText = <<<JS
    // Copy text inside the modal to the clipboard
    var copyBtn = $('.copy-btn');
    var textToCopy = '';
    copyBtn.on('click', function (e) {
        if ($(this).attr('id') === 'copy-access-btn') {
            textToCopy = $('.preview .access-button-code .code-block').text();
        } else if ($(this).attr('id') === 'copy-link-authorize-btn') {
            textToCopy = $('.preview .authorize-container .url-block').text();
        } else if ($(this).attr('id') === 'copy-link-token-btn') {
            textToCopy = $('.preview .token-container .url-block').text();
        } else if ($(this).attr('id') === 'copy-link-userinfo-btn') {
            textToCopy = $('.preview .userinfo-container .url-block').text();
        } else if ($(this).attr('id') === 'copy-client-id-btn') {
            textToCopy = $('#clientIdContainer').text();
        } else if ($(this).attr('id') === 'copy-client-secret-btn') {
            textToCopy = $('#clientSecretContainer').text();
        } else if ($(this).attr('id') === 'copy-redirect-uri-btn') {
            textToCopy = $('#redirectUriContainer').text();
        } else if ($(this).attr('id') === 'copy-domain-btn') {
            textToCopy = $('#domainContainer').text();
        }
        copyToClipboard(textToCopy, $(this));
        $(this).prev().show().fadeOut(1000);
        $(this).tooltip('hide');
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
    .access-button-preview .button-container {
        border: solid 1px #CCC;
        border-radius: 5px;
        padding: 25px;
    }

    .example-container {
        padding: 1.5rem;
        margin-top: 1rem;
        border: solid #f3f3f5;
        border-width: 4px;
        border-radius: 4px;
    }

</style>


<div class="oauth2-client-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'client_id',
                'value' => function ($model) {
                    /** @var Oauth2Client $model */
                    $copySuccess = '<span class="m-r-10 copy-success" style="display: none">
                        <small class="text-uppercase"><strong>' . Module::t('amossocialauth', 'Copiato') . '</strong></small>
                    </span>';
                    $copyButton = Html::button('<span class="mdi mdi-content-copy"></span>', [
                        'title' => Module::t('amoscore', 'Copia'),
                        'id' => 'copy-client-id-btn',
                        'class' => 'btn btn-outline-secondary copy-btn',
                        'data-toggle' => 'tooltip'
                    ]);

                    return '<span id="clientIdContainer">' . $model->client_id . '</span>' . '<span class="pull-right">' . $copySuccess . $copyButton . '</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'client_secret',
                'value' => function ($model) {
                    /** @var Oauth2Client $model */
                    $copySuccess = '<span class="m-r-10 copy-success" style="display: none">
                            <small class="text-uppercase"><strong>' . Module::t('amossocialauth', 'Copiato') . '</strong></small>
                        </span>';
                    $copyButton = Html::button('<span class="mdi mdi-content-copy"></span>', [
                        'title' => Module::t('amoscore', 'Copia'),
                        'id' => 'copy-client-secret-btn',
                        'class' => 'btn btn-outline-secondary copy-btn',
                        'data-toggle' => 'tooltip'
                    ]);

                    return '<span id="clientSecretContainer">' . $model->client_secret . '</span>' . '<span class="pull-right">' . $copySuccess . $copyButton . '</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'redirect_uri',
                'value' => function ($model) {
                    /** @var Oauth2Client $model */
                    $copySuccess = '<span class="m-r-10 copy-success" style="display: none">
                            <small class="text-uppercase"><strong>' . Module::t('amossocialauth', 'Copiato') . '</strong></small>
                        </span>';
                    $copyButton = Html::button('<span class="mdi mdi-content-copy"></span>', [
                        'title' => Module::t('amoscore', 'Copia'),
                        'id' => 'copy-redirect-uri-btn',
                        'class' => 'btn btn-outline-secondary copy-btn',
                        'data-toggle' => 'tooltip'
                    ]);

                    return '<span id="redirectUriContainer">' . $model->redirect_uri . '</span>' . '<span class="pull-right">' . $copySuccess . $copyButton . '</span>';
                },
                'format' => 'raw'
            ],
            [
                'label' => Module::t('amossocialauth', 'Domain'),
                'value' => function ($model) {
                    /** @var Oauth2Client $model */
                    $copySuccess = '<span class="m-r-10 copy-success" style="display: none">
                            <small class="text-uppercase"><strong>' . Module::t('amossocialauth', 'Copiato') . '</strong></small>
                        </span>';
                    $copyButton = Html::button('<span class="mdi mdi-content-copy"></span>', [
                        'title' => Module::t('amoscore', 'Copia'),
                        'id' => 'copy-domain-btn',
                        'class' => 'btn btn-outline-secondary copy-btn',
                        'data-toggle' => 'tooltip'
                    ]);
                    $domain = parse_url($model->redirect_uri, PHP_URL_HOST);

                    return '<span id="domainContainer">' . $domain . '</span>' . '<span class="pull-right">' . $copySuccess . $copyButton . '</span>';
                },
                'format' => 'raw'
            ],
            //            'grant_type',
            //            'scope',
        ],
    ]) ?>


    <div class="preview m-t-30">

        <hr class="m-b-30" style="border-top: 1px solid #CCC">

        <!-- Access button -->
        <div class="authorize-container m-b-20">

            <div class="access-button-preview m-b-15">
                <div class="row">
                    <div class="col-md-12">
                        <h4><?= Module::t('amossocialauth', 'Anteprima pulsante di accesso:') ?></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="example-container">
                            <?= $accessButton ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="access-button-code">
                <div class="row">
                    <div class="col-md-6">
                        <h4><?= Module::t('amossocialauth', 'Codice pulsante di accesso:') ?></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="m-r-10 copy-success" style="display: none">
                            <small class="text-uppercase"><strong><?= Module::t('amossocialauth', 'Copiato') ?></strong></small>
                        </span>
                        <?= Html::button('<span class="mdi mdi-content-copy"></span>', [
                            'title' => Module::t('amoscore', 'Copia'),
                            'id' => 'copy-access-btn',
                            'class' => 'btn btn-outline-secondary copy-btn',
                            'data-toggle' => 'tooltip'
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <pre><code class="code-block"><?= Html::encode($accessButton) ?></code></pre>
                    </div>
                </div>
            </div>

        </div>

        <hr class="m-b-30" style="border-top: 1px solid #CCC">

        <!-- Authorize -->
        <div class="authorize-container m-b-20">
            <div class="row">
                <div class="col-md-6">
                    <h4><?= Module::t('amossocialauth', 'Codice chiamata:') ?></h4>
                </div>
                <div class="col-md-6 text-right">
                    <span class="m-r-10 copy-success" style="display: none">
                        <small class="text-uppercase"><strong><?= Module::t('amossocialauth', 'Copiato') ?></strong></small>
                    </span>
                    <?= Html::button('<span class="mdi mdi-content-copy"></span>', [
                        'title' => Module::t('amoscore', 'Copia'),
                        'id' => 'copy-link-authorize-btn',
                        'class' => 'btn btn-outline-secondary copy-btn',
                        'data-toggle' => 'tooltip'
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <pre><code class="url-block"><?= $authorizeUrl ?></code></pre>
                </div>
            </div>
        </div>

        <hr class="m-b-30" style="border-top: 1px solid #CCC">

    </div>

</div>

<div id="form-actions" class="bk-btnFormContainer pull-right">
    <?= Html::a(BaseAmosModule::t('amoscore', 'Indietro'), '/socialauth/oauth2-client', [
        'class' => 'btn btn-secondary'
    ]); ?>
</div>