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

use yii\db\Migration;

class m231009_163000_alter_table_oauth2_client extends Migration
{
    const TABLE = '{{%oauth2_client}}';

    public function safeUp()
    {
        try {
            $this->dropColumn(self::TABLE, 'created_at');
            $this->dropColumn(self::TABLE, 'created_by');
            $this->dropColumn(self::TABLE, 'updated_by');
            $this->dropColumn(self::TABLE, 'updated_at');
            $this->dropColumn(self::TABLE, 'deleted_at');
            $this->dropColumn(self::TABLE, 'deleted_by');
        } catch (\yii\db\Exception $e) {}

        try {
            $this->addColumn(self::TABLE, 'created_by', $this->integer()->null()->defaultValue(null)->after('scope'));
            $this->addColumn(self::TABLE, 'created_at', $this->dateTime()->null()->defaultValue(null)->after('created_by'));
            $this->addColumn(self::TABLE, 'updated_by', $this->integer()->null()->defaultValue(null)->after('created_by'));
            $this->addColumn(self::TABLE, 'updated_at', $this->dateTime()->null()->defaultValue(null)->after('updated_by'));
            $this->addColumn(self::TABLE, 'deleted_by', $this->integer()->null()->defaultValue(null)->after('updated_at'));
            $this->addColumn(self::TABLE, 'deleted_at', $this->dateTime()->null()->defaultValue(null)->after('deleted_by'));
        } catch (\yii\db\Exception $e) {}

        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");

        try {
            $this->dropForeignKey('fk_refresh_token_oauth2_client_client_id', '{{%oauth2_refresh_token}}');
        } catch (\yii\db\Exception $e) {}

        try {
            $this->dropForeignKey('fk_authorization_code_oauth2_client_client_id', '{{%oauth2_authorization_code}}');
        } catch (\yii\db\Exception $e) {}

        try {
            $this->dropForeignKey('fk_access_token_oauth2_client_client_id', '{{%oauth2_access_token}}');
        } catch (\yii\db\Exception $e) {}

        $this->dropPrimaryKey('client_id', self::TABLE);
        $this->addColumn(self::TABLE, 'id', $this->primaryKey()->first());
        $this->regenerateForeignKeys();

        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function safeDown()
    {
        $this->dropColumn(self::TABLE, 'created_at');
        $this->dropColumn(self::TABLE, 'created_by');
        $this->dropColumn(self::TABLE, 'updated_by');
        $this->dropColumn(self::TABLE, 'updated_at');

        $this->addColumn(self::TABLE, 'created_at', $this->integer(11)->notNull()->defaultValue(null)->after('scope'));
        $this->addColumn(self::TABLE, 'updated_at', $this->integer(11)->notNull()->defaultValue(null)->after('created_at'));
        $this->addColumn(self::TABLE, 'created_by', $this->integer(11)->notNull()->defaultValue(null)->after('updated_at'));
        $this->addColumn(self::TABLE, 'updated_by', $this->integer(11)->notNull()->defaultValue(null)->after('created_by'));

        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");

        $this->dropColumn(self::TABLE, 'id');
        $this->addPrimaryKey('client_id', self::TABLE, 'client_id');

        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    protected function regenerateForeignKeys()
    {

        $this->createIndex('idx_client_id', '{{%oauth2_client}}', 'client_id');

        $this->addForeignKey('fk_refresh_token_oauth2_client_client_id', '{{%oauth2_refresh_token}}', 'client_id', '{{%oauth2_client}}', 'client_id', 'cascade', 'cascade');
        $this->addForeignKey('fk_authorization_code_oauth2_client_client_id', '{{%oauth2_authorization_code}}', 'client_id', '{{%oauth2_client}}', 'client_id', 'cascade', 'cascade');
        $this->addForeignKey('fk_access_token_oauth2_client_client_id', '{{%oauth2_access_token}}', 'client_id', '{{%oauth2_client}}', 'client_id', 'cascade', 'cascade');
    }
}
