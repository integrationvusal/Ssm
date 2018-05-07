<?php

class SubjectController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('change_subject');
        $subjects = (new SubjectModel())->getAll(['user_id' => $user['id']]);
        if($user['type'] == 1){
            $tmp = array();
            $permissions = PermissionController::getPermissions();
            foreach($subjects as $key => $subject){
                if(in_array($subject['id'], $permissions['subject'])) $tmp[] = $subject;
            }
            $subjects = $tmp;
        }

        $notices = (new NoticeModel())->getNoticesForUser($user);

        self::renderTemplate('subject' . ds . 'subject_choose.tpl', Array(
            'subjects' => $subjects,
			'current_subject'=>$_SESSION['subject']['id'],
            'current_menu' => 'subject',
            'notices' => $notices
        ));

    }

    public static function getCurrentSubject(){

        $subject = SessionStorage::get('subject');
        if($subject && isset($subject['id']) && $subject['id'] > 0){
            return $subject;
        }
        header("Location: " . Application::$settings['url'] . "/subject");
        exit;

    }

    public static function setNull(){
        SessionStorage::edit('subject', (new SubjectModel())->getEmptySubject());
        return SessionStorage::remove('subject');
    }

    public static function change($request, $vars){

        $user = RBACController::getUser('change_subject');
        if($request->isPost()){
            $subject = new SubjectModel();
            $subject = $subject->getOne($_POST);
            if($subject){
                (new ContragentModel())->check($user['id'], $subject['id']);
                SessionStorage::edit('subject', $subject);
                header("Location: " . Application::$settings['url']);
            }
        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function search($request, $vars){

        $user = RBACController::getUser();
        if($request->isPost() && $request->isAjax()){

            $response['status'] = 0;
            $data = (new SubjectModel())->getSearchAll($_POST);
            if($data){
                $response['status'] = 1;
                $response['data'] = $data;
            }
            echo json_encode($response);
        } else {

            ApplicationController::pageNotFound();

        }

    }

}