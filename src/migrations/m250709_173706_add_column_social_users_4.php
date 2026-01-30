<?php


use yii\db\Migration;

class m250709_173706_add_column_social_users_4 extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%social_users}}', 'token_invitation', $this->string()->after('raw_data'));

    }

    public function safeDown()
    {
        $this->dropColumn('{{%social_users}}', 'token_invitation');
    }

}
