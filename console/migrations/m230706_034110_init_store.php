<?php

use yii\db\Migration;

/**
 * Class m230706_034110_init_store
 */
class m230706_034110_init_store extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230706_034110_init_store cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230706_034110_init_store cannot be reverted.\n";

        return false;
    }
    */
}
