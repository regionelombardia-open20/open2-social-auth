<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\socialauth\components
 * @category   CategoryName
 */

namespace lispa\amos\socialauth\components;

use lispa\amos\socialauth\Module;
use Yii;
use yii\base\Widget;

/**
 * Class SocialLinkTable
 * @package lispa\amos\socialauth\components
 */
class SocialLinkTable extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();

        /**
         * @var $module Module
         */
        $module = Yii::$app->getModule('socialauth');

        /**
         * List of providers configured
         */
        $providers = $module->providers;

        return $this->render('social-link-table', [
            'providers' => $providers
        ]);
    }
}
