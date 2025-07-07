<?php

use yii\db\Migration;

class m250707_165452_create_update_post_subscribers_count_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%posts}}', 'subscribers_count', $this->integer()->unsigned());

        $sql = "CREATE PROCEDURE updatePostSubscribersCount (IN postID INT)
                BEGIN
                    UPDATE posts 
                        SET subscribers_count = (SELECT COUNT(*) FROM posts_track WHERE id_post = postID)
                    WHERE id = postID;
                END;";
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP PROCEDURE IF EXISTS updatePostSubscribersCount');
        $this->dropColumn('{{%posts}}', 'subscribers_count');
    }
}
