<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts}}`.
 */
class m250706_082149_create_posts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'text' => $this->text()->notNull(),
            'fields' => $this->json(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('created_by_idx', '{{%posts}}', 'created_by');
        $this->addForeignKey('posts_user_fk', '{{%posts}}', 'created_by', 'user', 'id', null, 'CASCADE');

        $this->execute("CREATE FULLTEXT INDEX `post_text_idx` ON posts (`name`)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('post_text_idx', '{{%posts}}');

        $this->dropForeignKey('posts_user_fk', '{{%posts}}');
        $this->dropIndex('created_by_idx', '{{%posts}}');

        $this->dropTable('{{%posts}}');
    }
}
