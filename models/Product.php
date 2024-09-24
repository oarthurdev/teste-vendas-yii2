<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $quantity
 */
class Product extends ActiveRecord
{
    public $image;
    public $original_name;

    public static function tableName()
    {
        return 'product';
    }

    public function rules()
    {
        return [
            [['name', 'price', 'quantity'], 'required'],
            [['price'], 'validatePrice'],
            [['quantity'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['product_image'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 5]
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'image' => 'Image'
        ];
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
