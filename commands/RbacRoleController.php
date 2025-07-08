<?php

namespace app\commands;

use app\models\User;
use Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

class RbacRoleController extends Controller
{
    /**
     * This command assigns a role to a user.
     * 
     * @param string $email user email.
     * @param string $role name of a role to assign.
     * @return int Exit code
     */
    public function actionAssign(string $email, string $role)
    {
        $user = User::findByEmail($email);
        if (!$user) {
            echo "The user not found" . PHP_EOL;
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $item = $auth->getRole($role);
        if (!$item) {
            echo "The {$role} role not found" . PHP_EOL;
            return ExitCode::DATAERR;
        }

        try {
            $auth->assign($item, $user->id);
            echo 'The role was assigned' . PHP_EOL;
        } catch (Exception $e) {
            echo "Assignment error "  . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    /**
     * This command revokes a role from a user.
     * 
     * @param string $email user email.
     * @param string $role name of a role to revoke.
     * @return int Exit code
     */
    public function actionRevoke(string $email, string $role)
    {
        $user = User::findByEmail($email);
        if (!$user) {
            echo "The user not found" . PHP_EOL;
            return ExitCode::DATAERR;
        }
        $auth = Yii::$app->authManager;
        $item = $auth->getRole($role);
        if (!$item) {
            echo "The {$role} role not found" . PHP_EOL;
            return ExitCode::DATAERR;
        }

        if ($auth->revoke($item, $user->id)) {
            echo "The role is revoked" . PHP_EOL;
        } else {
            echo "The role was not assigned" . PHP_EOL;
        }

        return ExitCode::OK;
    }


    /**
     * This command revokes all roles from a user.
     * 
     * @param string $email
     * @return int Exit code
     */
    public function actionRevokeAll(string $email)
    {
        $user = User::findByEmail($email);
        if (!$user) {
            echo "The user not found" . PHP_EOL;
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $auth->revokeAll($user->id);
        echo "Done" . PHP_EOL;

        return ExitCode::OK;
    }
}
