<?php

use yii\db\Migration;

class m230228_094400_update_table_credential_request extends Migration {

    public function safeUp() {
        $table = Yii::$app->db->schema->getTableSchema('credential_request');
        if (!isset($table->columns['email'])) {
            $this->addColumn('credential_request', 'email', $this->text());
        }
    }

    public function safeDown() {
        $this->dropColumn('credential_request', 'email');
    }

}
