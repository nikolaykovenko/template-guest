<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 28.02.15
 */

namespace simpleCMS\controllers;

/**
 * Контроллер стартовой страницы
 * @package simpleCMS\controllers
 */
class Start extends AController
{
    
    /**
     * Выполнение контроллера
     * @return string
     * @throws \Exception в случае ошибки
     */
    public function execute()
    {
        $this->setVariable('pageCaption', $this->appHelper->getConfigParam('siteTitle'));
        $this->setVariable('text', '<p>Добро пожаловать! Текстовое описание проекта.</p>');    
        
        return $this->render('text.twig');
    }
}
