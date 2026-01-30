<?php

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
