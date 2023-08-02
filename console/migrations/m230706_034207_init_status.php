<?php

use yii\db\Migration;

/**
 * Class m230706_034207_init_status
 */
class m230706_034207_init_status extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%status}}', [
            'id' => $this->primaryKey(),
            'status_type' => $this->integer()->notNull(),
            'enum_value' => $this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'code' => $this->string(5),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('IDX_key_enum', '{{%status}}', ['status_type', 'enum_value'], true);
        $this->createIndex('IDX_key_name', '{{%status}}', ['status_type', 'name'], true);

        $this->batchInsert('{{%status}}', 
            ['status_type', 'enum_value', 'name', 'code', 'created_at', 'updated_at'],
            [
                [1, 1, 'Garansi', 'G', time(), time()],
                [1, 2, 'Tidak Garansi', 'NG', time(), time()],
                [2, 1, 'Aktif', 'A', time(), time()],
                [2, 2, 'Tidak Aktif', 'NA', time(), time()],
                [3, 1, 'Open', 'O', time(), time()],
                [4, 2, 'Resolving', 'P', time(), time()],
                [4, 3, 'Resolved', 'PF', time(), time()],
                [4, 4, 'Resolved (Waiting for IT)', 'PW', time(), time()],
                [5, 5, 'Closed (Resolved)', 'C', time(), time()],
                [3, 6, 'Closed (no Issue)', 'CNI', time(), time()],
                [3, 7, 'Closed (on Duplicate)', 'CD', time(), time()],
                [3, 6, 'Closed (no Response)', 'CNR', time(), time()],
            ]
        );

        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique(),
            'code' => $this->string(5)->notNull()->unique(),
            'zip_codes' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->batchInsert('{{%region}}', 
            ['name', 'code', 'zip_code', 'created_at', 'updated_at'],
            [
                [1, 1, 'Garansi', 'G', time(), time()],
                [1, 2, 'Tidak Garansi', 'NG', time(), time()],
                [2, 1, 'Aktif', 'A', time(), time()],
                [2, 2, 'Tidak Aktif', 'NA', time(), time()],
                [3, 1, 'Open', 'O', time(), time()],
                [4, 2, 'Resolving', 'P', time(), time()],
                [4, 3, 'Resolved', 'PF', time(), time()],
                [4, 4, 'Resolved (Waiting for IT)', 'PW', time(), time()],
                [5, 5, 'Closed', 'C', time(), time()],
                [3, 6, 'Closed (no Issue)', 'CNI', time(), time()],
                [3, 7, 'Closed (on Duplicate)', 'CD', time(), time()],
                [3, 6, 'Closed (no Response)', 'CNR', time(), time()],
            ]
        );
    }

    public function safeDown()
    {
        $this->dropIndex('IDX_key_enum');
        $this->dropIndex('IDX_key_name');
        $this->dropTable('{{%status}}');
    }
}
