<?php

namespace app\helpers;

use Yii;

class DbHelper
{
    static public function truncateTable(string $name, bool $disableForeignKeyChecks = false)
    {
        if ($disableForeignKeyChecks) {
            self::disableForeignKeyChecks();
        }

        Yii::$app->db->createCommand()->truncateTable($name)->execute();

        if ($disableForeignKeyChecks) {
            self::enableForeignKeyChecks();
        }
    }

    static public function disableForeignKeyChecks()
    {
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
    }

    static public function enableForeignKeyChecks()
    {
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
    }
}
