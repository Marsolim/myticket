<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\tickets\actions\Action;

/**
 * TicketActionSearch represents the model behind the search form of `frontend\models\TicketAction`.
 */
class ActionSearch extends Action
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ticket_id', 'engineer_id', 'status_override'], 'integer'],
            [['summary'], 'safe'],
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
        $query = Action::find();

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->andWhere('0=1');
            return $query;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'status_override' => $this->status_override
        ]);

        $query->andFilterWhere(['like', 'summary', $this->summary]);

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
