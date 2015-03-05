<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 28.02.15
 */

namespace simpleCMS\model;

/**
 * Модель пользовательских файлов
 * @package simpleCMS\model
 */
class Comments extends AModel
{

    /**
     * Возвращает список комментариев для статьи
     * @param int $articleId
     * @return array
     */
    public function getArticleComments($articleId)
    {
        return $this->findAll("`article` = :article_id", ['article_id' => $articleId]);
    }
    
    /**
     * @inheritdoc
     */
    public function findAll($where = '', $whereValues = [])
    {
//        TODO: Добавить построение дерева комментариев
        
        $result = parent::findAll($where, $whereValues);
        return $result;
    }

    /**
     * Возвращает название таиблицы в БД
     * @return string
     */
    public function getTableName()
    {
        return 'comments';
    }

    /**
     * Параметры модели
     * Для валидации и автоматического присвоения
     * @return array
     */
    public function attributes()
    {
        return [
            'article' => ['required'],
            'parent_comment' => [],
            'fio' => ['required'],
            'email' => [],
            'text' => ['required'],
        ];
    }

    /**
     * Параметр, отвечающий за хранение всей истории записей модели в специальной таблице
     * @return bool
     */
    public function saveDataHistory()
    {
        return false;
    }

    
    

    /**
     * @inheritdoc
     */
    protected function orderBy()
    {
        return "`id` desc";
    }
}
