<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\tickets\actions\Repair;

/**
 * TicketActionSearch represents the model behind the search form of `frontend\models\TicketAction`.
 */
class RepairActionSearch extends Repair
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ticket_id', 'user_id', 'item_id'], 'integer'],
            [['action', 'serial'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchQuery($params)
    {
        $query = Repair::find();

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->andWhere('0=1');
            return $query;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ticket_id' => $this->ticket_id
        ]);

        $query->andFilterWhere(['like', 'summary', $this->summary]);
        $query->orFilterWhere(['like', 'action', $this->action]);
        $query->orFilterWhere(['like', 'serial', $this->serial]);

        return $query;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
