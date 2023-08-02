<?php

use yii\db\Migration;

/**
 * Class m230706_034233_init_user_profile
 */
class m230706_034233_init_user_profile extends Migration
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
        echo "m230706_034233_init_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230706_034233_init_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
