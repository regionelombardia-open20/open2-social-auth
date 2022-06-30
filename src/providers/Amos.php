<?php
namespace open20\amos\socialauth\providers;

class Amos extends \Hybrid_Provider_Model_OAuth2 {
    public $scope = "userinfo.profile";

    public $domain;
    public $protocol='https';

    /**
     * {@inheritdoc}
     */
    function initialize() {
        parent::initialize();

        //Pull work domain
        $this->domain = $this->config['wrapper']['domain'];

        // Provider api end-points
        $this->api->authorize_url = "{$this->protocol}://{$this->domain}/socialauth/oauth2/auth";
        $this->api->token_url = "{$this->protocol}://{$this->domain}/socialauth/oauth2/token";
        $this->api->token_info_url = "{$this->protocol}://{$this->domain}/socialauth/oauth2/tokeninfo";

        if (isset($this->config['redirect_uri']) && !empty($this->config['redirect_uri'])) {
            $this->api->redirect_uri = $this->config['redirect_uri'];
        }
    }

    /**
     * {@inheritdoc}
     */
    function loginBegin() {
        $parameters = array("scope" => $this->scope, "access_type" => "offline");
        $optionals = array("scope", "access_type", "redirect_uri", "approval_prompt", "hd", "state");

        foreach ($optionals as $parameter) {
            if (isset($this->config[$parameter]) && !empty($this->config[$parameter])) {
                $parameters[$parameter] = $this->config[$parameter];
            }
            if (isset($this->config["scope"]) && !empty($this->config["scope"])) {
                $this->scope = $this->config["scope"];
            }
        }

        if (isset($this->config['force']) && $this->config['force'] === true) {
            $parameters['approval_prompt'] = 'force';
        }

        \Hybrid_Auth::redirect($this->api->authorizeUrl($parameters));
    }

    /**
     * {@inheritdoc}
     */
    function getUserProfile() {
        // refresh tokens if needed
        $this->refreshToken();

        $response = $this->api->api("{$this->protocol}://{$this->domain}/socialauth/oauth2/userinfo");

        if (!isset($response->sub) || isset($response->error)) {
            throw new \Exception("User profile request failed! {$this->providerId} returned an invalid response:" . \Hybrid_Logger::dumpData( $response ), 6);
        }

        $this->user->profile->identifier = (property_exists($response, 'sub')) ? $response->sub : "";
        $this->user->profile->firstName = (property_exists($response, 'given_name')) ? $response->given_name : "";
        $this->user->profile->lastName = (property_exists($response, 'family_name')) ? $response->family_name : "";
        $this->user->profile->displayName = (property_exists($response, 'name')) ? $response->name : "";
        $this->user->profile->photoURL = (property_exists($response, 'picture')) ? $response->picture : "";
        $this->user->profile->profileURL = (property_exists($response, 'profile')) ? $response->profile : "";
        $this->user->profile->gender = (property_exists($response, 'gender')) ? $response->gender : "";
        $this->user->profile->language = (property_exists($response, 'locale')) ? $response->locale : "";
        $this->user->profile->email = (property_exists($response, 'email')) ? $response->email : "";
        $this->user->profile->emailVerified = (property_exists($response, 'email_verified')) ? ($response->email_verified === true || $response->email_verified === 1 ? $response->email : "") : "";

        return $this->user->profile;
    }

    /**
     * Add query parameters to the $url
     *
     * @param string $url    URL
     * @param array  $params Parameters to add
     * @return string
     */
    function addUrlParam($url, array $params){
        $query = parse_url($url, PHP_URL_QUERY);

        // Returns the URL string with new parameters
        if ($query) {
            $url .= '&' . http_build_query($params);
        } else {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

}

