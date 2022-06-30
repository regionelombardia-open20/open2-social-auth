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

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\attachments\components\FileImport;
use open20\amos\core\controllers\BackendController;
use open20\amos\core\user\User;
use open20\amos\mobile\bridge\modules\v1\models\AccessTokens;
use open20\amos\socialauth\models\SocialAuthUsers;
use open20\amos\socialauth\Module;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * Class FileController
 * @package open20\amos\socialauth\controllers
 */
class SocialAuthController extends BackendController
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
                            'link-user',
                            'link-social-account',
                            'unlink-user',
                            'unlink-social-account',
                        ],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'endpoint',
                            'sign-in',
                            'sign-up',
                            'mobile',
                            'land',
                        ],
                        //'roles' => ['*']
                    ]
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    /**
     * Endpoint bridge for auth actions
     *
     * @param $action
     * @param $provider
     * @return \Hybrid_Endpoint
     */
    public function actionEndpoint($action, $provider)
    {
        $key = 'hauth_' . $action; // either `hauth_start` or `hauth_done`
        $_REQUEST[$key] = $provider; // provider will be something like `facebook` or `google`

        $adapter = \Hybrid_Endpoint::process();

        return $adapter;
    }

    /**
     * @param $provider
     * @param null $urlBase
     * @return bool|\Hybrid_Provider_Adapter
     */
    public function authProcedure($provider, $urlBase = null)
    {
        /**
         * @var $baseUrl string with the base url
         */
        if (!empty($urlBase)) {
            $baseUrl = $urlBase;

        } else {
            $baseUrl = Yii::$app->request->getHostInfo();
        }
        $baseUrl = str_replace('http://',
            'https://', $baseUrl);

        /**
         * @var $config array with all configurations
         */
        $config = [
            'base_url' => $baseUrl,
            'providers' => $this->module->getProviders()
        ];

        /**
         * @var $callbackUrl string The full call back url to use in the provider
         */
        $callbackUrl = $baseUrl . '/socialauth/social-auth/endpoint';

        try {
            /**
             * @var $hybridauth \Hybrid_Auth
             */
            $hybridauth = new \Hybrid_Auth($config);
        } catch (\Exception $e) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Login Failed'));

            return false;
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $hybridauth->authenticate($provider, [
            'login_start' => $callbackUrl . '?action=start&provider=' . strtolower($provider),
            'login_done' => $callbackUrl . '?action=done&provider=' . strtolower($provider),
        ]);

        return $adapter;
    }

    /**
     * Login with social account
     * @param $provider
     * @return bool|\yii\web\Response
     */
    public function actionSignIn($provider, $redirects = true, $redirectTo = null)
    {

        $urlToRedirect = Yii::$app->getUser()->getReturnUrl('');
        $community_id = \Yii::$app->request->get('community');

        if (strpos($urlToRedirect, 'community/join') > 0) {
            $urlToCommunity = \Yii::$app->getUrlManager()->createUrl($urlToRedirect);
        } else {
            $urlToCommunity = \Yii::$app->getUrlManager()->createUrl(['/community/join', 'id' => $community_id, 'subscribe' => 1]);
        }

        //Spid requirements
        if ($provider == 'spid' && !\Yii::$app->request->get('signed')) {
            return \Yii::$app->controller->redirect(Url::to(['/socialauth/spid/aslogin',
                'provider' => 'spid',
                'signed' => true,
                'AuthId' => 'service-l1',
                'ReturnTo' => Url::to(['/'], true)
            ], true));
        }

        /**
         * If the user is already logged in go to home
         */
        if (!\open20\amos\core\utilities\CurrentUser::isPlatformGuest() && $redirects) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Already Logged In'));

            return $this->goHome();
        }

        /**
         * If login is not enabled
         */
        if (!$this->module->enableLogin) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Login Disabled'));

            return $this->goHome();
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the adapter is not set go back to home
         */
        if (!$adapter) {
//            return $this->goHome();
            return $this->goBack();
        }

        /**
         * @var $userProfile \Hybrid_User_Profile
         */
        $userProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        //Return direct result
        if (!$redirects) {
            return $userProfile;
        }

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = SocialAuthUsers::findOne(['identifier' => $userProfile->identifier, 'provider' => $provider]);

        //Override default timeout
        $loginTimeout = Yii::$app->params['loginTimeout'] ?: 3600;

        /**
         * If the social user exists
         */
        if ($socialUser) {
            /**
             * If the user exists
             */
            if ($socialUser->user && $socialUser->user->id) {

                //Check user deactivated
                if ($socialUser->user->status == User::STATUS_DELETED) {
                    Yii::$app->session->addFlash('danger', Module::t('amosadmin', 'User deactivated. To log in again, request reactivation of the profile.'));
//                    return $this->goHome();
                    return $this->goBack();
                }


                $signIn = Yii::$app->user->login($socialUser->user, $loginTimeout);

                // if google contact service enabled reload in session some contact data by google account
                AmosAdmin::fetchGoogleContacts();

                if ($redirectTo) {
                    return $this->redirect($redirectTo);
                } else if ($community_id) {
                    return $this->redirect($urlToCommunity);
                }

                return $this->goBack();
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Login with this User'));
            }

            return $this->goBack();
        } else {
            //Find for existing user with social email
            $q = User::find();
            $q->where(['email' => $userProfile->email]);
            $q->orWhere(['username' => $userProfile->email]);

            $userMatchMail = $q->one();

            if ($userMatchMail && $userMatchMail->id) {
                if (!$this->module->userOverload) {
                    Yii::$app->session->set('social-match', $provider);
                    Yii::$app->session->set('social-profile', $userProfile);

                    return $this->redirect('/'.AmosAdmin::getInstance()->id.'/security/login');
                } else {
                    //Link immediatelly to matched mail user
                    $this->linkSocialToUser($provider, $userProfile, $userMatchMail->id);

                    //Logijn to the platform
                    $signIn = Yii::$app->user->login($userMatchMail, $loginTimeout);
                    // if google contact service enabled reload in session some contact data by google account
                    AmosAdmin::fetchGoogleContacts();

                    //Back to home
                    if ($community_id) {
                        return $this->redirect($urlToCommunity);
                    }

                    return $this->goHome();
                }
            } else {
                Yii::$app->session->set('social-pending', $provider);
                Yii::$app->session->set('social-profile', $userProfile);

                return $this->redirect('/'.AmosAdmin::getInstance()->id.'/security/register');
            }
            //Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'User Not Found, Please try with Other User'));
        }
    }

    /**
     * @param $provider
     * @return \yii\web\Response
     */
    public function actionMobile($provider)
    {
        $userProfile = $this->actionSignIn($provider, false);

        if (!($userProfile instanceof \Hybrid_User_Profile)) {
            return $this->redirect(['/socialauth/social-auth/land', 'error' => true, 'errorMessage' => Yii::t('socialauth', 'Accesso Social Non Disponibile')]);
        }

        $q = \open20\amos\mobile\bridge\modules\v1\models\User::find();
        $q->where(['email' => $userProfile->email]);
        $q->orWhere(['username' => $userProfile->email]);

        $userMatchMail = $q->one();

        if (!$userMatchMail || !$userMatchMail->id) {
            return $this->redirect(['/socialauth/social-auth/land', 'error' => true, 'errorMessage' => Yii::t('socialauth', 'Non Sei Registrato Nella Piattaforma')]);
        }

        /**
         * @var $token AccessTokens
         */
        $token = $userMatchMail->refreshAccessToken('mobile', 'mobile');

        if ($token && !$token->hasErrors()) {
            return $this->redirect(['/socialauth/social-auth/land', 'token' => $token->access_token]);
        } else {
            return $this->redirect(['/socialauth/social-auth/land', 'error' => true, 'errorMessage' => Yii::t('socialauth', 'Errore Di ACcesso, Riprovare Tra Qualche Minuto')]);
        }

    }

    /**
     * @return bool
     */
    public function actionLand()
    {
        return true;
    }

    /**
     * @param $provider
     * @return bool|\yii\web\Response
     */
    public function actionSignUp($provider)
    {
        $community_id = \Yii::$app->request->get('community');
        $urlToCommunity = \Yii::$app->getUrlManager()->createUrl(['/community/join', 'id' => $community_id, 'subscribe' => 1]);

        if (!\open20\amos\core\utilities\CurrentUser::isPlatformGuest()) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Already Logged In'));

            return $this->goHome();
        }

        /**
         * If signup is not enabled
         */
        if (!$this->module->enableRegister) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Signup Disabled'));

            return $this->goHome();
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the mail is not set i can't create user
         */
        if (!$adapter) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, permission denied'));

            return $this->goHome();
        }

        //Change login timeout
        $loginTimeout = Yii::$app->params['loginTimeout'] ?: 3600;

        /**
         * @var $socialProfile \Hybrid_User_Profile
         */
        $socialProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = SocialAuthUsers::findOne(['identifier' => $socialProfile->identifier, 'provider' => $provider]);

        /**
         * If the social user exists
         */
        if ($socialUser) {
            /**
             * If the user exists
             */
            if ($socialUser->user && $socialUser->user->id) {

                //Check user deactivated
                if ($socialUser->user->status == User::STATUS_DELETED) {
                    Yii::$app->session->addFlash('danger', Module::t('amosadmin', 'User deactivated. To log in again, request reactivation of the profile.'));
//                    return $this->goHome();
                    return $this->goBack();
                }

                $signIn = Yii::$app->user->login($socialUser->user, $loginTimeout);

                if ($community_id) {
                    return $this->redirect($urlToCommunity);
                }
                return $this->goBack();
            }
        }

        /**
         * If the mail is not set i can't create user
         */
        if (empty($socialProfile->email)) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, missing mail permission'));

            return $this->goHome();
        }

        //Find for existing user with social email
        $q = User::find();
        $q->where(['email' => $socialProfile->email]);
        $q->orWhere(['username' => $socialProfile->email]);
        $userMatchMail = $q->one();

        if ($userMatchMail && $userMatchMail->id) {
            if (!$this->module->userOverload) {
                Yii::$app->session->set('social-match', $provider);
                Yii::$app->session->set('social-profile', $socialProfile);

                return $this->redirect('/'.AmosAdmin::getInstance()->id.'/security/login');
            } else {
                $this->linkSocialToUser($provider, $socialProfile, $userMatchMail->id);

                //Logijn to the platform
                $signIn = Yii::$app->user->login($userMatchMail, $loginTimeout);

                if ($community_id) {
                    return $this->redirect($urlToCommunity);
                }
                return $this->goHome();
            }
        }

        //Create user and user profile
        $userCreated = $this->createUser($socialProfile);

        /**
         * If user creation fails
         */
        if (!$userCreated || isset($userCreated['error'])) {
            return $this->goHome();
        }

        /**
         * @var $user User
         */
        $user = $userCreated['user'];

        /**
         * @var $userProfile UserProfile
         */
        $userProfile = UserProfile::findOne(['user_id' => $user->id]);

        /**
         * If $newUser is false the user is not created
         */
        if (!$userProfile || !$userProfile->id) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Error when loading profile data, try again'));

            //Rollback User on error
            $user->id->delete();

            return $this->goHome();
        }

        //Import user profile image
        $importProfile = $this->importUserImage($socialProfile, $userProfile);

        //Back to home on error
        if (!$importProfile) {
            //Rollback on error
            $this->rollBackUser($userProfile);

            return $this->goHome();
        }

        //Create social record
        $socialUser = $this->createSocialUser($userProfile, $socialProfile, $provider);

        //If social profile creation fails
        if (!$socialUser || isset($socialUser['error'])) {
            //Rollback on error
            $this->rollBackUser($userProfile);
            return $this->goHome();
        }

        //Logijn to the platform
        $signIn = Yii::$app->user->login($socialUser->user, $loginTimeout);

        if ($community_id) {
            return $this->redirect($urlToCommunity);
        }

        return $this->goHome();
    }

    /**
     * @param $userProfile UserProfile
     * @return bool - the operation result
     */
    protected function rollBackUser($userProfile)
    {
        /**
         * @var $user User
         */
        $user = $userProfile->user;

        //Delete user profile
        $userProfile->delete();

        //Delete User
        $user->delete();

        return true;
    }

    /**
     * @param \Hybrid_User_Profile $socialProfile
     * @return bool|int
     */
    protected function createUser(\Hybrid_User_Profile $socialProfile)
    {
        try {
            //Name Parts (maybe it contains last name
            $userNameParts = explode(' ', $socialProfile->firstName);

            //If the name is explodable generate last name
            if (count($userNameParts)) {
                if (!$socialProfile->lastName) {
                    //This contains only name parts
                    $copyParts = $userNameParts;

                    //Only name part
                    $userName = reset($userNameParts);

                    //Shift out name
                    array_shift($copyParts);

                    //Last name of user (or rollback to first name)
                    $userSurname = count($copyParts) ? implode(' ', $copyParts) : $userName;
                } else {
                    //Name of the user
                    $userName = $socialProfile->firstName;

                    //Last name of user
                    $userSurname = $socialProfile->lastName;
                }
            } else {
                $userName = 'User';
                $userSurname = $socialProfile->email;
            }

            /** @var AmosAdmin $adminModule */
            $adminModule = AmosAdmin::getInstance();

            /**
             * @var $newUser integer False or UserId
             */
            $newUser = $adminModule->createNewAccount(
                $userName,
                $userSurname,
                $socialProfile->email,
                true
            );

            //If $newUser is false the user is not created
            if (!$newUser || isset($newUser['error'])) {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, user creation error'));

                if ($newUser['messages']) {
                    foreach ($newUser['messages'] as $message) {
                        Yii::$app->session->addFlash('danger', Module::t('amossocialauth', reset($message)));
                    }
                }

                return false;
            }

            return $newUser;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param \Hybrid_User_Profile $socialProfile
     * @param $userProfile
     * @return bool
     */
    protected function importUserImage(\Hybrid_User_Profile $socialProfile, $userProfile)
    {
        //If profile image url is set
        if ($socialProfile->photoURL) {
            //Request file header
            $fileHeader = @get_headers($socialProfile->photoURL);

            //If the file exists (header 200)
            if (preg_match("|200|", $fileHeader[0]) || preg_match("|304|", $fileHeader[0]) || preg_match("|302|", $fileHeader[0])) {
                // Get Importer component
                $importTool = new FileImport();

                //The Image content
                $temporaryFile = $this->obtainImage($socialProfile->photoURL);

                if ($temporaryFile == false) {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to store image file, try again'));

                    return false;
                }

                //Import file as avatar
                $importResult = $importTool->importFileForModel($userProfile, 'userProfileImage', $temporaryFile);

                if (isset($importResult['error'])) {
                    Yii::$app->session->addFlash('danger', $importResult['error']);
                    return false;
                } elseif ($importResult == false) {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to import the user avatar'));
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $fileUrl
     * @return string
     */
    protected function obtainImage($fileUrl)
    {
        try {
            //Temporary file path
            $filepath = '/tmp/' . md5($fileUrl);

            //Obtain File Data
            $fileData = file_get_contents($fileUrl);

            //Put content to temporary dir
            file_put_contents($filepath, $fileData);

            //Change permissions
            @chmod($filepath, 0777);

            return $filepath;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param UserProfile $userProfile
     * @param \Hybrid_User_Profile $socialProfile
     * @param $provider
     * @return bool|SocialAuthUsers
     */
    protected function createSocialUser($userProfile, \Hybrid_User_Profile $socialProfile, $provider)
    {
        try {
            /**
             * @var $socialUser SocialAuthUsers
             */
            $socialUser = new SocialAuthUsers();

            /**
             * @var $socialProfileArray array User profile from provider
             */
            $socialProfileArray = (array)$socialProfile;
            $socialProfileArray['provider'] = $provider;
            $socialProfileArray['user_id'] = $userProfile->user_id;

            /**
             * If all data can be loaded to new record
             */
            if ($socialUser->load(['SocialAuthUsers' => $socialProfileArray])) {
                /**
                 * Is valid social user
                 */
                if ($socialUser->validate()) {
                    $socialUser->save();
                    return $socialUser;
                } else {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Link The Social Profile'));
                    return false;
                }
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Invalid Social Profile, Try again'));
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Link current logged user to social account
     * @param $provider
     * @return \yii\web\Response
     */
    public function actionLinkUser($provider)
    {
        $this->setUpLayout('login');

        /**
         * If the user is already logged in go to home
         */
        if (\open20\amos\core\utilities\CurrentUser::isPlatformGuest()) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Please LogIn to your account First'));

            return $this->goHome();
        }

        /**
         * If linking is not enabled
         */
        if (!$this->module->enableLink) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Linking Disabled'));

            return $this->goBack();
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the adapter is not set go back to home
         */
        if (!$adapter) {
            return $this->goBack();
        }

        /**
         * @var $userProfile \Hybrid_User_Profile
         */
        $userProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        /**
         * Find for existing social profile with the same ID
         * @var $existingUserProfile SocialAuthUsers
         */
        $existingUserProfile = SocialAuthUsers::findOne(['identifier' => $userProfile->identifier, 'provider' => $provider]);

        /**
         * If the social profile exists go back with notice
         */
        if ($existingUserProfile && $existingUserProfile->id) {
            if ($existingUserProfile->user_id == Yii::$app->user->id) {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Profile Already Connected'));
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Profile Already Connected to Another User'));
            }

            return $this->goBack();
        }

        /**
         * @var $userProfileArray array User profile from provider
         */
        $userProfileArray = (array)$userProfile;
        $userProfileArray['provider'] = $provider;
        $userProfileArray['user_id'] = Yii::$app->user->id;

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = new SocialAuthUsers();

        /**
         * If all data can be loaded to new record
         */
        if ($socialUser->load(['SocialAuthUsers' => $userProfileArray])) {
            /**
             * Is valid social user
             */
            if ($socialUser->validate()) {
                $socialUser->save();

                Yii::$app->session->addFlash('success', Module::t('amossocialauth', 'Social profile Linked'));

                return $this->goBack();
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Link The Social Profile'));

                return $this->goBack();
            }
        } else {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Invalid Social Profile, Try again'));

            return $this->goBack();
        }
    }

    /**
     * Link current logged user to social account
     * @param $provider
     * @return string
     */
    public function actionLinkSocialAccount($provider)
    {
        $this->setUpLayout('empty');

        /**
         * If the user is already logged in go to home
         */
        if (\open20\amos\core\utilities\CurrentUser::isPlatformGuest()) {
            $message = Module::t('amossocialauth', 'Please LogIn to your account First');
            return $this->render('link-social-account', ['message' => $message]);
        }

        /**
         * If linking is not enabled
         */
        if (!$this->module->enableLink) {
            $message = Module::t('amossocialauth', 'Social Linking Disabled');
            return $this->render('link-social-account', ['message' => $message]);
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the adapter is not set go back to home
         */
        if (!$adapter) {
            return $this->render('link-social-account', ['message' => 'no adapter for provider ' . $provider]);
        }

        /**
         * @var $userProfile \Hybrid_User_Profile
         */
        $userProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        /**
         * Find for existing social profile with the same ID
         * @var $existingUserProfile SocialAuthUsers
         */
        $existingUserProfile = SocialAuthUsers::findOne(['identifier' => $userProfile->identifier, 'provider' => $provider]);

        /**
         * If the social profile exists go back with notice
         */
        if ($existingUserProfile && $existingUserProfile->id) {
            if ($existingUserProfile->user_id == Yii::$app->user->id) {
                $message = Module::t('amossocialauth', 'Social Profile Already Connected');
            } else {
                $message = Module::t('amossocialauth', 'Social Profile Already Connected to Another User');
            }

            return $this->render('link-social-account', ['message' => $message]);
        }

        /**
         * @var $userProfileArray array User profile from provider
         */
        $userProfileArray = (array)$userProfile;
        $userProfileArray['provider'] = $provider;
        $userProfileArray['user_id'] = Yii::$app->user->id;

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = new SocialAuthUsers();

        /**
         * If all data can be loaded to new record
         */
        if ($socialUser->load(['SocialAuthUsers' => $userProfileArray])) {
            /**
             * Is valid social user
             */
            if ($socialUser->validate()) {
                $socialUser->save();

                $message = Module::t('amossocialauth', 'Social profile Linked');

            } else {
                $message = Module::t('amossocialauth', 'Unable to Link The Social Profile');

            }
        } else {
            $message = Module::t('amossocialauth', 'Invalid Social Profile, Try again');

        }
        return $this->render('link-social-account', ['message' => $message]);
    }

    /**
     * UnLink current logged user to social account
     * @param $provider
     * @return \yii\web\Response
     */
    public function actionUnlinkUser($provider)
    {
        $this->setUpLayout('login');

        /**
         * If the user is already logged in go to home
         */
        if (\open20\amos\core\utilities\CurrentUser::isPlatformGuest()) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Please LogIn to your account First'));

            return $this->goHome();
        }

        /**
         * If linking is not enabled
         */
        if (!$this->module->enableLink) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Linking Disabled'));

            return $this->goBack();
        }

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = SocialAuthUsers::findOne([
            'user_id' => Yii::$app->user->id,
            'provider' => $provider
        ]);

        /**
         * If linking is not enabled
         */
        if (!$socialUser || !$socialUser->id) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social User Not Found'));

            return $this->goBack();
        }

        //If found delete and go back
        $socialUser->delete();

        //Reponse good state
        Yii::$app->session->addFlash('success', Module::t('amossocialauth', 'Social Account Unlinked'));

        //Go back
        return $this->goBack();
    }

    /**
     * Create new social profile on db a link to selected user_id
     * @param $provider
     * @param \Hybrid_User_Profile $socialProfile
     * @param $user_id
     * @return \yii\web\Response
     */
    protected function linkSocialToUser($provider, \Hybrid_User_Profile $socialProfile, $user_id)
    {

        /**
         * @var $userProfileArray array User profile from provider
         */
        $userProfileArray = (array)$socialProfile;
        $userProfileArray['provider'] = $provider;
        $userProfileArray['user_id'] = $user_id;

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = new SocialAuthUsers();

        /**
         * If all data can be loaded to new record
         */
        if ($socialUser->load(['SocialAuthUsers' => $userProfileArray])) {
            /**
             * Is valid social user
             */
            if ($socialUser->validate()) {
                $socialUser->save();

                Yii::$app->session->addFlash('success', Module::t('amossocialauth', 'Social profile Linked'));

                return true;
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Link The Social Profile'));

                return false;
            }
        } else {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Invalid Social Profile, Try again'));

            return false;
        }
    }

    /**
     * UnLink current logged user to social account
     * @param $provider
     * @return string
     */
    public function actionUnlinkSocialAccount($provider)
    {
        $this->setUpLayout('empty');

        /**
         * If the user is already logged in go to home
         */
        if (\open20\amos\core\utilities\CurrentUser::isPlatformGuest()) {
            $message = Module::t('amossocialauth', 'Please LogIn to your account First');
            if (Yii::$app->request->isPost) {
                $result['result'] = false;
                $result['message'] = $message;
                return json_encode($result);
            }
            return $this->render('link-social-account', ['message' => $message]);
        }

        /**
         * If linking is not enabled
         */
        if (!$this->module->enableLink) {
            $message = Module::t('amossocialauth', 'Social Linking Disabled');
            if (Yii::$app->request->isPost) {
                $result['result'] = false;
                $result['message'] = $message;
                return json_encode($result);
            }
            return $this->render('link-social-account', ['message' => $message]);
        }

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = SocialAuthUsers::findOne([
            'user_id' => Yii::$app->user->id,
            'provider' => $provider
        ]);

        /**
         * If linking is not enabled
         */
        if (!$socialUser || !$socialUser->id) {
            $message = Module::t('amossocialauth', 'Social User Not Found');

            if (Yii::$app->request->isPost) {
                $result['result'] = false;
                $result['message'] = $message;
                return json_encode($result);
            }
            return $this->render('link-social-account', ['message' => $message]);
        }


        //If found delete and go back
        $socialUser->delete();

        $message = Module::t('amossocialauth', 'Social Account Unlinked');

        if (Yii::$app->request->isPost) {
            $result['result'] = true;
            $result['message'] = $message;
            return json_encode($result);
        }
        return $this->render('link-social-account', ['message' => $message]);
    }


}
