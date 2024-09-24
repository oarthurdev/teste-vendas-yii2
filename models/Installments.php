<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Installments extends ActiveRecord
{
    public static function tableName()
    {
        return 'installments';
    }

    public function rules()
    {
        return [
            [['sale_id', 'installment_number', 'installment_value'], 'required'],
            [['sale_id', 'installment_number'], 'integer'],
            [['installment_value'], 'number', 'min' => 0.01],
            ['installment_value', 'validateInstallmentValue'],
        ];
    }

    public function validateInstallmentValue($attribute, $params)
    {
        $sale = Sales::findOne($this->sale_id);
        if ($sale) {
            $totalInstallments = Installments::find()
                ->where(['sale_id' => $this->sale_id])
                ->sum('installment_value');

            if ($totalInstallments + $this->installment_value > $sale->total_price) {
                $this->addError($attribute, 'A soma das parcelas não pode exceder o valor total da venda.');
            }
        }
    }

    public function getSale()
    {
        return $this->hasOne(Sales::class, ['id' => 'sale_id']);
    }

    public function getInstallmentDetails()
    {
        return [
            'installment_number' => $this->installment_number,
            'installment_value' => $this->installment_value,
        ];
    }
}
