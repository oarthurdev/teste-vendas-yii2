<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['description'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Se a validação falhar, retorna todos os produtos.
            return $dataProvider;
        }

        // Adiciona filtros com base nos parâmetros de busca
        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['price' => $this->price]);

        return $dataProvider;
    }
}
