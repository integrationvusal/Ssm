<?php

class RBACController extends Controller{

    public static function getUser($requiredPermission = false){
        $user = AuthController::getCurrentUser();
        if(!$requiredPermission) {
            return $user;
        }

        $permissions = PermissionController::getPermissions(['user_id' => $user['id'], 'operator_id' => $user['operator']['id']]);
        if($requiredPermission && array_key_exists($requiredPermission, $permissions) && $permissions[$requiredPermission]) return $user;

        ApplicationController::forbidden();
    }



}