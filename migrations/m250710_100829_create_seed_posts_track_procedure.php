<?php

use yii\db\Migration;

class m250710_100829_create_seed_posts_track_procedure extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
            CREATE PROCEDURE seedPostsTrack()
            BEGIN

                DECLARE maxPostId INT;
                DECLARE maxUserId INT;
                DECLARE minUserId INT; 
                DECLARE postId INT;
                DECLARE userId INT;
                DECLARE subscribersCount INT;   

                SET maxPostId = (SELECT id FROM posts ORDER BY id DESC LIMIT 1);
                SET maxUserId = (SELECT id FROM user ORDER BY id DESC LIMIT 1);
            
                SET postId = (SELECT id FROM posts ORDER BY id ASC LIMIT 1);
                SET minUserId = (SELECT id FROM user ORDER BY id ASC LIMIT 1);

                SET userId = minUserId;

                post_loop: WHILE postId <= maxPostId DO

                    SET subscribersCount = 0;
                
                    user_loop: WHILE userId <= maxUserId DO

                        INSERT INTO `posts_track` (id_post, id_user, track_at)
                            VALUES(postId, userId, now());

                        SET subscribersCount = subscribersCount +1;
                        SET userId = userId+1;

                        IF subscribersCount >= 10 THEN 
                            LEAVE user_loop;
                        END IF;
                    END WHILE user_loop;

                    # UPDATE the posts.subscribers_count column
                    UPDATE posts SET subscribers_count = subscribersCount WHERE id = postId;

                    SET postId = postId+1;
                    SET userId = minUserId;
                END WHILE post_loop;
            END;
        SQL;


        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->execute('DROP PROCEDURE IF EXISTS seedPostsTrack');
    }
}
