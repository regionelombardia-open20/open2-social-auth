<?php


use yii\db\Migration;

class m250610_103706_add_column_social_users_2 extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%social_users}}', 'access_token', $this->text()->after('auth_type'));
        $this->addColumn('{{%social_users}}', 'expire_at', $this->string()->after('auth_type'));
        $this->addColumn('{{%social_users}}', 'refresh_token', $this->text()->after('auth_type'));

    }

    public function safeDown()
    {
        $this->dropColumn('{{%social_users}}', 'access_token');
        $this->dropColumn('{{%social_users}}', 'expire_at');
        $this->dropColumn('{{%social_users}}', 'refresh_token');

        return true;
    }
}
