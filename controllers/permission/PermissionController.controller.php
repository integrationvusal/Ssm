<?php

class PermissionController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('operator_grant');
        if(isset($vars['operator_id'])){

            $operator_permissions = (new PermissionModel())->getAll(['user_id' => $user['id'], 'operator_id' => $vars['operator_id']]);

            $operator = (new OperatorModel())->getOne(['user_id' => $user['id'], 'operator_id' => $vars['operator_id']]);
            if(!$operator) ApplicationController::pageNotFound();
            $subjects = (new SubjectModel())->getAll(['user_id' => $user['id']]);

            $flash = SessionStorage::getFlash("permission");

            $permissionsSet = Application::$settings['permissions_set'];
            $permissions = (new UserPermissionModel())->getAll(['user_id' => $user['id']]);
            foreach ($permissionsSet as $k => $ps){
                if(array_key_exists($k, $permissions)){
                    if(!$permissions[$k]) unset($permissionsSet[$k]);
                }
            }
            unset($permissionsSet['subject']);

            self::renderTemplate("permission" . ds . "permission.tpl", [
                'operator_permissions' => $operator_permissions,
                'permissions_set' => $permissionsSet,
                'subjects' => $subjects,
                'user' => $user,
                'operator' => $operator,
                'flash' => $flash,
                'current_menu' => 'operator'
            ]);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function getPermissions($array = false){

        if($array){
            $permissions = (new PermissionModel())->getAll($array);
            SessionStorage::edit("permissions", $permissions);
            return $permissions;
        } else {
            return SessionStorage::get('permissions');
        }

    }

    public static function create($request, $vars){

        $user = RBACController::getUser('operator_grant');
        if($request->isPost()){

            $permissionModel = new PermissionModel();
            $context['type'] = 0;
            $context['status'] = $permissionModel->createPermission($_POST);
            $context['title'] = "İstifadəçi modulları";
            if($context['status']){
                $context['message'] = "İstifadəçi modulları əlavə olundu";
            } else {
                $context['message'] = "İstifadəçi modullarını əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("permission", $context);
            header("Location: " . Application::$settings['url'] . "/permission/" . $_POST['operator_id']);

        } else {

            ApplicationController::pageNotFound();

        }

    }

}