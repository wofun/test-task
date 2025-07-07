<?php

use yii\db\Migration;

class m250707_152036_create_update_post_visitors_count_procedure extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%posts}}', 'visitors_count', $this->integer()->unsigned());

        $sql = "CREATE PROCEDURE updatePostVisitorsCount (IN postID INT)
                BEGIN
                    UPDATE posts 
                        SET visitors_count = (SELECT COUNT(*) FROM posts_visitors WHERE id_post = postID)
                    WHERE id = postID;
                END;";
        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->execute('DROP PROCEDURE IF EXISTS updatePostVisitorsCount');
        $this->dropColumn('{{%posts}}', 'visitors_count');
    }
}
