<?php

namespace app\commands;

use app\components\EmptyLogger;
use app\components\faker_data_generators\Post as PostGenerator;
use app\components\faker_data_generators\PostVisitor as PostVisitorGenerator;
use app\components\faker_data_generators\User as UserGenerator;
use app\components\faker_data_generators\PostTrack as PostTrackGenerator;
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
        ini_set('memory_limit', '512M');
        Yii::setLogger(new EmptyLogger());
    }

    /**
     * This command seeds all database tables.
     * 
     * @return int Exit code
     */
    public function actionIndex()
    {
        $this->actionUsers(100000);

        $this->actionPosts(100000);
        // $this->actionPosts(1000000);

        $this->actionPostsVisitors();

        $this->actionPostsTrack();

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

        $this->truncateTable(Profile::tableName(), true);
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
        $dataGenerator = new PostVisitorGenerator;

        $this->truncateTable($tableName);

        $inserted = 0;
        echo "Seeding the {$tableName} table ";

        foreach (
            $dataGenerator($amountPerBatch = 1000, [
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
                }
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        $this->showInsertedInfo($tableName, $inserted);

        $this->updatePostsVisitorsCount();

        return ExitCode::OK;
    }

    /**
     * This command seeds the Posts Track table.
     * 
     * @return int Exit code
     */
    public function actionPostsTrack()
    {
        $tableName = PostTrack::tableName();
        $dataGenerator = new PostTrackGenerator();

        $this->truncateTable($tableName);

        $inserted = 0;
        echo "Seeding the {$tableName} table ";

        foreach (
            $dataGenerator(
                $amountPerBatch = 1000,
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

        $this->updatePostsSubscribersCount();

        return ExitCode::OK;
    }


    /**
     * Truncate a table and show the appropriate message
     * 
     * @param string $tableName
     * @param bool $disableForeignKeyChecks 
     */
    private function truncateTable(string $tableName, bool $disableForeignKeyChecks = false)
    {
        if ($disableForeignKeyChecks) {
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        }

        Yii::$app->db->createCommand()->truncateTable($tableName)->execute();
        echo "The {$tableName} table was truncated" . PHP_EOL;

        if ($disableForeignKeyChecks) {
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        }
    }


    private function updatePostsVisitorsCount()
    {
        $postIdFrom = Post::find()->min('id');
        $postIdTo = Post::find()->max('id');

        $i = 0;
        echo 'Calculating posts.visitors_count column values ';

        for ($id = $postIdFrom; $id <= $postIdTo; $id++) {
            Yii::$app->db->createCommand("CALL updatePostVisitorsCount({$id})")->execute();
            $i++;
            if ($i % 10000 === 0) {
                echo '.';
            }
        }

        echo PHP_EOL;
    }


    private function updatePostsSubscribersCount()
    {
        $postIdFrom = Post::find()->min('id');
        $postIdTo = Post::find()->max('id');

        $i = 0;
        echo 'Calculating posts.subscribers_count column values ';

        for ($id = $postIdFrom; $id <= $postIdTo; $id++) {
            Yii::$app->db->createCommand("CALL updatePostSubscribersCount({$id})")->execute();
            $i++;
            if ($i % 10000 === 0) {
                echo '.';
            }
        }

        echo PHP_EOL;
    }

    private function showInsertedInfo($tableName, $amount)
    {
        echo "       " . number_format($amount) . " row" . ($amount > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
    }
}
