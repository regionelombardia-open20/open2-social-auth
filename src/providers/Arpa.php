<?php

namespace open20\amos\socialauth\providers;

use open20\amos\socialauth\models\SocialAuthUsers;
use Firebase\JWT\JWK;
use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\Exception;
use Hybridauth\Exception\InvalidApplicationCredentialsException;
use Hybridauth\Exception\InvalidAuthorizationStateException;
use Hybridauth\Exception\UnexpectedApiResponseException;
use sizeg\jwt\Jwt;

/**
 * Provider ARPA per Regione Toscana
 */
class Arpa extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $protocol = 'https';

    /**
     * {@inheritdoc}
     */
    protected $scope = 'openid profile email';

    protected $audience = 'suap-dt';


    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = '';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = '';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = '';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenInfoUrl = '';


    public function authenticate()
    {
        $this->logger->info(sprintf('%s::authenticate()', get_class($this)));
//        pr('2');
        if ($this->isConnected()) {
            return true;
        }

        try {
            $this->authenticateCheckError();

            $code = filter_input($_SERVER['REQUEST_METHOD'] === 'POST' ? INPUT_POST : INPUT_GET, 'code');

            if (empty($code)) {
                $this->authenticateBegin();
            } else {
                $this->authenticateFinish();
            }
        } catch (Exception $e) {
            $this->clearStoredData();

            throw $e;
        }

        return null;
    }



    protected function authenticateBegin()
    {
        $authUrl = $this->getAuthorizeUrl().'&audience=' . $this->audience;
        $this->logger->debug(sprintf('%s::authenticateBegin(), redirecting user to:', get_class($this)), [$authUrl]);
        \Hybridauth\HttpClient\Util::redirect($authUrl);
    }

    protected function exchangeCodeForAccessToken($code)
    {
        $this->tokenExchangeParameters['code'] = $code;
//        pr(get_class($this->httpClient));die;
        $response = $this->httpClient->request(
            $this->accessTokenUrl,
            $this->tokenExchangeMethod,
            $this->tokenExchangeParameters,
            $this->tokenExchangeHeaders
        );
//$resp = json_decode($response, true);
//// Se vuoi solo decodificare SENZA verificare la firma
//        $tokenParts = explode('.', $resp['access_token']);
//        $header = json_decode(base64_decode(strtr($tokenParts[0], '-_', '+/')), true);
//        $payload = json_decode(base64_decode(strtr($tokenParts[1], '-_', '+/')), true);

        $this->validateApiResponse('Unable to exchange code for API access token');

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $configs = $this->config->toArray();
//pr($configs);
        // Recupera clientId e clientSecret PRIMA di parent::initialize()
        $this->clientId = $configs['keys']['id'];
        //pr($this->clientId);
        $this->clientSecret = $this->config->get('keys.secret') ?? '';

        parent::initialize();

        // Provider configs from the collection
        $configs = $this->config->toArray();

        if (empty($configs['openid_identifier'])) {
            throw new \Exception('Missing "openid_identifier" in provider config');
        }

        // Configura manualmente gli endpoint ARPA
        $baseUrl = rtrim($configs['openid_identifier'], '/');
        $this->authorizeUrl = "{$baseUrl}/protocol/openid-connect/auth";
        $this->accessTokenUrl = "{$baseUrl}/protocol/openid-connect/token";
        $this->accessTokenInfoUrl = "{$baseUrl}/protocol/openid-connect/userinfo";

        $this->tokenExchangeParameters['audience'] = 'suap-dt';
        $this->tokenRefreshParameters['client_id'] = $this->clientId;
        $this->tokenRefreshParameters['client_secret'] = $this->clientSecret;

        // Set callback if configured
        if (isset($configs['redirect_uri']) && !empty($configs['redirect_uri'])) {
            $this->setCallback($configs['redirect_uri']);
        }
    }

    protected function configure()
    {
        $this->clientId = $this->config->filter('keys')->get('id') ?: $this->config->filter('keys')->get('key');
        $this->clientSecret = $this->config->filter('keys')->get('secret') ?? '';

        if (!$this->clientId) {
            throw new InvalidApplicationCredentialsException(
                'Your application id is required in order to connect to ' . $this->providerId
            );
        }

        $this->scope = $this->config->exists('scope') ? $this->config->get('scope') : $this->scope;

        if ($this->config->exists('tokens')) {
            $this->setAccessToken($this->config->get('tokens'));
        }

        if ($this->config->exists('supportRequestState')) {
            $this->supportRequestState = $this->config->get('supportRequestState');
        }

        $this->setCallback($this->config->get('callback'));
        $this->setApiEndpoints($this->config->get('endpoints'));
    }

    /**
     * {@inheritdoc}
     */
    public function loginBegin()
    {
        $configs = $this->config->toArray();

        $parameters = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->callback,
            'scope' => $this->scope,
            'audience' => $this->audience,
        ];

        // Gestisci eventuale stato (utile per CSRF)
        if (isset($configs['state']) && !empty($configs['state'])) {
            $parameters['state'] = $configs['state'];
        }

        // Se ARPA supporta acr_values (livello di autenticazione) puoi eventualmente aggiungerlo:
        // $parameters['acr_values'] = 'some_value';

        // Costruisci l’URL di autorizzazione
        $authorizeUrl = $this->authorizeUrl . '?' . http_build_query($parameters);
        $this->logger->debug(
            sprintf('%s::loginBegin(), redirecting user to:', get_class($this)),
            [$authorizeUrl]
        );

        \Hybrid_Auth::redirect($authorizeUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserProfile()
    {
        // Refresh tokens if needed
        $accessTokenRefreshed = $this->refreshAccessToken();
//pr(json_decode($accessTokenRefreshed));
//pr('------');die;
        $userInfoUrl = $this->accessTokenInfoUrl;
        $accessToken = $this->getAccessToken();
//pr($accessToken);die;
        $response = $this->apiRequest($userInfoUrl);
        $profilo = SocialAuthUsers::find()->andWhere(['identifier' => $response->sub])->one();
        if (empty($profilo)) {
            $profilo = new SocialAuthUsers();
        }

        if (!isset($response->sub) || isset($response->error)) {
            throw new \Exception("User profile request failed! {$this->providerId} returned an invalid response:" . \Hybrid_Logger::dumpData($response), 6);
        }
        if(\Yii::$app->session->get('token_invitation')){
            $profilo->token_invitation = \Yii::$app->session->get('token_invitation');
        }
//pr($response); die;
        $profilo->identifier = property_exists($response, 'sub') ? $response->sub : "";
        $profilo->firstName = property_exists($response, 'given_name') ? $response->given_name : "";
        $profilo->lastName = property_exists($response, 'family_name') ? $response->family_name : "";
//        $profilo->displayName     = property_exists($response, 'name') ? $response->name : "";
        $profilo->email = property_exists($response, 'email') ? $response->email : "";
        $profilo->emailVerified = property_exists($response, 'email_verified') && ($response->email_verified === true || $response->email_verified === 1) ? $response->email : "";

        // ARPA-specific fields
        $profilo->displayName = property_exists($response, 'preferred_username') ? $response->preferred_username : "";
        $profilo->auth_level = property_exists($response, 'auth_level') ? $response->auth_level : "";
        $profilo->auth_type = property_exists($response, 'auth_type') ? $response->auth_type : "";
        $profilo->provider = 'arpa';
        if (!empty($accessToken['access_token'])) {
            $profilo->access_token = $accessToken['access_token'];
            $profilo->refresh_token = $accessToken['refresh_token'];
            $profilo->expire_at = $accessToken['expires_at'];
        }

        // Extras se presenti
        if (property_exists($response, 'extras')) {
            $profilo->fiscal_code = property_exists($response->extras, 'fiscalNumber') ? $response->extras->fiscalNumber : "";
        }
        if($profilo->id){
            $profilo->save(false);
        }
        return $profilo->attributes;
    }

//    public function refreshAccessToken($parameters = [])
//    {
//
//        $this->tokenRefreshParameters = !empty($parameters)
//            ? $parameters
//            : $this->tokenRefreshParameters;
//
//        if (!$this->isRefreshTokenAvailable()) {
//            return null;
//        }
//
//        $response = $this->httpClient->request(
//            $this->accessTokenUrl,
//            $this->tokenRefreshMethod,
//            $this->tokenRefreshParameters,
//            $this->tokenRefreshHeaders
//        );
//
//        $this->validateApiResponse('Unable to refresh the access token');
//
//        $this->validateRefreshAccessToken($response);
//
//        return $response;
//    }
}
