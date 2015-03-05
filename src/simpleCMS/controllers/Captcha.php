<?php
/**
 * @package NCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 05.03.15
 */

namespace simpleCMS\controllers;

/**
 * Генерация капчи
 * @package simpleCMS\controllers
 */
class Captcha extends AController
{

    /**
     * Выполнение контроллера
     * @return string
     * @throws \Exception в случае ошибки
     */
    public function execute()
    {
//        Старый неоптимизированный класс
        $captcha = new \simpleCMS\core\Captcha($_GET['captcha']);
        $captcha->initialize();
        
        $initParams = ['width', 'height', 'color', 'bg_color', 'noise'];
        foreach ($initParams as $param) {
            if (isset($_GET[$param])) {
                $method = 'set_' . $param;
                $captcha->$method($_GET[$param]);
            }
        }
        
        $captcha->render();
        die();
    }
}
