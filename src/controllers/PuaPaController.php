<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\core
 * @category   CategoryName
 */

namespace open20\amos\socialauth\controllers;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\controllers\BackendController;
use open20\amos\core\helpers\Html;
use open20\amos\core\user\User;
use open20\amos\socialauth\models\SocialIdmUser;
use open20\amos\socialauth\Module;
use open20\amos\socialauth\utility\SocialAuthUtility;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\authclient\OpenIdConnect;
use yii\log\Logger;

class PuaPaController extends BackendController {

    /**
     * @var string $layout
     */
    public $layout = 'login';

    const LOGGED_WITH_OPEN_ID_CONNECT = 'OpenIdConnect';
    const LOGGED_WITH_OPEN_ID_CONNECT_NONCE = 'OpenIdConnect';
    const LOGGED_WITH_OPEN_ID_CONNECT_REDIRECT = 'OpenIdConnect-redirect';
    const LOGGED_WITH_OPEN_ID_CONNECT_ID_TOKEN = 'OpenIdConnect-id_token';

    /**
     *
     * @var bool $useFrontendUrl
     */
    public $useFrontendUrl = true;
    public static $client;

    /**
     *
     * @var array $config
     */
    public $config = [
        'name' => 'Pua',
        'title' => 'Pua Login',
        'issuerUrl' => 'https://sso-coll.dfp.gov.it',
        'authUrl' => 'https://sso-coll.dfp.gov.it/connect/authorize',
        'apiBaseUrl' => 'https://sso-coll.dfp.gov.it',
        'scope' => 'openid profile',
        'tokenUrl' => 'https://sso-coll.dfp.gov.it/connect/token',
    ];

    public function init() {
        parent::init();

        $module = Module::getInstance();
        if ($module->enablePuaLogin) {
            $this->config = \yii\helpers\ArrayHelper::merge($this->config, Module::instance()->puaConfiguration);
            if (empty($this->config['clientId']) || empty($this->config['clientSecret'])) {
                throw new \yii\base\InvalidConfigException(\Yii::t(
                                        'amosapp', "Impossibile utilizzare il PUA senza impostare clientId e clientSecret."
                ));
            }
            if ($module->puaUseFrontendUrl == false) {
                $this->useFrontendUrl = $module->puaUseFrontendUrl;
            }
        } else
            return $this->goHome();
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'connect',
                            'login',
                        ],
                    //'roles' => ['*']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'logout',
                        ],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    public function actionConnect($redir = null) {
        try {
            if (!$this->isGuestUser()) {
                return $this->goHome();
            }

            $client = $this->getClient();
            $baseUrl = $this->getBaseUrlPlatform();
            $stringNonce = bin2hex(openssl_random_pseudo_bytes(6));         
            $client->setValidateAuthNonce(bin2hex(openssl_random_pseudo_bytes(6)));
            
            $url = $client->buildAuthUrl(['client_id' => $this->config['clientId'], 'redirect_uri' => $baseUrl . '/socialauth/pua-pa/login']);
           
            $state = $this->getStateFromUrl($url);
            $nonce = $this->getNonceFromUrl($url);

            $session = \Yii::$app->session;
            $session->set(self::LOGGED_WITH_OPEN_ID_CONNECT, $state);
            $session->set(self::LOGGED_WITH_OPEN_ID_CONNECT_NONCE, $nonce);
            if (!empty($redir)) {
                $session->set(self::LOGGED_WITH_OPEN_ID_CONNECT_REDIRECT, $redir);
            }

            return Yii::$app->getResponse()->redirect($url);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
    }

    public function actionLogin($code, $state, $nonce = null) {
        $socialModule = Module::getInstance();

        try {

            if (!$this->isGuestUser()) {
                return $this->goHome();
            }

            $client = $this->getClient();
            if ($socialModule->useBasicAuthPua == true) {
                $request = $client->createRequest()
                        ->setMethod('POST')
                        ->setUrl('connect/token')
                        ->addHeaders(['Authorization' => 'Basic ' . \yii\helpers\BaseStringHelper::base64UrlEncode($client->clientId . ":" . $client->clientSecret)])
                        ->addHeaders(['content-type' => 'application/x-www-form-urlencoded'])
                        ->setData([
                    //                'client_id' => $client->clientId,
                    //                'client_secret' => $client->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->getBaseUrlPlatform() . '/socialauth/pua-pa/login',
                ]);
            } else {
                $request = $client->createRequest()
                        ->setMethod('POST')
                        ->setUrl('connect/token')
                        ->addHeaders(['content-type' => 'application/x-www-form-urlencoded'])
                        ->setData([
                    'client_id' => $client->clientId,
                    'client_secret' => $client->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->getBaseUrlPlatform() . '/socialauth/pua-pa/login',
                ]);
            }

            $response = $request->send();

            $session = \Yii::$app->session;

            $state = $session->get(self::LOGGED_WITH_OPEN_ID_CONNECT);
            $nonce = $session->get(self::LOGGED_WITH_OPEN_ID_CONNECT_NONCE);
            $redir = $session->get(self::LOGGED_WITH_OPEN_ID_CONNECT_REDIRECT);

            $content = json_decode($response->content);

            $session->set(self::LOGGED_WITH_OPEN_ID_CONNECT_ID_TOKEN, $content->id_token);

            $requestUser = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('connect/userinfo')
                    ->addHeaders(['Authorization' => $content->token_type . ' ' . $content->access_token])
                    ->addHeaders(['content-type' => 'application/x-www-form-urlencoded']);

            $userinfo = $requestUser->send();

            $user = false;
            $data = [];
     
            if ($userinfo->isOk && !empty($userinfo->content)) {
                $data = json_decode($userinfo->content);
                $user = $this->setUser($data);
            }
            if ($user && !empty($data)) {
                $loginTimeout = \Yii::$app->params['loginTimeout'] ?: 3600;

                $signIn = \Yii::$app->user->login($user, $loginTimeout);
                if ($signIn) {
                    if (!empty($data->nickname)) {
                        SocialAuthUtility::updateFiscalCode($user->id, $data->nickname);
                    }
                    if (!empty($redir)) {
                        return $this->redirect([$redir]);
                    }
                    return $this->goHome();
                }
            }
            if ($socialModule->enableRegister) {
                \Yii::$app->getSession()->addFlash('danger', Module::t('amossocialauth', 'Login Failed'));
            }
            return $this->redirect([SocialAuthUtility::getLoginLink()]);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
    }

    /**
     *
     * @param stdClass $data
     * @return null|open20\amos\core\user\User
     */
    protected function setUser($data) {
        $socialModule = Module::getInstance();

        try {
            $n = 0; 
            if (!empty($data->nickname)) {
                $userByCF = UserProfile::find()->andWhere(['codice_fiscale' => $data->nickname]);
                if ($userByCF->count() == 1) {
                    $n = 1;
                    $user = $userByCF->one()->user;
                    if ($this->isUserDisabled($user->id)) {
                        return null;
                    } else {
                        $this->setIdmUser($data, $user);
                        return $user;
                    }
                }
                if ($n == 0) {
                    if (!$socialModule->enableRegister) {
                        Yii::$app->session->addFlash(
                                'danger', Module::t('amossocialauth', 'Unable to register, user creation disabled')
                        );

                        return null;
                    }
                    /** @var AmosAdmin $adminModule */
                    $adminModule = AmosAdmin::getInstance();

                    $newUser = $adminModule->createNewAccount(
                            $data->given_name, $data->family_name, $data->email, true
                    );
                    if (!$newUser || isset($newUser['error'])) {
                        Yii::$app->session->addFlash(
                                'danger', Module::t('amossocialauth', 'Unable to register, user creation error')
                        );
                    }
                    if (!empty($newUser['user'])) {
                        $user = $newUser['user'];

                        $this->setIdmUser($data, $user);

                        return $user;
                    }
                }
            }
            return null;
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
            return null;
        }
    }

    /**
     * 
     * @param stdClass $data
     * @param open20\amos\core\user\User $user
     */
    protected function setIdmUser($data, $user) {
        try {

            $idmUser = SocialIdmUser::find()->andWhere(['user_id' => $user->id])->one();
            if (empty($idmUser)) {
                $idmUser = new SocialIdmUser();
                $idmUser->user_id = $user->id;
                $idmUser->numeroMatricola = $data->sub;
                $idmUser->codiceFiscale = $data->nickname;
                $idmUser->nome = $data->given_name;
                $idmUser->cognome = $data->family_name;
                $idmUser->emailAddress = $data->email;                
                $idmUser->rawData = json_encode((array) $data);
                $idmUser->accessMethod = 'PUA-PA';
                $idmUser->save(false);
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param $user
     * @return bool
     */
    public function isUserDisabled($user_id) {
        $user = User::findOne($user_id);
        if ($user) {
            if ($user->status == User::STATUS_DELETED || !$user->userProfile->attivo) {
                \Yii::$app->getSession()->addFlash('danger', Module::t('amossocialauth', 'User is disabled'));
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param string $url
     * @return string
     */
    protected function getStateFromUrl($url) {
        $state = null;
        try {

            $newQuery = [];
            $queryParams = explode('&', (parse_url($url)['query']));
            foreach ($queryParams as $v) {
                $nq = explode('=', $v);
                $newQuery[$nq[0]] = $nq[1];
            }
            $state = $newQuery['state'];
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
        return $state;
    }

    /**
     * 
     * @param string $url
     * @return string
     */
    protected function getNonceFromUrl($url) {
        $nonce = null;
        try {

            $newQuery = [];
            $queryParams = explode('&', (parse_url($url)['query']));
            foreach ($queryParams as $v) {
                $nq = explode('=', $v);
                $newQuery[$nq[0]] = $nq[1];
            }
            $nonce = $newQuery['nonce'];
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
        return $nonce;
    }

    /**
     *
     * @return yii\authclient\OpenIdConnect $client
     */
    public function getClient() {
        if (empty(self::$client)) {
            self::$client = Yii::$app->authClientCollection->getClient('pua');
            self::$client->clientId = $this->config['clientId'];
            self::$client->clientSecret = $this->config['clientSecret'];
        }
        return self::$client;
    }

    /**
     * 
     * @return string
     */
    protected function getBaseUrlPlatform() {
        $baseUrl = Yii::$app->params['platform']['frontendUrl'];
        if ($this->useFrontendUrl == false) {
            $baseUrl = Yii::$app->params['platform']['backendUrl'];
        }
        return $baseUrl;
    }

    /**
     * Controlli su utente guest - se possibile utilizzare CurrentUser::isPlatformGuest()
     */
    private function isGuestUser() {
        $isGuestUser = Yii::$app->user->isGuest;

        // baso il controllo sull'usare CurrentUser::isPlatformGuest sul paramtro
        if (isset(Yii::$app->params['platformConfigurations']) && isset(Yii::$app->params['platformConfigurations']['guestUserId'])) {
            $isGuestUser = \open20\amos\core\utilities\CurrentUser::isPlatformGuest();
        }

        return $isGuestUser;
    }

    public function actionLogout($redir = null) {
        try {

            $client = $this->getClient();

            $session = \Yii::$app->session;

            $id_token = $session->get(self::LOGGED_WITH_OPEN_ID_CONNECT_ID_TOKEN);
            $state = $session->get(self::LOGGED_WITH_OPEN_ID_CONNECT);
            $nonce = $session->get(self::LOGGED_WITH_OPEN_ID_CONNECT_NONCE);

            $request = $client->createRequest()
                    ->setMethod('GET')
                    ->setUrl('session/end')
                    ->addHeaders(['content-type' => 'application/x-www-form-urlencoded'])
                    ->setData([
                'id_token_hint' => $id_token,
                'post_logout_redirect_uri' => (empty($redir) ? $this->getBaseUrlPlatform() : $this->getBaseUrlPlatform() . $redir),
                'state' => $state
            ]);

            $response = $request->send();
            if ($response->isOk) {
                $headers = $response->getHeaders()->toArray();
                return \Yii::$app->getResponse()->redirect($client->apiBaseUrl . $headers['location'][0]);
            }
            return $this->goHome();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
    }

}
