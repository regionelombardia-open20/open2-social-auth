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
 * Class m170711_160734_create_table_social_users
 */
class m170711_160734_create_table_social_users extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%social_users}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->null()->comment('Birth Year'),
            'identifier' => $this->string(255)->notNull()->comment('Unique Identiffier for Provider'),
            'profileURL' => $this->string(255)->null()->comment('Provider Profile Url'),
            'webSiteURL' => $this->string(255)->null()->comment('Web Site Url'),
            'photoURL' => $this->string(255)->null()->comment('Photo Url'),
            'displayName' => $this->string(60)->null()->comment('User Display Name'),
            'description' => $this->string(255)->null()->comment('User Description'),
            'firstName' => $this->string(60)->null()->comment('First Name'),
            'lastName' => $this->string(60)->null()->comment('Last Name'),
            'gender' => $this->string(20)->null()->comment('Gender'),
            'language' => $this->string(20)->null()->comment('Language'),
            'email' => $this->string(255)->null()->comment('Email'),
            'emailVerified' => $this->string(255)->null()->comment('Email Veriffied'),
            'phone' => $this->string(45)->null()->comment('Phone Number'),
            'address' => $this->string(255)->null()->comment('Address'),
            'country' => $this->string(60)->null()->comment('Country'),
            'region' => $this->string(60)->null()->comment('Region'),
            'city' => $this->string(60)->null()->comment('City'),
            'zip' => $this->integer(10)->null()->comment('Zip Code'),
            'provider' => $this->string(20)->notNull()->comment('Provider Name'),
            'age' => $this->integer(3)->null()->comment('Age'),
            'birthDay' => $this->integer(2)->null()->comment('Birth Day'),
            'birthMonth' => $this->integer(2)->null()->comment('Birth Month'),
            'birthYear' => $this->integer(4)->null()->comment('Birth Year'),
            'job_title' => $this->string(45)->null()->comment('Job Title'),
            'organization_name' => $this->string(45)->null()->comment('Organization Name')
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
        $this->addForeignKey('fk_user_social_users', $this->getRawTableName(), 'user_id', '{{%user}}', 'id');
    }
}
