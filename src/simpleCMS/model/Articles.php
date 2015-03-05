<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 28.02.15
 */

namespace simpleCMS\model;

/**
 * Модель пользователей
 * @package simpleCMS\model
 */
class Articles extends AModel
{

    /**
     * Возвращает название таиблицы в БД
     * @return string
     */
    public function getTableName()
    {
        return 'articles';
    }

    /**
     * Параметры, которые можно присваивать в матоматическом режиме
     * @return array
     */
    public function attributes()
    {
        return [
            'caption' => ['required'],
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
}
