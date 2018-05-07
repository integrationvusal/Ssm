<?php

class ContactController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('contact');
        self::renderTemplate("contact" . ds . "contact.tpl", [
            'flash' => SessionStorage::getFlash('contact'),
            'current_menu' => 'contact'
        ]);

    }

    public static function process($request, $vars){

        if($request->isPost()){

            $user = (new UserModel)->getOneById($_POST['user_id']);
            $mails = ['info@ssm.az'];
            $subject = 'SSM.AZ saytından əks əlaqə məktubu';
            $date = date("Y-m-d H:i:s");
            $body = "Bu məktub " . $user["name"] . " adlı istifadəçinin profilindən " . $date . " tarixində göndərilib\n";
            $body .= "Əlaqə üçün məlumat:\n";
            $body .= "Ad: " . stripslashes($_POST["name"]) . "\n";
            $body .= "Email: " . stripslashes($_POST["email"]) . "\n";
            $body .= "Məzmun: " . $_POST["message"];

            $from = 'info@ssm.az';
            $fromName = 'SSM.AZ';

            $context['type'] = 0;
            $context['status'] = Utils::sendMail($mails, $subject, $body, $from, $fromName);
            $context['title'] = 'Əlaqə';
            if($context['status']){
                $context['message'] = 'Müraciətiniz qəbul olundu';
            } else {
                $context['message'] = 'Əməliyyat zamanı xəta';
            }
            SessionStorage::addFlash('contact', $context);
            header("Location: " . Application::$settings['url'] . "/contact");

        } else {

            ApplicationController::pageNotFound();

        }

    }

}