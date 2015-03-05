<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 02.03.15
 */

namespace simpleCMS\controllers;

use simpleCMS\auth\TNeedAuth;
use simpleCMS\exceptions\Exception404;
use simpleCMS\model\AModel;
use simpleCMS\model\UserFiles;

/**
 * Абстрактный конттроллер для выполнения действия над файлами
 * @package simpleCMS\controllers
 */
abstract class AArticleItem extends AController
{
    
    /**
     * Получение элемента статьи
     * @param null|int $id
     * @return \stdClass
     * @throws Exception404
     */
    protected function getItem($id = null)
    {
        if (is_null($id)) {
            if (!isset($_GET['id'])) {
                throw new Exception404();
            }
            $id = $_GET['id'];
        }

        $item = $this->getModel()->findOne("`id` = :id", ['id' => $id]);
        if (empty($item)) {
            throw new Exception404();
        }
        
        return $item;
    }

    /**
     * Возвращает модель статтей
     * @return AModel
     */
    protected function getModel()
    {
        return $this->appHelper->getComponent('articlesModel');
    }
}
