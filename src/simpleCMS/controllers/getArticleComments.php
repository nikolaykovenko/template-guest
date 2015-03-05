<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 05.03.15
 */

namespace simpleCMS\controllers;

use simpleCMS\model\Comments;

/**
 * Получение списка комментариев статьи
 * @package simpleCMS\controllers
 */
class GetArticleComments extends AArticleItem
{

    /**
     * Выполнение контроллера
     * @return string
     * @throws \Exception в случае ошибки
     */
    public function execute()
    {
        if (!isset($_GET['article']) or !($article = $this->getItem($_GET['article']))) {
            $this->setVariable('text', 'Ошибка статьи =(');
            return $this->render('text.twig', false);
        }
        
        /** @var Comments $commentsModel */
        $commentsModel = $this->appHelper->getComponent('commentsModel');
        
        $this->setVariable('commentsList', $commentsModel->getArticleComments($article->id));
        return $this->render('comments-list.twig', false);
    }
}
