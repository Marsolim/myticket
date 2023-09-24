<?php

namespace frontend\models\search;

use common\db\ObjectQuery;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\tickets\Ticket;

/**
 * TicketSearch represents the model behind the search form of `frontend\models\Ticket`.
 */
class TicketSearch extends Ticket
{
    public $searchstring;

    public $date_range;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id'], 'integer'],
            [['number', 'problem'], 'safe'],
            [['searchstring', 'date_range'], 'safe'],
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

    // /**
    //  * Creates data provider instance with search query applied
    //  *
    //  * @param array $params
    //  *
    //  * @return ActiveDataProvider
    //  */
    // public function search($query)
    // {
    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //         'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
    //         'pagination'=>[
    //             'pageSize' => 10
    //         ]
    //     ]);

    //     if (!$this->validate()) {
    //         // uncomment the following line if you do not want to return any records when validation fails
    //         $query->andFilterWhere('0=1');
    //         return $dataProvider;
    //     }

    //     return $dataProvider;
    // }

    public function search($params)
    {
        // create ActiveQuery
        $query = Ticket::find();
        // Important: lets join the query with our previously mentioned relations
        // I do not make any other configuration like aliases or whatever, feel free
        // to investigate that your self
        $query->joinWith(['store']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination'=>[
                'pageSize' => 10
            ]
        ]);

        // // Important: here is how we set up the sorting
        // // The key is the attribute name on our "TourSearch" instance
        // $dataProvider->sort->attributes['city'] = [
        //     // The tables are the ones our relation are configured to
        //     // in my case they are prefixed with "tbl_"
        //     'asc' => ['tbl_city.name' => SORT_ASC],
        //     'desc' => ['tbl_city.name' => SORT_DESC],
        // ];
        // // Lets do the same with country now
        // $dataProvider->sort->attributes['country'] = [
        //     'asc' => ['tbl_country.name' => SORT_ASC],
        //     'desc' => ['tbl_country.name' => SORT_DESC],
        // ];
        // No search? Then return data Provider
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $rdate = explode(' - ', $this->date_range);
        // We have to do some search... Lets do some magic
        $query->andFilterWhere(
            ['between', 'ticket.created_at', strtotime($rdate[0]), strtotime($rdate[1])])
        // Here we search the attributes of our relations using our previously configured
        // ones in "TourSearch"
        ->orFilterWhere(['like', 'customer.name', $this->searchstring])
        ->orFilterWhere(['like', 'ticket.number', $this->searchstring])
        ->orFilterWhere(['like', 'ticket.external_number', $this->searchstring])
        ->orFilterWhere(['like', 'ticket.problem', $this->searchstring]);

        return $dataProvider;
    }

    // /**
    //  * Creates data provider instance with search query applied
    //  *
    //  * @param array $params
    //  *
    //  * @return ActiveQuery
    //  */
    // public function searchQuery($params)
    // {
    //     $query = new ObjectQuery(Ticket::class);
    //     $query->innerJoin()->groupBy(['ticket.number', 'action.summary', 'customer.name', 'customer.code'])
    //     // add conditions that should always apply here

    //     //$dataProvider = new ActiveDataProvider([
    //     //    'query' => $query,
    //     //]);
        
    //     $this->load($params);

    //     if (!$this->validate()) {
    //         // uncomment the following line if you do not want to return any records when validation fails
    //         $query->andWhere('0=1');
    //         return $query;
    //     }

    //     // grid filtering conditions
    //     $query->andFilterWhere([
    //         'id' => $this->id,
    //         'customer_id' => $this->customer_id,
    //     ]);

    //     $query->orFilterWhere(['like', 'number', $this->searchstring])
    //         ->orFilterWhere(['like', 'problem', $this->searchstring]);

    //     return $query;
    // }
}
