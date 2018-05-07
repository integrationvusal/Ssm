<?php

class ManagerController extends Controller{

    public static function main($request, $vars, $params = array()){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }

        $limit = 20;
        $page = 1;
        if(isset($vars['page'])) $page = (int)$vars['page'];
        $offset = ($page - 1) * $limit;

        $managerModel = new ManagerModel();
        $siteUsers = $managerModel->getAll($limit, $offset);
        $siteUsersCount = $managerModel->getCountAll();

        $paginator = Utils::generatePaginator($siteUsersCount, $limit, $page);

        self::renderTemplate("manager" . ds . "manager.tpl",array_merge($params, [
            'siteUsers' => $siteUsers,
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit,
            'flash' => SessionStorage::getFlash('manager'),
            'current_menu' => 'admin_users'
        ]));

    }

    public static function update($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $managerModel = new ManagerModel();
        if($request->isPost()){

            $res = $managerModel->updateManager($_POST);
            $context['type'] = 0;
            $context['status'] = $res['status'];
            $context['title'] = "Redaktə et";
            $context['message'] = $res['message'];;
            SessionStorage::addFlash("manager", $context);
            header("Location: " . Application::$settings['url'] . "/manager");

        } else {

            $manager_id = 0;
            if(isset($vars['manager_id'])) $manager_id = (int)$vars['manager_id'];
            $manager = $managerModel->getOne(['manager_id' => $manager_id]);
            self::main($request, ['page' => 1], ['model' => $manager]);

        }

    }

    public static function create($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $managerModel = new ManagerModel();
        if($request->isPost()){

            $res = $managerModel->createManager($_POST);
            $context['type'] = 0;
            $context['status'] = $res['status'];
            $context['title'] = "Yeni istifadəçi";
            $context['message'] = $res['message'];;
            SessionStorage::addFlash("manager", $context);
            header("Location: " . Application::$settings['url'] . "/manager");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function delete($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $managerModel = new ManagerModel();
        if($request->isPost()){

            $res = $managerModel->deleteManager($_POST);
            $context['type'] = 0;
            $context['status'] = $res['status'];
            $context['title'] = "İstifadəçi silinməsi";
            $context['message'] = $res['message'];;
            SessionStorage::addFlash("manager", $context);
            header("Location: " . Application::$settings['url'] . "/manager");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function subject($request, $vars, $params = array()){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }

        $manager_id = 0;
        if(isset($vars['manager_id'])) $manager_id = (int)$vars['manager_id'];
        $manager = (new ManagerModel())->getOne(['manager_id' => $manager_id]);
        if(!$manager) ApplicationController::pageNotFound();

        $subjectModel = new SubjectModel();
        $subjects = $subjectModel->getAll(['user_id' => $manager['id']]);

        self::renderTemplate("subject" . ds . "manager_subject.tpl",array_merge($params, [
            'manager' => $manager,
            'subjects' => $subjects,
            'goodsTypes' => Utils::remap('id', 'title', Application::$settings['goods_types']),
            'subjectTypes' => Application::$settings['subject_types'],
            'flash' => SessionStorage::getFlash('subject'),
            'current_menu' => 'admin_users'
        ]));

    }

    public static function updateSubject($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $subjectModel = new SubjectModel();
        if($request->isPost()){

            $res = $subjectModel->updateSubject($_POST);
            $context['type'] = 0;
            $context['status'] = $res['status'];
            $context['title'] = "Redaktə et";
            $context['message'] = $res['message'];
            SessionStorage::addFlash("subject", $context);
            header("Location: " . Application::$settings['url'] . "/manager/subject/" . $_POST['manager_id']);

        } else {

            $manager_id = 0;
            if(isset($vars['manager_id'])) $manager_id = (int)$vars['manager_id'];
            $subject_id = 0;
            if(isset($vars['subject_id'])) $subject_id = (int)$vars['subject_id'];
            $manager = (new ManagerModel())->getOne(['manager_id' => $manager_id]);
            $subject = $subjectModel->getOne(['user_id' => $manager_id, 'id' => $subject_id]);
            if(!$manager || !$subject) ApplicationController::pageNotFound();
            self::subject($request, ['manager_id' => $manager['id']], ['model' => $subject]);

        }

    }

    public static function createSubject($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $subjectModel = new SubjectModel();
        if($request->isPost()){

            $res = $subjectModel->createSubject($_POST);
            $context['type'] = 0;
            $context['status'] = $res['status'];
            $context['title'] = "Yeni obyekt";
            $context['message'] = $res['message'];;
            SessionStorage::addFlash("subject", $context);
            header("Location: " . Application::$settings['url'] . "/manager/subject/" . $_POST['manager_id']);

        } else {

            ApplicationController::pageNotFound();

        }

    }


    /**
     * CURRENCY SECTION
     */

    public static function currency($request, $vars, $params = array()){

        $user = RBACController::getUser();

        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }

        $limit = 20;
        $page = 1;
        if(isset($vars['page'])) $page = (int)$vars['page'];
        $offset = ($page - 1) * $limit;

        $model = new CurrencyModel();
        $datas = $model->getAll($limit, $offset);
        $count_data = $model->getAllCount();

        $paginator = Utils::generatePaginator($count_data, $limit, $page);

        self::renderTemplate("manager" . ds . "currency.tpl",array_merge($params, [
            'datas' => $datas,
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit,
            'currentDate' => date("Y-m-d"),
            'flash' => SessionStorage::getFlash('notice'),
            'current_menu' => 'admin_currency'
        ]));
    }

    public static function updateCurrency($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $model = new CurrencyModel();
        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $model->update($_POST);
            $context['title'] = "Valyuta redaktə et";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("currency", $context);
            header("Location: " . Application::$settings['url'] . "/manager/currency");

        } else {

            $currency_id = 0;
            if(isset($vars['currency_id'])) $currency_id = (int)$vars['currency_id'];
            $model = $model->getOne(['currency_id' => $currency_id]);
            $model['currency'] = Utils::getCurrency($model['name']);
            self::currency($request, ['page' => 1], ['model' => $model]);

        }

    }


    public static function createCurrency($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $model = new CurrencyModel();
        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $model->create($_POST);
            $context['title'] = "Yeni valyuta";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("currency", $context);
            header("Location: " . Application::$settings['url'] . "/manager/currency");

        } else {

            ApplicationController::pageNotFound();

        }

    }

     public static function deleteCurrency($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $model = new CurrencyModel();
        if($request->isPost()){


            $context['type'] = 0;
            $context['status'] = $model->delete($_POST);
            $context['title'] = "Valyutanı sil";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("currency", $context);
            header("Location: " . Application::$settings['url'] . "/manager/currency");

        } else {

            ApplicationController::pageNotFound();

        }

    }


    /**
     * NOTICES SECTION
     */

    public static function notice($request, $vars, $params = array()){

        $user = RBACController::getUser();

        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }

        $limit = 20;
        $page = 1;
        if(isset($vars['page'])) $page = (int)$vars['page'];
        $offset = ($page - 1) * $limit;

        $noticeModel = new NoticeModel();
        $notices = $noticeModel->getAll($limit, $offset);
        $noticesCount = $noticeModel->getAllCount();

        $paginator = Utils::generatePaginator($noticesCount, $limit, $page);

        self::renderTemplate("manager" . ds . "notice.tpl",array_merge($params, [
            'notices' => $notices,
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit,
            'currentDate' => date("Y-m-d"),
            'flash' => SessionStorage::getFlash('notice'),
            'current_menu' => 'admin_notice'
        ]));

    }

    public static function createNotice($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $noticeModel = new NoticeModel();
        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $noticeModel->createNotice($_POST);
            $context['title'] = "Yeni elan";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("notice", $context);
            header("Location: " . Application::$settings['url'] . "/manager/notice");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function updateNotice($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $noticeModel = new NoticeModel();
        if($request->isPost()){


            $context['type'] = 0;
            $context['status'] = $noticeModel->updateNotice($_POST);
            $context['title'] = "Elan redaktə et";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("notice", $context);
            header("Location: " . Application::$settings['url'] . "/manager/notice");

        } else {

            $notice_id = 0;
            if(isset($vars['notice_id'])) $notice_id = (int)$vars['notice_id'];
            $notice = $noticeModel->getOne(['notice_id' => $notice_id]);
            self::notice($request, ['page' => 1], ['model' => $notice]);

        }

    }

    public static function deleteNotice($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $noticeModel = new NoticeModel();
        if($request->isPost()){


            $context['type'] = 0;
            $context['status'] = $noticeModel->deleteNotice($_POST);
            $context['title'] = "Elanı sil";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("notice", $context);
            header("Location: " . Application::$settings['url'] . "/manager/notice");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function expireNotice($request, $vars){

        $user = RBACController::getUser();
        $noticeModel = new NoticeModel();
        if($request->isAjax() && $request->isPost()){

            $context['status'] = $noticeModel->expireForUser(array_merge($_POST, ['user' => $user]));
            echo json_encode($context);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function getWhoViewed($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        $noticeModel = new NoticeModel();
        if($request->isAjax() && $request->isPost()){

            $context['data'] = $noticeModel->getAllWhoViewed();
            echo json_encode($context);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    // NOTICES SECTION

    public static function permission($request, $vars){

        $user = RBACController::getUser();
        if($user['spc'] != 1) {
            ApplicationController::pageNotFound();
        }
        if($request->isPost()){

            $permissionModel = new UserPermissionModel();
            $context['type'] = 0;
            $context['status'] = $permissionModel->createPermission($_POST);
            $context['title'] = "İstifadəçi modulları";
            if($context['status']){
                $context['message'] = "İstifadəçi modulları əlavə olundu";
            } else {
                $context['message'] = "İstifadəçi modullarını əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("userpermission", $context);
            header("Location: " . Application::$settings['url'] . "/manager/user/permission/" . $_POST['user_id']);

        } else {
            $permissionModel = new UserPermissionModel();
            if(isset($vars['user_id'])){

                $user = (new UserModel)->getOneById($vars['user_id']);
                $permissions = $permissionModel->getAll(['user_id' => $user['id']]);
                $subjects = (new SubjectModel())->getAll(['user_id' => $user['id']]);
                $flash = SessionStorage::getFlash("userpermission");
                $permissionsSet = Application::$settings['permissions_set'];
                unset($permissionsSet['subject']);

                self::renderTemplate("userpermission" . ds . "userpermission.tpl", [
                    'user_permissions' => $permissions,
                    'permissions_set' => $permissionsSet,
                    'subjects' => $subjects,
                    'user_id' => $user['id'],
                    'flash' => $flash,
                    'current_menu' => 'admin_users'
                ]);

            } else {
                ApplicationController::pageNotFound();
            }
        }
    }
}