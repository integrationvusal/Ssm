<?php

class CategoryController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('category_read');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('category');

        $category = new CategoryModel();

        $model = Application::$settings['goods_types'][$subject['goods_type']];

        $categories = $category->getAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']]);

        self::renderTemplate('category' . ds . 'category.tpl', [
            'user' => $user,
            'subject' => $subject,
            'model' => $model,
            'categories' => $categories,
            'flash' => $flash,
            'goods_type' => $subject['goods_type'],
            'current_menu' => 'category'
        ]);

    }

    public static function create($request, $vars){

        if($request->isPost()){

            $category = new CategoryModel('category_create');
            $context['type'] = 0;
            $context['status'] = $category->createCategory($_POST);
            $context['title'] = "Yeni kategoriya";
            if($context['status']){
                $context['message'] = "Yeni kategoriya əlavə olundu";
            } else {
                $context['message'] = "Yeni kategoriyanı əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("category", $context);
            header("Location: " . Application::$settings['url'] . "/category");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function update($request, $vars){

        $user = RBACController::getUser('category_update');
        $subject = SubjectController::getCurrentSubject();
        $category = new CategoryModel();

        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $category->updateCategory($_POST);
            $context['title'] = "Redaktə et";
            if($context['status']){
                $context['message'] = "Kategoriya yeniləndi";
            } else {
                $context['message'] = "Kategoriyanı yeniləmək mümkün olmadı";
            }
            SessionStorage::addFlash("category", $context);
            header("Location: " . Application::$settings['url'] . "/category");

        } else {

            $category_id = isset($vars['category_id']) ? $vars['category_id']: 0;
            $categories = $category->getAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']]);

            if($category_id){

                $category = $category->getOne([
                    'id' => $category_id,
                    'user_id' => $user['id'],
                    'goods_type' => $subject['goods_type']
                ]);

                if(!$category){

                    ApplicationController::pageNotFound();

                }

                $model = Application::$settings['goods_types'][$subject['goods_type']];

                self::renderTemplate('category' . ds . 'category.tpl', [
                    'user' => $user,
                    'subject' => $subject,
                    'model' => $model,
                    'categories' => $categories,
                    'category' => $category
                ]);

            } else {

                ApplicationController::pageNotFound();

            }

        }

    }

    public static function delete($request, $vars){

        $user = RBACController::getUser('category_delete');
        if($request->isPost()){

            $category = new CategoryModel();
            $context['type'] = 0;
            $context['status'] = $category->deleteCategory($_POST);
            $context['title'] = "Kategoriyanı sil";
            if($context['status'] == 1){
                $context['message'] = "Obyekt silindi";
            } elseif($context['status'] == 2) {
                $context['message'] = "Bu kategoriyanı istifadə edən <br> mallar olduguna görə,<br> obyekti silmək mümkün olmadı";
            } else {
                $context['message'] = "Obyekti silmək mümkün olmadı";
            }
            SessionStorage::addFlash("category", $context);
            header("Location: " . Application::$settings['url'] . "/category");
        } else {
            ApplicationController::pageNotFound();
        }

    }

}