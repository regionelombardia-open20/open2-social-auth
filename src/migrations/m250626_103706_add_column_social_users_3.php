<?php


use yii\db\Migration;

class m250626_103706_add_column_social_users_3 extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%social_users}}', 'access_token', $this->text()->after('auth_type'));
        $this->alterColumn('{{%social_users}}', 'refresh_token', $this->text()->after('auth_type'));

    }

    public function safeDown()
    {

        return true;
    }
}
