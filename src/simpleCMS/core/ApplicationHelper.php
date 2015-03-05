<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 23.02.15
 */

namespace simpleCMS\core;

/**
 * Класс для быстрого доступа к основным элементам ЦМС
 * Синглтон
 */
class ApplicationHelper
{

    /**
     * @var static|null
     */
    private static $instance;

    /**
     * @var array|null
     */
    private $config = null;

    /**
     * @var \PDO|null экземпляр подключения к БД
     */
    private $dbh;

    /**
     * @var \Twig_Environment
     */
    private $renderer;

    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * Возвращает экземпляр объекта
     * @return ApplicationHelper
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Возвращает экземпляр подключения к БД
     * @return \PDO
     * @throws \Exception в случае ошибки
     */
    public function getDbh()
    {
        if (is_null($this->dbh)) {
            $config = $this->getConfigParam('db');
            if (empty($config)) {
                throw new \Exception('DB config is not set');
            }
            
            $this->dbh = new \PDO(
                $config['dsn'],
                $config['user'],
                $config['pass'],
                [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}"]
            );
        }
        
        return $this->dbh;
    }

    /**
     * Рендерит переданный шаблон
     * @param string $template
     * @param array $variables
     * @param bool $wrapInContainer флаг оборачивания шаблона в контейнер
     * @param string $containerName название шаблона контейнера
     * @return string
     */
    public function render($template, array $variables = [], $wrapInContainer = true, $containerName = 'container.twig')
    {
        if (empty($this->renderer)) {
            $loader = new \Twig_Loader_Filesystem($this->getConfigParam('templatesPath'));
            $this->renderer = new \Twig_Environment($loader);
            
            $telNumberFilter = new \Twig_SimpleFilter('telNumber', ['simpleCMS\model\TelNumbers', 'telFormat']);
            $this->renderer->addFilter('telNumber', $telNumberFilter);
        }
        
        if ($wrapInContainer) {
            $variables['_templateName'] = $template;
            $template = $containerName;
        }
        
        return $this->renderer->render($template, $variables);
    }

    /**
     * Устанавливает параметры конфигурации
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Возвращает значение параметра конфигурации
     * @param string $param
     * @return mixed
     */
    public function getConfigParam($param)
    {
        if (isset($this->config[$param])) {
            return $this->config[$param];
        }
        
        return null;
    }

    /**
     * Устанавливает компонент приложения
     * @param string $componentId
     * @param mixed $component
     * @return $this
     */
    public function setComponent($componentId, $component)
    {
        $this->serviceLocator->set($componentId, $component);
        return $this;
    }

    /**
     * Возвращает компонент приложения по его id
     * @param string $componentId
     * @return mixed
     * @throws \Exception если компонент не найден
     */
    public function getComponent($componentId)
    {
        return $this->serviceLocator->get($componentId);
    }



    /**
     * Конструктор
     */
    protected function __construct()
    {
        $this->serviceLocator = new ServiceLocator();
    }
}
