<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-social-auth
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

use open20\amos\socialauth\Module;

/**
 * This is the base-model class for table "oauth2_client".
 *
 * @property integer $id
 * @property string $client_id
 * @property string $redirect_uri
 * @property string $client_secret
 * @property string $grant_type
 * @property string $scope
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $deleted_by
 * @property string $deleted_at
 */
class Oauth2Client extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oauth2_client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'redirect_uri', 'client_secret'], 'required'],
            [['client_id'], 'unique'],
            [['client_id'], 'match',
                'pattern' => '/^[A-Za-z0-9_\-]+$/',
                'message' => Module::t('amossocialauth', '{attributeLabel} può contenere solo lettere, numeri, _, -.', [
                    'attributeLabel' => $this->attributeLabels()['client_id']
                ])
            ],
            [['grant_type', 'scope'], 'string'],
            [['redirect_uri'], 'url'],
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['client_id', 'client_secret'], 'string', 'max' => 80],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'redirect_uri' => 'Redirect URI',
            'client_secret' => 'Client Secret',
            'grant_type' => 'Grant Type',
            'scope' => 'Scope',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
        ];
    }
}
