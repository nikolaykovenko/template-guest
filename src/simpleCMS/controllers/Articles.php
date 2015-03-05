<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 05.03.15
 */

namespace simpleCMS\controllers;

use simpleCMS\exceptions\Exception404;

/**
 * Модель статьи
 * @package simpleCMS\controllers
 */
class Articles extends AArticleItem
{

    /**
     * Выполнение контроллера
     * @return string
     * @throws \Exception в случае ошибки
     */
    public function execute()
    {
        if (isset($_GET['id'])) {
            return $this->showItem($_GET['id']);
        } else {
            return $this->showItemsList();
        }
        
    }
    
    

    /**
     * Отображение статьи
     * @param int $id
     * @return string
     * @throws Exception404
     */
    protected function showItem($id)
    {
        $item = $this->getItem($id);
        
        $this->setVariable('pageCaption', $item->caption);
        $this->setVariable('item', $item);
        
        return $this->render('article-item.twig');
    }

    /**
     * Отображает список статтей
     * @return string
     */
    protected function showItemsList()
    {
        return 'list';
    }
}
