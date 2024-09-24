<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sales".
 *
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property int $quantity
 * @property string $total_price
 * @property string $sale_date
 *
 * @property Product $product
 * @property User $user
 * @property Installments[] $installmentsData
 */
class Sales extends \yii\db\ActiveRecord
{
    public $installmentValues;

    public static function tableName()
    {
        return 'sales';
    }

    public function rules()
    {
        return [
            [['product_id', 'user_id', 'quantity', 'total_price', 'installments', 'sale_date'], 'required'],
            [['product_id', 'user_id', 'quantity', 'installments'], 'integer'],
            [['sale_date', 'description'], 'safe'],
            [['total_price'], 'validatePrice'],
            [['installmentValues'], 'safe'],
            [['installmentValues'], 'each', 'rule' => ['number']],
            ['installmentValues', 'validateInstallmentValues'],
        ];
    }

    public function saveInstallments()
    {
        if ($this->installmentValues) {
            foreach ($this->installmentValues as $index => $value) {
                $installment = new Installments();
                $installment->sale_id = $this->id;
                $installment->installment_number = $index + 1;
                $installment->installment_value = $value;

                if (!$installment->save()) {
                    Yii::error("Erro ao salvar a parcela: " . json_encode($installment->getErrors()));
                }
            }
        }
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function validateInstallmentValues($attribute, $params)
    {
        $totalInstallmentsValue = array_sum($this->installmentValues);
        if ($totalInstallmentsValue != $this->total_price) {
            $this->addError($attribute, 'A soma dos valores das parcelas deve ser igual ao valor total da venda.');
        }
    }

    public function getInstallmentsValue($installmentNumber)
    {
        if ($installmentNumber > 0 && $installmentNumber <= $this->installments) {
            return $this->total_price / $this->installments;
        }
        
        return null;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->total_price = $this->product->price * $this->quantity; // Multiplica o preço do produto pela quantidade
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Installments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstallmentsData()
    {
        return $this->hasMany(Installments::className(), ['sale_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $this->saveInstallments();
        }
    }

    public function validatePrice($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        
        $cleanedPrice = $this->removePrefix($this->$attribute);
        
        if (!is_numeric($cleanedPrice)) {
            $this->addError($attribute, 'O preço deve ser um número.');
        } else {
            $this->$attribute = (float)$cleanedPrice;
        }
    }

    private function removePrefix($price)
    {
        return str_replace(['$', ' '], '', $price);
    }
}
