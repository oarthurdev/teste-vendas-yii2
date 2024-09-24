<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sales}}`.
 */

class m240923_132328_create_sales_table extends Migration
{
    public function safeUp()
    {
        // Criação da tabela de vendas
        $this->createTable('sales', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'total_price' => $this->decimal(10, 2)->notNull(),
            'installments' => $this->integer()->notNull(),
            'sale_date' => $this->timestamp()->notNull(),
        ]);

        $this->createTable('installments', [
            'id' => $this->primaryKey(),
            'sale_id' => $this->integer()->notNull(),
            'installment_number' => $this->integer()->notNull(),
            'installment_value' => $this->decimal(10, 2)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-sales-product_id',
            'sales',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-sales-user_id',
            'sales',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-installments-sale_id',
            'installments',
            'sale_id',
            'sales',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-installments-sale_id', 'installments');
        $this->dropForeignKey('fk-sales-product_id', 'sales');
        $this->dropForeignKey('fk-sales-user_id', 'sales');

        $this->dropTable('installments');
        $this->dropTable('sales');
    }
}
