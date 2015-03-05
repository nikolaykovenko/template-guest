<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 28.02.15
 */

namespace simpleCMS\model;


/**
 * Модель истории изменений
 * @package simpleCMS\model
 */
class DataHistory extends AModel
{

    /**
     * Возвращает название таиблицы в БД
     * @return string
     */
    public function getTableName()
    {
        return 'data_history';
    }

    /**
     * Параметры модели
     * Для валидации и автоматического присвоения
     * @return array
     */
    public function attributes()
    {
        return [
            'table_name' => ['required'],
            'item_id' => ['required'],
            'data' => ['required'],
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
