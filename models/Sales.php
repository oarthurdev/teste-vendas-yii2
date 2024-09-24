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
            [['user_id', 'installments'], 'integer'],
            [['product_id', 'quantity'], 'each', 'rule' => ['integer']],
            [['sale_date', 'description'], 'safe'],
            [['total_price'], 'validatePrice'],
            [['installmentValues'], 'safe'],
            [['installmentValues'], 'each', 'rule' => ['number']],
            [['installmentValues'], 'validateInstallmentValues'],
        ];
    }

    public function calculateTotalPrice()
    {
        $total = 0;
        foreach ($this->product_id as $index => $productId) {
            $product = Product::findOne($productId);
            if ($product) {
                $total += $product->price * $this->quantity[$index];
            }
        }
        $this->total_price = $total;
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

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->via('salesProducts'); // Nome da função que define a relação com a tabela de junção
    }

    public function getSalesProducts()
    {
        return $this->hasMany(SalesProducts::class, ['sales_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function validateInstallmentValues($attribute, $params)
    {
        // Obtém o valor total da venda
        $totalPrice = $this->total_price;

        // Calcula o total das parcelas
        $totalInstallments = array_sum($this->installmentValues);

        // Verifica se o total das parcelas é igual ao total da venda
        if ($totalInstallments != $totalPrice) {
            $this->addError($attribute, 'O total das parcelas deve ser igual ao valor total da venda.');
        }
    }

    public function getInstallmentsValue($installmentNumber)
    {
        if ($installmentNumber > 0 && $installmentNumber <= $this->installments) {
            return $this->total_price / $this->installments;
        }
        
        return null;
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
