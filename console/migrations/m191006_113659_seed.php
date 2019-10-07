<?php

use yii\db\Migration;

/**
 * Class m191006_113659_seed
 */
class m191006_113659_seed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        for ($i = 1; $i <= 10; $i++) {

            $this->insert('gift_code',array(
                'token'=>substr(strtoupper(md5(microtime())), 0, 8),
                'status' =>10,
                'expires_at' =>time(),
                'created_at' =>time(),
                'updated_at' =>time(),
            ));

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('gift_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191006_113659_seed cannot be reverted.\n";

        return false;
    }
    */
}
