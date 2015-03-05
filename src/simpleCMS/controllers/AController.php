<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 23.02.15
 */

namespace simpleCMS\controllers;

use simpleCMS\core\ApplicationHelper;

/**
 * Базовый контроллер
 * @package simpleCMS\controllers
 */
abstract class AController
{
    /**
     * @var ApplicationHelper
     */
    protected $appHelper;

    /**
     * @var array массив переменных шаблона
     */
    protected $variables = [];

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->appHelper = ApplicationHelper::getInstance();
        $this->setVariable('appHelper', $this->appHelper);
        $this->testAuth();
    }

    /**
     * Редирект
     * @param string $redirectUrl
     * @return null
     */
    public function redirect($redirectUrl)
    {
        header('Location: ' . $redirectUrl);
        return null;
    }

    /**
     * @param string $variable
     * @param mixed $value
     * @return $this
     */
    protected function setVariable($variable, $value)
    {
        $this->variables[$variable] = $value;
        return $this;
    }

    /**
     * Рендеринг шаблона
     * @param string $template название шаблона
     * @param bool $wrapInContainer флаг оборачивания шаблона в контейнер 
     * @param string $containerName название шаблона контейнера
     * @return string
     */
    protected function render($template, $wrapInContainer = true, $containerName = 'container.twig')
    {
        return $this->appHelper->render($template, $this->variables, $wrapInContainer, $containerName);
    }

    /**
     * Проверка авторизации пользователя
     * Для контроллер проверка по-умолчанию отсутствует
     * @return bool
     */
    protected function testAuth()
    {
        return true;
    }

    
    /**
     * Выполнение контроллера
     * @return string
     * @throws \Exception в случае ошибки
     */
    abstract public function execute();
}
