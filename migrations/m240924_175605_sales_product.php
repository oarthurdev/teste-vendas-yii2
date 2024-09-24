<?php

use yii\db\Migration;

/**
 * Class m240924_175605_sales_product
 */
class m240924_175605_sales_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('sales_products', [
            'id' => $this->primaryKey(),
            'sales_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Criação de chaves estrangeiras
        $this->addForeignKey(
            'fk-sales-products-sales_id',
            'sales_products',
            'sales_id',
            'sales',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-sales-products-product_id',
            'sales_products',
            'product_id',
            'product',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove as chaves estrangeiras primeiro
        $this->dropForeignKey('fk-sales-products-sales_id', 'sales_products');
        $this->dropForeignKey('fk-sales-products-product_id', 'sales_products');

        $this->dropTable('sales_products');
    }
}
