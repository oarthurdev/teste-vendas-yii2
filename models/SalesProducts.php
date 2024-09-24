<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class SalesProducts extends ActiveRecord
{
    public static function tableName()
    {
        return 'sales_products'; // Nome da tabela de junção
    }

    public function getSale()
    {
        return $this->hasOne(Sales::class, ['id' => 'sales_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
}
