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
use Generator;
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
        $this->truncateTable($tableName);

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

        $this->displayResultInfo($tableName, $inserted);

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

        $this->truncateTable($tableName);

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

        $this->displayResultInfo($tableName, $inserted);

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

        $countToInsert = Post::find()->sum('visitors_count');

        $progressPercentInTens = 0;
        $inserted = 0;

        echo "Seeding the {$tableName} table ";

        foreach (
            $this->getPostVisitorBatch(
                $amountPerBatch = 25000,
                Post::find()->min('id'),
                Post::find()->max('id'),
                User::find()->min('id'),
                User::find()->max('id')
            ) as $batch
        ) {
            try {
                Yii::$app->db->createCommand()->batchInsert($tableName, $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                if ($inserted % 300000 === 0) {
                    echo '.';
                    $this->displayProgressPercentageInTens($countToInsert, $inserted, $progressPercentInTens);
                }
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        $this->displayResultInfo($tableName, $inserted);

        return ExitCode::OK;
    }



    private function getPostVisitorBatch(int $amountPerBatch = 1000, int $postIdFrom, int $postIdTo, int $userIdFrom, int $userIdTo): Generator
    {
        $data = [];
        for ($postId = $postIdFrom; $postId <= $postIdTo; $postId++) {
            $visitorsCount = Yii::$app->db->createCommand("SELECT visitors_count FROM " . Post::tableName() . " WHERE id = :id")->bindValue('id', $postId)->queryScalar();
            $userId = rand($userIdFrom, $userIdTo - $visitorsCount);
            for ($k = $userId; $k < $userId + $visitorsCount; $k++) {
                $data[] = [
                    'id_post' => $postId,
                    'id_visitor' => $k,
                    'view_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    $data = [];
                }
            }
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

        $countToInsert = Post::find()->sum('subscribers_count');

        $progressPercentInTens = 0;
        $inserted = 0;

        echo "Seeding the {$tableName} table ";

        foreach (
            $this->getPostTrackBatch(
                $amountPerBatch = 25000,
                Post::find()->min('id'),
                Post::find()->max('id'),
                User::find()->min('id'),
                User::find()->max('id')
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
                    $this->displayProgressPercentageInTens($countToInsert, $inserted, $progressPercentInTens);
                }
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        $this->displayResultInfo($tableName, $inserted);

        return ExitCode::OK;
    }


    private function getPostTrackBatch(int $amountPerBatch = 1000, int $postIdFrom, int $postIdTo, int $userIdFrom, int $userIdTo): Generator
    {
        $data = [];
        for ($postId = $postIdFrom; $postId <= $postIdTo; $postId++) {
            $subscribersCount = Yii::$app->db->createCommand("SELECT subscribers_count FROM " . Post::tableName() . " WHERE id = :id")->bindValue('id', $postId)->queryScalar();
            $userId = rand($userIdFrom, $userIdTo - $subscribersCount);
            for ($k = $userId; $k < $userId + $subscribersCount; $k++) {
                $data[] = [
                    'id_post' => $postId,
                    'id_user' => $k,
                    'track_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    $data = [];
                }
            }
        }
        if (!empty($data)) {
            yield $data;
        }
    }

    /**
     * Displays an information about inserted data to the table
     * 
     * @param string $tableName
     * @param int $count number of inserted data
     * 
     */
    private function displayResultInfo($tableName, $count)
    {
        echo "       " . number_format($count) . " row" . ($count > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
    }


    /**
     * Displays a percentage of progress
     * 
     * @param int $totalCount
     * @param int $insertedCount
     * @param int $currentPercentInTens
     */
    private function displayProgressPercentageInTens(int $totalCount, int $insertedCount, int &$currentPercentInTens = 0)
    {
        $currentPercent = floor($insertedCount  * 100 / $totalCount);
        if (floor($currentPercent / 10) > $currentPercentInTens) {
            $currentPercentInTens = floor($currentPercent / 10);
            echo "{$currentPercent}%";
        }
    }

    /**
     * Truncate a table and show the appropriate message
     * 
     * @param string $tableName
     * @param bool $disableForeignKeyChecks 
     */
    private function truncateTable(string $tableName)
    {
        DbHelper::truncateTable($tableName, false);
        echo "The {$tableName} table was truncated" . PHP_EOL;
    }
}
