<?php

namespace frontend\models\search;

use common\db\ObjectQuery;
use common\models\actors\Engineer;
use common\models\actors\User;
use common\models\tickets\actions\closings\Awaiting;
use common\models\tickets\actions\closings\Duplicate;
use common\models\tickets\actions\closings\NoProblem;
use common\models\tickets\actions\closings\Normal;
use common\models\tickets\actions\Open;
use common\models\tickets\actions\Repair;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\tickets\Ticket;
use frontend\helpers\UserHelper;
use frontend\models\GeneralManager;
use frontend\models\StoreManager;
use Yii;

/**
 * TicketSearch represents the model behind the search form of `common\models\tickets\Ticket`.
 */
class TicketSearch extends Model
{
    //public $id;

    public $searchstring;

    public $date_range;

    public $cust;

    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['id'], 'integer'],
            [['searchstring', 'date_range', 'status', 'cust'], 'safe'],
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
        $query = new ObjectQuery(Ticket::class);
        // Important: lets join the query with our previously mentioned relations
        // I do not make any other configuration like aliases or whatever, feel free
        // to investigate that your self
        $query->joinWith(['store s'], false);
        
        $user = User::findOne(['id' => Yii::$app->user->id]);
        if (!empty($user) && $user instanceof Engineer){
            $query->joinWith(['assignments ass'], false);
            $query->andFilterWhere(['ass.user_id' => $user->id]);
        }

        if (!empty($user) && $user instanceof StoreManager){
            $query->andFilterWhere(['s.parent_id' => $user->associate_id]);
        }

        if (!empty($user) && $user instanceof GeneralManager){
            $query->joinWith(['store.depot dc'], false);
            $query->andFilterWhere(['dc.parent_id' => $user->associate_id]);
        }

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
        //if (!($this->load($params) && $this->validate())) {
        //    return $dataProvider;
        //}
        $this->load($params);
        if (!($this->validate())) {
            return $dataProvider;
        }

        if (!empty($this->date_range)){
            $rdate = explode(' - ', $this->date_range);
            // We have to do some search... Lets do some magic
            $query->andFilterWhere(
                ['BETWEEN', 'ticket.created_at', strtotime($rdate[0]), strtotime($rdate[1])]
            );
        }
        // Here we search the attributes of our relations using our previously configured
        // ones in "TourSearch"
        
        if (!empty($this->cust)){
            $query->andFilterWhere(
                ['OR', ['ticket.customer_id' => $this->cust], ['s.parent_id' => $this->cust]]
            );
        }
        
        if (!empty($this->status)){
            $type = match ($this->status) {
                'b' => Open::class,
                'p' => Repair::class,
                's' => Normal::class,
                'r' => Awaiting::class,
                'n' => NoProblem::class,
                'd' => Duplicate::class,
            };
            $query->joinWith(['lastAction la'], false);
            $query->andFilterWhere(['la.type' => $type]);
        }

        if (!empty($this->searchstring)){
            $query->andFilterWhere(
                ['OR', 
                    ['LIKE', 's.name', $this->searchstring],
                    ['LIKE', 'ticket.number', $this->searchstring],
                    ['LIKE', 'ticket.external_number', $this->searchstring],
                    ['LIKE', 'ticket.problem', $this->searchstring]
                ]
            );
        }

        return $dataProvider;
    }
}
