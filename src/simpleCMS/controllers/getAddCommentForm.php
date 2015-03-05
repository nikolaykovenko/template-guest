<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 05.03.15
 */

namespace simpleCMS\controllers;

/**
 * Получение формы для добавления нового комментария
 * @package simpleCMS\controllers
 */
class GetAddCommentForm extends AArticleItem
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
        
        $this->setVariable('article', $article->id);
        if (isset($_GET['reply'])) {
            $this->setVariable('reply', $_GET['reply']);
        }
        
        return $this->render('forms/add-comment.twig', false);
    }
}
