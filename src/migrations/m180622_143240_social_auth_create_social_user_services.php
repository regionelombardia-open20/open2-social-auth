<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\socialauth\migrations
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

use open20\amos\core\migration\AmosMigrationTableCreation;

/**
 * Class m180622_143240_social_auth_create_social_user_services
 */
class m180622_143240_social_auth_create_social_user_services extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%social_user_services}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'social_users_id' => $this->integer(11)->notNull()->comment('Social User Id'),
            'service' => $this->string(255)->notNull()->comment('Service name'),
            'access_token' => $this->string(255)->null()->comment('Service Access Token'),
            'token_type' => $this->string(255)->null()->comment('Token type'),
            'expires_in' => $this->integer(11)->null()->comment('Access token expiration'),
            'refresh_token' => $this->string(255)->null()->comment('Refresh token'),
            'token_created' => $this->integer(11)->null()->comment('created_at'),
            'service_id' =>  $this->string(255)->null()->comment('Service ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function beforeTableCreation()
    {
        parent::beforeTableCreation();
        $this->setAddCreatedUpdatedFields(true);
    }

    /**
     * @inheritdoc
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey('fk_social_user_services_social_users', $this->getRawTableName(), 'social_users_id', '{{%social_users}}', 'id');
    }
}
