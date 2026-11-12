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
