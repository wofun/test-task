<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

class RbacRoleController extends Controller
{
    /**
     * This command assigns a role to a user.
     * 
     * @param int $uid user ID.
     * @param string $role name of a role to assign.
     * @return int Exit code
     */
    public function actionAssign(string $role, int $uid)
    {
        $auth = Yii::$app->authManager;
        $item = $auth->getRole($role);
        $auth->assign($item, $uid);

        return ExitCode::OK;
    }

    /**
     * This command revokes a role from a user.
     * 
     * @param int $uid user ID.
     * @param string $role name of a role to revoke.
     * @return int Exit code
     */
    public function actionRevoke(string $role, int $uid)
    {
        $auth = Yii::$app->authManager;
        $item = $auth->getRole($role);
        $auth->revoke($item, $uid);

        return ExitCode::OK;
    }


    /**
     * This command revokes all roles from a user.
     * 
     * @param int $uid user ID.
     * @return int Exit code
     */
    public function actionRevokeAll(int $uid)
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($uid);

        return ExitCode::OK;
    }
}
