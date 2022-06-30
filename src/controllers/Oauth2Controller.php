<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\socialauth
 * @category   CategoryName
 */

namespace open20\amos\socialauth\controllers;

use conquer\oauth2\models\Client;
use open20\amos\admin\models\LoginForm;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\socialauth\models\search\ClientSearch;
use open20\amos\socialauth\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

class Oauth2Controller extends CrudController
{
    /**
     * @var string $layout
     */
    public $layout = 'login';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'token',
                            'back',
                            'auth',
                            'userinfo',
                            'index',
                        ],
                        //'roles' => ['*']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'manage',
                        ],
                        'roles' => ['ADMIN']
                    ]
                ],
            ],
            'oauth2Auth' => [
                'class' => \conquer\oauth2\AuthorizeFilter::className(),
                'only' => ['auth'],
                'allowImplicit' => false
            ],
            'tokenAuth' => [
                'class' => \conquer\oauth2\TokenAuth::className(),
                'only' => ['userinfo'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setModelObj(new Client());
        $this->setModelSearch(new ClientSearch());

        $this->setAvailableViews(
            [
                'grid' => [
                    'name' => 'grid',
                    'label' => Module::t(
                        'amosdiscussioni',
                        '{iconaTabella}' . Html::tag('p', Module::t('amosdiscussioni', 'Table')),
                        [
                            'iconaTabella' => AmosIcons::show('view-list-alt')
                        ]
                    ),
                    'url' => '?currentView=grid'
                ],
            ]
        );

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    public function beforeAction($action)
    {
        if ($action->id == 'userinfo') {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            /**
             * Returns an access token.
             */
            'token' => [
                'class' => \conquer\oauth2\TokenAction::classname(),
            ],
            /**
             * OPTIONAL
             * Third party oauth providers also can be used.
             */
            'back' => [
                'class' => \yii\authclient\AuthAction::className(),
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    public function actionManage($layout = null)
    {
        $this->setUpLayout('list');

        //se il layout di default non dovesse andar bene si può aggiuntere il layout desiderato
        //in questo modo nel controller "return parent::actionIndex($this->layout);"
        if ($layout) {
            $this->setUpLayout($layout);
        }

        return $this->render(
            'index',
            [
                'dataProvider' => $this->getDataProvider(),
                'model' => $this->getModelSearch(),
                'currentView' => $this->getCurrentView(),
                'availableViews' => $this->getAvailableViews(),
                'url' => ($this->url) ? $this->url : null,
                'parametro' => ($this->parametro) ? $this->parametro : null
            ]
        );
    }

    /**
     * Display login form, signup or something else.
     * AuthClients such as Google also may be used
     */
    public function actionAuth()
    {
        $model = new LoginForm();

        if (Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->login()) {
            if ($this->isOauthRequest) {
                $this->finishAuthorization();
            } else {
                return $this->goBack();
            }
        } else {
            return $this->render(
                'login',
                [
                    'model' => $model,
                ]
            );
        }
    }

    public function actionUserinfo($access_token)
    {
        $userProfile = UserProfile::findOne(Yii::$app->user->id);

        return [
                'sub' => $userProfile->id,
                'given_name' => $userProfile->user->username,
                'family_name' => $userProfile->cognome,
                'name' => $userProfile->nome,
                'picture' => $userProfile ? $userProfile->getAvatarWebUrl() : null,
                'profile' => '',
                'gender' => $userProfile->sesso,
                'locale' => $userProfile->language,
                'email' => $userProfile->user->email
            ];
    }
}
