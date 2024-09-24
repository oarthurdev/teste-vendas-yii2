<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class SalesSearch extends Sales
{
    public function rules()
    {
        return [
            [['id', 'product_id', 'user_id', 'quantity'], 'integer'],
            [['total_price'], 'number'],
            [['sale_date'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Sales::find();

        $query->joinWith(['product', 'user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['product.id' => $this->product_id])
              ->andFilterWhere(['user.id' => $this->user_id])
              ->andFilterWhere(['quantity' => $this->quantity])
              ->andFilterWhere(['total_price' => $this->total_price])
              ->andFilterWhere(['sale_date' => $this->sale_date]);

        return $dataProvider;
    }
}
