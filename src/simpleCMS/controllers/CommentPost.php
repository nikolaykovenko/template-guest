<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 05.03.15
 */

namespace simpleCMS\controllers;

use simpleCMS\model\Comments;

/**
 * Добавление нового комментария
 * @package simpleCMS\controllers
 */
class CommentPost extends AController
{

    /**
     * Выполнение контроллера
     * @return string
     * @throws \Exception в случае ошибки
     */
    public function execute()
    {
        parse_str($_POST['form'], $params);

        /** @var Comments $model */
        $model = $this->appHelper->getComponent('commentsModel');
        
        try {
            $item = $model->initItem($params);
            $model->insert($item);

            $this
                ->setVariable('article', $params['article'])
                ->setVariable('status', 'ok')
                ->setVariable('message', 'Комментарий успешно добавлен');
            
        } catch (\Exception $e) {
            $this
                ->setVariable('status', 'error')
                ->setVariable('message', $e->getMessage());
        }
        
        
        return $this->render('json-result.twig', false);
    }
}
