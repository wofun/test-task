<?php

use yii\db\Migration;

class m250710_093524_create_seed_posts_visitors_procedure extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
            CREATE PROCEDURE seedPostsVisitors()
            BEGIN

                DECLARE maxPostId INT;
                DECLARE maxUserId INT;
                DECLARE minUserId INT; 
                DECLARE postId INT;
                DECLARE userId INT;
                DECLARE visitorsCount INT;   

                SET maxPostId = (SELECT id FROM posts ORDER BY id DESC LIMIT 1);
                SET maxUserId = (SELECT id FROM user ORDER BY id DESC LIMIT 1);
            
                SET postId = (SELECT id FROM posts ORDER BY id ASC LIMIT 1);
                SET minUserId = (SELECT id FROM user ORDER BY id ASC LIMIT 1);

                SET userId = minUserId;

                post_loop: WHILE postId <= maxPostId DO

                    SET visitorsCount = 0;
                
                    user_loop: WHILE userId <= maxUserId DO

                        INSERT INTO `posts_visitors` (id_post, id_visitor, view_at)
                            VALUES(postId, userId, now());

                        SET visitorsCount = visitorsCount +1;
                        SET userId = userId+1;

                        IF visitorsCount >= 100 THEN 
                            LEAVE user_loop;
                        END IF;
                    END WHILE user_loop;

                    # UPDATE the posts.visitors_count column
                    UPDATE posts SET visitors_count = visitorsCount WHERE id = postId;

                    SET postId = postId+1;
                    SET userId = minUserId;
                END WHILE post_loop;
            END;
        SQL;


        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->execute('DROP PROCEDURE IF EXISTS seedPostsVisitors');
    }
}
