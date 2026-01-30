<?php

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
