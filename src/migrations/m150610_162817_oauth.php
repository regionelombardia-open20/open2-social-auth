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

use yii\db\Schema;
use yii\db\Migration;

/**
 *
 *
 */
class m150610_162817_oauth extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable('{{%oauth2_client}}', [
            'client_id' => Schema::TYPE_STRING . '(80) NOT NULL',
            'client_secret' => Schema::TYPE_STRING . '(80) NOT NULL',
            'redirect_uri' => Schema::TYPE_TEXT . ' NOT NULL',
            'grant_type' => Schema::TYPE_TEXT,
            'scope' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'PRIMARY KEY (client_id)',
        ]);

        $this->createTable('{{%oauth2_access_token}}', [
            'access_token' => Schema::TYPE_STRING . '(40) NOT NULL',
            'client_id' => Schema::TYPE_STRING . '(80) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER,
            'expires' => Schema::TYPE_INTEGER . ' NOT NULL',
            'scope' => Schema::TYPE_TEXT,
            'PRIMARY KEY (access_token)',
        ]);

        $this->createTable('{{%oauth2_refresh_token}}', [
            'refresh_token' => Schema::TYPE_STRING . '(40) NOT NULL',
            'client_id' => Schema::TYPE_STRING . '(80) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER,
            'expires' => Schema::TYPE_INTEGER . ' NOT NULL',
            'scope' => Schema::TYPE_TEXT,
            'PRIMARY KEY (refresh_token)',
        ]);

        $this->createTable('{{%oauth2_authorization_code}}', [
            'authorization_code' => Schema::TYPE_STRING . '(40) NOT NULL',
            'client_id' => Schema::TYPE_STRING . '(80) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER,
            'redirect_uri' => Schema::TYPE_TEXT . ' NOT NULL',
            'expires' => Schema::TYPE_INTEGER . ' NOT NULL',
            'scope' => Schema::TYPE_TEXT,
            'PRIMARY KEY (authorization_code)',
        ]);

        $this->addforeignkey('fk_refresh_token_oauth2_client_client_id', '{{%oauth2_refresh_token}}', 'client_id', '{{%oauth2_client}}', 'client_id', 'cascade', 'cascade');
        $this->addforeignkey('fk_authorization_code_oauth2_client_client_id', '{{%oauth2_authorization_code}}', 'client_id', '{{%oauth2_client}}', 'client_id', 'cascade', 'cascade');
        $this->addforeignkey('fk_access_token_oauth2_client_client_id', '{{%oauth2_access_token}}', 'client_id', '{{%oauth2_client}}', 'client_id', 'cascade', 'cascade');

        $this->createIndex('ix_authorization_code_expires', '{{%oauth2_authorization_code}}', 'expires');
        $this->createIndex('ix_refresh_token_expires', '{{%oauth2_refresh_token}}', 'expires');
        $this->createIndex('ix_access_token_expires', '{{%oauth2_access_token}}', 'expires');
    }

    public function safeDown()
    {
        $this->dropTable('{{%oauth2_authorization_code}}');
        $this->dropTable('{{%oauth2_refresh_token}}');
        $this->dropTable('{{%oauth2_access_token}}');
        $this->dropTable('{{%oauth2_client}}');
    }
}
