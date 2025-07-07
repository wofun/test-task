<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts_track}}`.
 */
class m250706_085223_create_posts_track_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%posts_track}}', [
            'id_post' => $this->integer()->notNull(),
            'id_user' => $this->integer()->notNull(),
            'track_at' => $this->dateTime()->notNull(),
        ]);
        $this->addPrimaryKey('posts_track_pk', '{{%posts_track}}', ['id_post', 'id_user']);

        $this->createIndex('id_post_idx', '{{%posts_track}}', 'id_post');
        $this->addForeignKey('posts_track_post_fk', '{{%posts_track}}', 'id_post', '{{%posts}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('id_user_idx', '{{%posts_track}}', 'id_user');
        $this->addForeignKey('posts_track_user_fk', '{{%posts_track}}', 'id_user', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('posts_track_post_fk', '{{%posts_track}}');
        $this->dropIndex('id_post_idx', '{{%posts_track}}');

        $this->dropForeignKey('posts_track_user_fk', '{{%posts_track}}');
        $this->dropIndex('id_user_idx', '{{%posts_track}}');

        $this->dropTable('{{%posts_track}}');
    }
}
