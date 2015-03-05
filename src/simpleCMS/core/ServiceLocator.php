<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 28.02.15
 */

namespace simpleCMS\core;


/**
 * Service Locator приложения
 * Синглтон
 * @package simpleCMS\core
 */
class ServiceLocator
{
    
    /**
     * @var array массив компонентов
     */
    private $components = [];
    
    /**
     * Устанавливает компонент
     * @param string $componentId
     * @param mixed $component
     * @return $this
     */
    public function set($componentId, $component)
    {
        $this->components[$componentId] = $component;
        return $this;
    }

    /**
     * Возвращает компонент по его id
     * @param string $componentId
     * @return mixed
     * @throws \Exception если компонент не найден
     */
    public function get($componentId)
    {
        if (isset($this->components[$componentId])) {
            return $this->components[$componentId];
        }

        throw new \Exception('Component ' . $componentId . ' don\'t exist');
    }

    

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        return $this->get($name);
    }

}
