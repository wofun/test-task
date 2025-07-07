<?php

namespace app\commands;

use app\components\EmptyLogger;
use app\models\Post;
use app\models\PostTrack;
use app\models\PostVisitor;
use app\models\User;
use Exception;
use Faker\Factory;
use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use yii\helpers\StringHelper;

class SeederController extends Controller
{
    private $faker;

    public function init()
    {
        Yii::setLogger(new EmptyLogger());
        ini_set('memory_limit', '512M');
        $this->faker = Factory::create(str_replace('-', '_', Yii::$app->language));
    }

    /**
     * This command seeds all database tables.
     * 
     * @return int Exit code
     */
    public function actionIndex()
    {
        $this->actionUsers(100000);
        $this->actionPosts(1000000);
        $this->actionPostsVisitors();
        $this->actionPostsTrack();

        ExitCode::OK;
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
        $this->truncateTable($tableName, true);

        echo "Seeding the {$tableName} table ";
        $inserted = 0;

        foreach ($this->getUserBatch($amount, $amountPerBatch) as $batch) {
            try {
                Yii::$app->db->createCommand()->batchInsert(User::tableName(), $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                echo '.';
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        echo "      $inserted row" . ($inserted > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
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
        // $this->truncateTable($tableName, true);

        $idFrom = User::find()->min('id');
        $idTo = User::find()->max('id');

        echo "Seeding the {$tableName} table ";
        $inserted = 0;

        foreach ($this->getPostBatch($amount, $amountPerBatch, $idFrom, $idTo) as $batch) {
            try {
                Yii::$app->db->createCommand()->batchInsert(Post::tableName(), $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                echo '.';
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        echo "      $inserted row" . ($inserted > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
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

        $userIdFrom = User::find()->min('id');
        $userIdTo = User::find()->max('id');
        $postIdFrom = Post::find()->min('id');
        $postIdTo = Post::find()->max('id');

        echo "Seeding the {$tableName} table ";
        $inserted = 0;

        foreach ($this->getPostVisitorBatch($postIdFrom, $postIdTo, $userIdFrom, $userIdTo) as $batch) {
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

        echo "      {$inserted} row" . ($inserted > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
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

        $userIdFrom = User::find()->min('id');
        $userIdTo = User::find()->max('id');
        $postIdFrom = Post::find()->min('id');
        $postIdTo = Post::find()->max('id');

        echo "Seeding the {$tableName} table ";
        $inserted = 0;

        foreach ($this->getPostTrackBatch($postIdFrom, $postIdTo, $userIdFrom, $userIdTo) as $batch) {
            if (empty($batch)) {
                continue;
            }
            try {
                Yii::$app->db->createCommand()->batchInsert($tableName, $columns = array_keys($batch[0]), $batch)->execute();
                $inserted += count($batch);
                if ($inserted % 100000 === 0) { // 100 000
                    echo '.';
                }
            } catch (Exception $e) {
                echo 'Batch insert error: ' . StringHelper::truncate($e->getMessage(), 110) . PHP_EOL;
            }
        }
        echo PHP_EOL;

        echo "      $inserted row" . ($inserted > 1 ? 's' : null) . " inserted in the {$tableName} table \n";
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
        echo "The {$tableName} table was truncated." . PHP_EOL;

        if ($disableForeignKeyChecks) {
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        }
    }

    /**
     * The generator of the User seeder 
     */
    private function getUserBatch($totalAmount, $amountPerBatch = 1000)
    {
        $data = [];
        $passwordHash = Yii::$app->getSecurity()->generatePasswordHash('password');

        for ($i = 0; $i < $totalAmount; $i++) {
            if (count($data) === $amountPerBatch) {
                yield $data;
                $data = [];
            }
            $data[] = [
                'username' => $this->faker->firstName . ' ' . $this->faker->lastName  . ' ' . $i,
                'email' => $i . $this->faker->email,
                'password_hash' =>  $passwordHash,
                'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
                'confirmed_at' => time(),
                'created_at' => time(),
                'updated_at' => time(),
                'flags' => 0,
            ];
        }
        yield $data;
    }

    /**
     * The generator of the Posts table
     */
    private function getPostBatch($totalAmount, $amountPerBatch = 1000, $userIdFrom, $userIdTo)
    {
        $data = [];
        for ($i = 0; $i < $totalAmount; $i++) {
            if (count($data) === $amountPerBatch) {
                yield $data;
                $data = [];
            }
            $data[] = [
                'name' => $this->faker->name,
                'text' => $this->faker->text,
                'created_by' => rand($userIdFrom, $userIdTo),
                'created_at' => date('Y-m-d H:i:s', time()),
                // 'created_at' => $this->faker->dateTimeBetween('-3 year'),
            ];
        }
        yield $data;
    }

    /**
     * The generator of the PostVisitor seeder
     */
    private function getPostVisitorBatch($postIdFrom, $postIdTo, $userIdFrom, $userIdTo, $amountPerBatch = 100000)
    {
        $data = [];
        for ($i = $postIdFrom; $i <= $postIdTo; $i++) {
            $userId = rand($userIdFrom, $userIdTo - 100);
            for ($k = $userId; $k <= $userId + 100; $k++) {
                $data[] = [
                    'id_post' => $i,
                    'id_visitor' => $k,
                    'view_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    unset($data);
                    break;
                }
            }
        }
        yield $data;
    }

    /**
     * The generator of the PostTrack seeder
     */
    private function getPostTrackBatch($postIdFrom, $postIdTo, $userIdFrom, $userIdTo, $amountPerBatch = 100000)
    {
        $data = [];
        for ($i = $postIdFrom; $i <= $postIdTo; $i++) {
            $userId = rand($userIdFrom, $userIdTo - 20);
            for ($k = $userId; $k <= $userId + rand(10, 20); $k++) {
                $data[] = [
                    'id_post' => $i,
                    'id_user' => $k,
                    'track_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    unset($data);
                    break;
                }
            }
        }
        yield $data;
    }
}
