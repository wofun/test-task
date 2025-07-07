<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts_visitors}}`.
 */
class m250706_084242_create_posts_visitors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%posts_visitors}}', [
            'id_post' => $this->integer()->notNull(),
            'id_visitor' => $this->integer()->notNull(),
            'view_at' => $this->dateTime()->notNull(),
        ]);
        $this->addPrimaryKey('posts_visitors_pk', '{{%posts_visitors}}', ['id_post', 'id_visitor']);

        $this->createIndex('id_post_idx', '{{%posts_visitors}}', 'id_post');
        $this->addForeignKey('posts_visitors_post_fk', '{{%posts_visitors}}', 'id_post', '{{%posts}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('id_visitor_idx', '{{%posts_visitors}}', 'id_visitor');
        $this->addForeignKey('posts_visitors_user_fk', '{{%posts_visitors}}', 'id_visitor', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('posts_visitors_post_fk', '{{%posts_visitors}}');
        $this->dropIndex('id_post_idx', '{{%posts_visitors}}');

        $this->dropForeignKey('posts_visitors_user_fk', '{{%posts_visitors}}');
        $this->dropIndex('id_visitor_idx', '{{%posts_visitors}}');

        $this->dropTable('{{%posts_visitors}}');
    }
}
