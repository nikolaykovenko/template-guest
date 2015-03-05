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
        return $this->makeCommentsTree(parent::findAll($where, $whereValues));
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


    /**
     * Преобразовывает полученные из БД комментарии в древовидное состояние
     * @param array $rawData
     * @return array
     */
    private function makeCommentsTree(array $rawData)
    {
        $result = [];
        
        foreach ($rawData as $line) {
            if ( $line->parent_comment == null) {
                $line = $this->addItemSubItems($rawData, $line);
                $result[] = $line;
            }
        }
        
        return $result;
    }

    /**
     * Добавляет подэлементы
     * @param array $rawData
     * @param \stdClass $item
     * @return \stdClass
     */
    private function addItemSubItems(array $rawData, \stdClass $item)
    {
        $subItems = [];
        $result = $item;
        
        foreach ($rawData as $rawItem) {
            if ($rawItem->parent_comment == $item->id) {
                $subItems[] = $rawItem;
            }
        }
        
        if (!empty($subItems)) {
            foreach ($subItems as &$subItem) {
                $subItem = $this->addItemSubItems($rawData, $subItem);
            }
            $result->sub = $subItems;
        }
        
        return $result;
    }
}
