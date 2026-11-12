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
