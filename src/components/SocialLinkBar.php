<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\socialauth
 * @category   CategoryName
 */

namespace lispa\amos\socialauth\components;

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\socialauth\models\SocialAuthUsers;
use lispa\amos\socialauth\Module;
use Yii;
use yii\base\Component;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class FileImport
 * @package lispa\amos\socialauth\components
 */
class SocialLinkBar extends Widget
{
    public function run()
    {
        parent::run();

        /**
         * Return string
         */
        $result = '';

        /**
         * @var $module Module
         */
        $module = Yii::$app->getModule('socialauth');

        /**
         * List of providers configured
         */
        $providers = $module->providers;

        /**
         * @var $enabledProviders array List of providers not yet linked
         */
        $enabledProviders = [];

        /**
         * Iterate all provider and find existing links
         */
        foreach ($providers as $providerName=>$config) {
            $lowCaseName = strtolower($providerName);

            /**
             * @var $socialAccount SocialAuthUsers
             */
            $socialAccount = SocialAuthUsers::findOne([
                'provider' => $lowCaseName,
                'user_id' => Yii::$app->user->id
            ]);

            /**
             * If the user profile is not linked to this user append the provider
             */
            if(!$socialAccount || !$socialAccount->id) {
                $enabledProviders[$providerName] = $config;
            }
        }

        return $this->render('social-link-bar', [
            'providers' => $enabledProviders
        ]);
    }
}
