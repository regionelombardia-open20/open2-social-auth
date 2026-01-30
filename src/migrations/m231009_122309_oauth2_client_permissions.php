<?php

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
 * Class m231009_122209_oauth2_federator_permissions*/
class m231009_122309_oauth2_client_permissions extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
            [
                'name' => 'OAUTH2CLIENT_ADMINISTRATOR',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Administrator role for Oauth2Client',
                'ruleName' => null,
                'parent' => ['ADMIN']
            ],
            [
                'name' => 'OAUTH2CLIENT_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model Oauth2Client',
                'ruleName' => null,
                'parent' => ['OAUTH2CLIENT_ADMINISTRATOR']
            ],
            [
                'name' => 'OAUTH2CLIENT_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di READ sul model Oauth2Client',
                'ruleName' => null,
                'parent' => ['OAUTH2CLIENT_ADMINISTRATOR']
            ],
            [
                'name' => 'OAUTH2CLIENT_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di UPDATE sul model Oauth2Client',
                'ruleName' => null,
                'parent' => ['OAUTH2CLIENT_ADMINISTRATOR']
            ],
            [
                'name' => 'OAUTH2CLIENT_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di DELETE sul model Oauth2Client',
                'ruleName' => null,
                'parent' => ['OAUTH2CLIENT_ADMINISTRATOR']
            ],

        ];
    }
}
