<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\comments\models\search
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

namespace open20\amos\socialauth\models\search;

use open20\amos\socialauth\models\Oauth2Client;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class Oauth2ClientSearch
 * @package open20\amos\socialauth\models\search
 */
class Oauth2ClientSearch extends Oauth2Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['id', 'created_by', 'updated_by', 'deleted_by'],
                'integer'
            ],
            [
                ['client_id', 'client_secret', 'redirect_uri', 'grant_type', 'scope', 'updated_at', 'deleted_at'],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $query = Oauth2Client::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'client_id', $this->client_id]);
        $query->andFilterWhere(['like', 'redirect_uri', $this->redirect_uri]);
        $query->andFilterWhere(['like', 'client_secret', $this->client_secret]);
        $query->andFilterWhere(['like', 'grant_type', $this->grant_type]);
        $query->andFilterWhere(['like', 'scope', $this->scope]);

        return $dataProvider;
    }
}
