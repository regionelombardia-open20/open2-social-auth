Amos Social Auth
-----------------------

Social Auth For Amos

Installation
------------

1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
    
    Either run
    
    ```bash
    composer require open20/amos-social-auth
    ```
    
    or add
    
    ```
    "open20/amos-social-auth": "~1.0"
    ```
    
    to the require section of your `composer.json` file.
    
2.  Add module to your main config in common:
        
    ```php
    <?php
    'modules' => [
        'socialauth' => [
            'class' => 'open20\amos\socialauth\Module'
        ],
    ],
    ```
    
3. Apply migrations
    
    ```bash
    php yii migrate/up --migrationPath=@vendor/open20/amos-social-auth/src/migrations
    ```



Configuration
-------------

* Sample configuration
    ```php
    <?php
        'modules' => [
            'socialauth' => [
                'class' => 'open20\amos\socialauth\Module',
                'enableLogin' => true,
                'enableLink' => false,
                'enableRegister' => false,
                'providers' => [
                   "Facebook" => [
                        "enabled" => true,
                        "keys" => [
                            "id" => "",
                            "secret" => ""
                        ],
                        "scope" => "email"
                    ],
                    "Twitter" => [
                        "enabled" => true,
                        "keys" => [
                            "key" => "",
                            "secret" => ""
                        ],
                        "scope" => 'email',
                        "includeEmail" => true
                    ],
                    "Google" => [
                        "enabled" => true,
                        "keys" => [
                            "id" => "",
                            "secret" => ""
                        ],
                        "scope" => 'email',
                        "includeEmail" => true
                    ],
                ]
            ],
        ],
    ```
    see configuration doc: https://hybridauth.github.io/hybridauth/userguide/Configuration.html

* Action enable/disable

    * `enableLogin` To alow Social Login
    * `enableLink` To Enable Social Account Linking (my-profile 'settings' tab)
    * `enableRegister` To Enable Registration with Social
    * `enableServices` To list enabled services related to social accounts. By default the array contains `calendar` and `contacts`  

The provider linking functionality is managed in 'My Profile', amos-admin.
To enable social links check in admin configuraion the visibility for box social-accounts and for the the providers buttons.

```php
$modules['admin'] =  [
    'class' => 'open20\amos\admin\AmosAdmin',
	'enableRegister' => true,
         'fieldsConfigurations' => [
                'boxes' => [
                    .
                    .
                    .
                    'box_social_account' => ['form' => true, 'view' => true],
                ],
                'fields' => [
                    .
                    .
                    .   
                    'facebook' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
                    'google' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
                    'linkedin' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
                    'twitter' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
                    .
                    .
                    .
                ]
            ]
        ];
```

Providers
------------

Providers configuration doc: https://hybridauth.github.io/hybridauth/userguide.html section 'Popular Providers'
    
* Google  
    (guide from https://hybridauth.github.io/hybridauth/userguide/IDProvider_info_Google.html)
    1. Go to the Google Developers Console
        https://console.developers.google.com/projectselector/apis/library?supportedpurview=project.  
    2. From the project drop-down, select a project, or create a new one.  
    3. Enable the Google API services:
        In the list of Google APIs, search for the Google+ API service.
        Select Google+ API from the results list.
        Press the Enable API button.
        The same for People API and Calendar API
         * Enable API in Google console  
            **for account and contacts:**  People API, Google+ API  
            **for calendar events:** Google Calendar API
    4. When the process completes, enabled APIs appears in the list of enabled APIs. To access, select API Manager on the left sidebar menu, then select the Enabled APIs tab.  
    5. In the sidebar under "API Manager", select Credentials.
       In the Credentials tab, select the New credentials drop-down list, and choose **OAuth client ID**.  
    6. From the Application type list, choose the Web application.  
    7. Enter a name and provide this URLs as Authorized redirect URIs:
     https://YourPlatformUrl/socialauth/social-auth/sign-in?action=done&provider=google  
     https://YourPlatformUrl/socialauth/social-auth/sign-up?action=done&provider=google  
     https://YourPlatformUrl/admin/user-profile/enable-google-service
     
     8. Once you have registered, copy and past the created application credentials (Client ID and Secret) into the HybridAuth config file.
    
   
    