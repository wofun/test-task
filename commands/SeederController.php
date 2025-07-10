<?php

namespace app\commands;

use app\components\EmptyLogger;
use app\components\faker_data_generators\Post as PostGenerator;
use app\components\faker_data_generators\User as UserGenerator;
use app\helpers\DbHelper;
use app\models\AuthAssignment;
use app\models\Post;
use app\models\PostTrack;
use app\models\PostVisitor;
use app\models\Profile;
use app\models\User;
use Exception;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use yii\helpers\StringHelper;

class SeederController extends Controller
{
    public function init()
    {
        parent::init();

        Yii::setLogger(new EmptyLogger());
        DbHelper::disableForeignKeyChecks();
    }

    public function __destruct()
    {
        DbHelper::enableForeignKeyChecks();
    }

    /**
     * This command seeds all database tables.
     * 
     * @return int Exit code
     */
    public function actionIndex()
    {
        // $time = time();
        $this->actionUsers(100000);
        $this->actionPosts(1000000);
        $this->actionPostsVisitors();
        $this->actionPostsTrack();
        // echo 'The process took ' . date('H:i:s', (time() - $time)) . PHP_EOL;

        return ExitCode::OK;
    }


    /**
     * This command seeds the users table.
     * 
     * @param int $amount Amount of users to seed
     * @return int Exit code
     */
    public function actionUsers($amount = 100000, $amountPerBatch = 10000)
    {
        if ($amountPerBatch > $amount) {
            $amountPerBatch = $amount;
        }

        $tableName = User::tableName();
        $dataGenerator = new UserGenerator($amount);

        $this->truncateTable(AuthAssignment::tableName());
        $this->truncateTable(Profile::tableName());
        $this->truncateTable($tableName, true);

        echo "Seeding the {$tableName} table ";
        $inserted = 0;

        foreach ($dataGenerator($amountPerBatch) as $batch) {
            try {
                Yii::$app->db->createCommand()->batchInsert(User::tableName(), $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                echo '.';
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        $this->showInsertedInfo($tableName, $inserted);

        return ExitCode::OK;
    }


    /**
     * This command seeds the posts table.
     * 
     * @param int $amount Amount of posts to seed
     * @return int Exit code
     */
    public function actionPosts($amount = 1000000, $amountPerBatch = 25000)
    {
        if ($amountPerBatch > $amount) {
            $amountPerBatch = $amount;
        }

        $tableName = Post::tableName();
        $dataGenerator = new PostGenerator($amount);

        $this->truncateTable($tableName, true);

        echo "Seeding the {$tableName} table ";
        $inserted = 0;

        foreach (
            $dataGenerator($amountPerBatch, [
                'userIdFrom' => User::find()->min('id'),
                'userIdTo' => User::find()->max('id')
            ]) as $batch
        ) {
            try {
                Yii::$app->db->createCommand()->batchInsert(Post::tableName(), $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                echo '.';
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        $this->showInsertedInfo($tableName, $inserted);

        return ExitCode::OK;
    }


    /**
     * This command seeds the Posts Visitors table.
     * 
     * @return int Exit code
     */
    public function actionPostsVisitors()
    {
        $tableName = PostVisitor::tableName();

        $this->truncateTable($tableName);

        PostVisitor::dropIndexesAndForeignKeys();

        $inserted = 0;
        echo "Seeding the {$tableName} table ";

        foreach (
            $this->getPostVisitorBatch($amountPerBatch = 100000, [
                'postIdFrom' => Post::find()->min('id'),
                'postIdTo' =>  Post::find()->max('id'),
                'userIdFrom' => User::find()->min('id'),
                'userIdTo' => User::find()->max('id'),
            ]) as $batch
        ) {
            if (empty($batch)) {
                continue;
            }
            try {
                Yii::$app->db->createCommand()->batchInsert($tableName, $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                if ($inserted % 100000 === 0) {
                    echo '.';
                    if ($inserted % 1000000 === 0) {
                        // echo '';
                    }
                }
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        PostVisitor::addIndexesAndForeignKeys();

        $this->showInsertedInfo($tableName, $inserted);

        return ExitCode::OK;
    }


    private function getPostVisitorBatch(int $amountPerBatch = 1000, array $options = [])
    {
        $data = [];
        for ($i = $options['postIdFrom']; $i <= $options['postIdTo']; $i++) {
            $visitorsCount = 0;
            $userId = rand($options['userIdFrom'], $options['userIdTo'] - 150);
            for ($k = $userId; $k < $userId + rand(100, 150); $k++) {
                $visitorsCount++;
                $data[] = [
                    'id_post' => $i,
                    'id_visitor' => $k,
                    'view_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    $data = [];
                }
            }
            Yii::$app->db->createCommand("UPDATE " . Post::tableName() . " SET visitors_count = {$visitorsCount} WHERE id = {$i}")->execute();
        }
        if (!empty($data)) {
            yield $data;
        }
    }

    /**
     * This command seeds the Posts Track table.
     * 
     * @return int Exit code
     */
    public function actionPostsTrack()
    {
        $tableName = PostTrack::tableName();

        $this->truncateTable($tableName);

        PostTrack::dropIndexesAndForeignKeys();

        $inserted = 0;
        echo "Seeding the {$tableName} table ";

        foreach (
            $this->getPostTrackBatch(
                $amountPerBatch = 25000,
                [
                    'postIdFrom' => Post::find()->min('id'),
                    'postIdTo' => Post::find()->max('id'),
                    'userIdFrom' => User::find()->min('id'),
                    'userIdTo' => User::find()->max('id'),
                ]
            ) as $batch
        ) {
            if (empty($batch)) {
                continue;
            }
            try {
                Yii::$app->db->createCommand()->batchInsert($tableName, $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                if ($inserted % 100000 === 0) {
                    echo '.';
                }
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        $this->showInsertedInfo($tableName, $inserted);

        PostTrack::addIndexesAndForeignKeys();

        return ExitCode::OK;
    }


    private function getPostTrackBatch(int $amountPerBatch = 1000, array $options = [])
    {
        $data = [];
        for ($i = $options['postIdFrom']; $i <= $options['postIdTo']; $i++) {
            $subscribersCount = 0;
            $userId = rand($options['userIdFrom'], $options['userIdTo'] - 20);
            for ($k = $userId; $k < $userId + rand(10, 20); $k++) {
                $subscribersCount++;
                $data[] = [
                    'id_post' => $i,
                    'id_user' => $k,
                    'track_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    $data = [];
                }
            }
            Yii::$app->db->createCommand("UPDATE " . Post::tableName() . " SET subscribers_count = {$subscribersCount} WHERE id = {$i}")->execute();
        }
        if (!empty($data)) {
            yield $data;
        }
    }


    private function showInsertedInfo($tableName, $amount)
    {
        echo "       " . number_format($amount) . " row" . ($amount > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
    }


    /**
     * Truncate a table and show the appropriate message
     * 
     * @param string $tableName
     * @param bool $disableForeignKeyChecks 
     */
    private function truncateTable(string $tableName, bool $disableForeignKeyChecks = false)
    {
        DbHelper::truncateTable($tableName, false);
        echo "The {$tableName} table was truncated" . PHP_EOL;
    }




    /**
     * This command seeds the Posts Visitors table.
     * 
     * @return int Exit code
     */
    /*  public function _actionPostsVisitors()
    {
        $tableName = PostVisitor::tableName();

        $this->truncateTable($tableName);

        $time = time();
        PostVisitor::dropIndexesAndForeignKeys();
        echo "Seeding the {$tableName} table ... ";
        Yii::$app->db->createCommand("CALL seedPostsVisitors();")->execute();
        echo PHP_EOL;

        PostVisitor::addIndexesAndForeignKeys();
        echo 'The process took ' . date('H:i:s', (time() - $time)) . PHP_EOL;

        $this->showInsertedInfo(
            $tableName,
            Yii::$app->db->createCommand("SELECT COUNT(*) FROM posts_visitors;")->queryScalar()
        );
    } */

    /**
     * This command seeds the Posts Track table.
     * 
     * @return int Exit code
     */
    /* public function _actionPostsTrack()
    {
        $tableName = PostTrack::tableName();

        $this->truncateTable($tableName);

        $time = time();
        echo "Seeding the {$tableName} table ... ";
        Yii::$app->db->createCommand("CALL seedPostsTrack();")->execute();
        echo PHP_EOL;
        echo 'The process took ' . date('H:i:s', (time() - $time)) . PHP_EOL;

        $this->showInsertedInfo(
            $tableName,
            Yii::$app->db->createCommand("SELECT COUNT(*) FROM posts_track;")->queryScalar()
        );
    } */
}
