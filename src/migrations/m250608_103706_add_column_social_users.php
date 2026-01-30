<?php


use yii\db\Migration;

class m250608_103706_add_column_social_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%social_users}}', 'fiscal_code', $this->string()->after('emailVerified'));
        $this->addColumn('{{%social_users}}', 'auth_level', $this->string()->after('emailVerified'));
        $this->addColumn('{{%social_users}}', 'auth_type', $this->string()->after('emailVerified'));
        $this->addColumn('{{%social_users}}', 'raw_data', $this->text()->after('organization_name'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%social_users}}', 'fiscal_code');
        $this->dropColumn('{{%social_users}}', 'auth_level');
        $this->dropColumn('{{%social_users}}', 'auth_type');
        $this->dropColumn('{{%social_users}}', 'raw_data');

        return true;
    }
}
