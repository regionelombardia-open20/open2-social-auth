<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\socialauth\models
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\socialauth\models;


use open20\amos\core\record\Record;
use open20\amos\socialauth\Module;

/**
 * Class SocialAuthServices
 * @package open20\amos\socialauth\models
 *
 * @property integer $id
 * @property integer $social_users_id
 * @property string $service
 * @property string $access_token
 * @property integer $expires_in
 * @property string $token_type
 * @property string $refresh_token
 * @property integer $token_created
 * @property string $service_id
 *
 * @property SocialAuthUsers $socialUser
 * @property \open20\amos\core\user\User $user
 */
class SocialAuthServices extends Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'social_user_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['social_users_id', 'service'], 'required'],
            [[
                'service',
                'access_token',
                'token_type',
                'refresh_token',
                'service_id'
            ],
                'string'
            ],
            [['social_users_id', 'expires_in', 'token_created'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('amossocialauth', 'ID'),
            'social_users_id' => Module::t('amossocialauth', 'Social User Id'),
            'service' => Module::t('amossocialauth', 'Service name'),
            'access_token' => Module::t('amossocialauth', 'Service Access Token'),
            'token_type' => Module::t('amossocialauth', 'Token type'),
            'expires_in' => Module::t('amossocialauth', 'Access token expiration'),
            'refresh_token' => Module::t('amossocialauth', 'Refresh token'),
            'token_created' => Module::t('amossocialauth', 'created_at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocialUser()
    {
        return $this->hasOne(SocialAuthUsers::className(), ['id' => 'social_users_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\open20\amos\core\user\User::className(), ['id' => 'user_id'])->via('socialUser');
    }
}