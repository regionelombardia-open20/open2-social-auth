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
use open20\amos\core\user\User;
use open20\amos\socialauth\Module;

/**
 * @property string $id ID
 * @property string $user_id User ID
 * @property User $user User
 * @property string $identifier Unique Identiffier for Provider
 * @property string $profileURL Provider Profile Url
 * @property string $webSiteURL Web Site Url
 * @property string $photoURL Photo Url
 * @property string $displayName User Display Name
 * @property string $description User Description
 * @property string $firstName First Name
 * @property string $lastName Last Name
 * @property string $fiscal_code Last Name
 * @property string $gender Gender
 * @property string $language Language
 * @property string $email Email
 * @property string $emailVerified Email Veriffied
 * @property string $phone Phone Number
 * @property string $address Address
 * @property string $country Country
 * @property string $region Region
 * @property string $city City
 * @property integer $zip Zip Code
 * @property string $provider Provider Name
 * @property integer $age Age
 * @property integer $birthDay Birth Day
 * @property integer $birthMonth Birth Month
 * @property integer $birthYear Birth Year
 * @property string $job_title Job Name
 * @property string $organization_name Organization Name
 * @property string $auth_level Organization Name
 * @property string $auth_type Name
 * @property string $access_token Name
 * @property string $refresh_token Name
 * @property string $expire_at Name
 * @property string $raw_data Name
 * @property string $token_invitation Name
 *
 * @property SocialAuthServices[] $services
 */


class SocialAuthUsers extends Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'social_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identifier', 'provider'], 'required'],
            [['token_invitation', 'access_token','refresh_token','fiscal_code','auth_level','auth_type', 'raw_data','profileURL', 'webSiteURL', 'photoURL', 'displayName', 'description', 'firstName', 'lastName', 'gender', 'language', 'email', 'emailVerified', 'phone', 'address', 'country', 'region', 'city', 'provider', 'job_title', 'organization_name'], 'string'],
            [['expire_at', 'user_id', 'age', 'birthDay', 'birthMonth', 'birthYear', 'zip'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('amossocialauth', 'ID'),
            'user_id' => Module::t('amossocialauth', 'User ID'),
            'identifier' => Module::t('amossocialauth', 'Unique Identiffier for Provider'),
            'profileURL' => Module::t('amossocialauth', 'Provider Profile Url'),
            'webSiteURL' => Module::t('amossocialauth', 'Web Site Url'),
            'photoURL' => Module::t('amossocialauth', 'Photo Url'),
            'displayName' => Module::t('amossocialauth', 'User Display Name'),
            'description' => Module::t('amossocialauth', 'User Description'),
            'firstName' => Module::t('amossocialauth', 'First Name'),
            'lastName' => Module::t('amossocialauth', 'Last Name'),
            'gender' => Module::t('amossocialauth', 'Gender'),
            'language' => Module::t('amossocialauth', 'Language'),
            'email' => Module::t('amossocialauth', 'Email'),
            'emailVerified' => Module::t('amossocialauth', 'Email Veriffied'),
            'phone' => Module::t('amossocialauth', 'Phone Number'),
            'address' => Module::t('amossocialauth', 'Address'),
            'country' => Module::t('amossocialauth', 'Country'),
            'region' => Module::t('amossocialauth', 'Region'),
            'city' => Module::t('amossocialauth', 'City'),
            'zip' => Module::t('amossocialauth', 'Zip Code'),
            'provider' => Module::t('amossocialauth', 'Provider Name'),
            'age' => Module::t('amossocialauth', 'Age'),
            'birthDay' => Module::t('amossocialauth', 'Birth Day'),
            'birthMonth' => Module::t('amossocialauth', 'Birth Month'),
            'birthYear' => Module::t('amossocialauth', 'Birth Year'),
            'job_title' => Module::t('amossocialauth', 'Job Title'),
            'organization_name' => Module::t('amossocialauth', 'Organization Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getServices(){
        return $this->hasMany(SocialAuthServices::className(), ['social_users_id' => 'id']);
    }

    public function beforeDelete()
    {
        // if any service is associated to the linked account, it will be removed and deleted before delete the link itself
        $services = $this->services;
        if(!empty($services)){
            /** @var Module $socialAuth */
            $socialAuth = \Yii::$app->getModule('socialauth');
            foreach ($services as $service) {
                $socialAuth->removeGoogleService($service);
                $client = $socialAuth->getClient('google');
                if(!is_null($client) && !empty($service->access_token)){
                    $client->revokeToken($service->access_token);
                }
                $service->delete();
            }
        }
        return parent::beforeDelete();
    }


}
