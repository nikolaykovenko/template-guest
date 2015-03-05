<?php
/**
 * @package simpleCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 23.02.15
 */

namespace simpleCMS\model;

use simpleCMS\core\ApplicationHelper;
use simpleCMS\exceptions\ValidationException;

/**
 * Базовая модель
 * @package simpleCMS\model
 */
abstract class AModel
{

    /**
     * @var ApplicationHelper
     */
    protected $appHelper;

    /**
     * @var \PDO
     */
    protected $dbh;

    /**
     * Конструктор
     * @throws \Exception
     */
    public function __construct()
    {
        $this->appHelper = ApplicationHelper::getInstance();
        $this->dbh = $this->appHelper->getDbh();
    }

    /**
     * Возвращает все записи, попадающие под параметры выборки
     * @param string $where параметр where
     * @param array $whereValues значения для подстновки
     * @return array
     */
    public function findAll($where = '', $whereValues = [])
    {
        $sth = $this->selectQueryExecute($where, $whereValues);

        $result = [];
        while ($obj = $sth->fetch()) {
            $result[] = $obj;
        }

        return $result;
    }

    /**
     * Возвращает первую запись, попадающую под параметр выборки
     * @param string $where параметр where
     * @param array $whereValues значения для подстновки
     * @return \stdClass
     */
    public function findOne($where = '', $whereValues = [])
    {
        return $this->selectQueryExecute($where, $whereValues)->fetch();
    }

    /**
     * Удаляет элемент
     * @param int $itemId
     * @return bool
     * @throws \Exception
     */
    public function deleteItem($itemId)
    {
        return $this->execute(
            $this->dbh->prepare("delete from `{$this->getTableName()}` where `id` = :id"),
            ['id' => $itemId]
        );
    }

    /**
     * Добавляет новую запись
     * @param \stdClass $instance
     * @return bool
     * @throws \Exception в случае ошибки
     */
    public function insert(\stdClass $instance)
    {
        $set = $this->prepareSet($instance, $bindParams);
        if (empty($set)) {
            return true;
        }
        
        $sth = $this->dbh->prepare("insert into {$this->getTableName()} $set");
        
        if ($this->execute($sth, $bindParams)) {
            $instance->id = $this->dbh->lastInsertId();
            return true;
        }
    }

    /**
     * Обновляет запись в БД
     * @param \stdClass $instance
     * @return bool
     * @throws \Exception
     */
    public function updateItem(\stdClass $instance)
    {
        if (!property_exists($instance, 'id')) {
            throw new \Exception('Вы не можете обновить запись без id');
        }
        
        $set = $this->prepareSet($instance, $bindParams, $oldInstance);
        if (empty($set)) {
            return true;
        }
        
        $this->insertItemToHistoryTable($oldInstance);
        
        $sth = $this->dbh->prepare("update {$this->getTableName()} $set where id = :update_id");
        $bindParams['update_id'] = $instance->id;
        
        return $this->execute($sth, $bindParams);
    }

    /**
     * Масовое присвоение значений из массива
     * @param array $attrValues значения атрибутов модели
     * @param \stdClass|null $item инициализированный элемент
     * @return \stdClass
     */
    public function initItem(array $attrValues, \stdClass $item = null)
    {
        if (is_null($item)) {
            $item = new \stdClass();
        }

        foreach ($attrValues as $attribute => $value) {
            if (array_key_exists($attribute, $this->attributes())) {
                $item->$attribute = $value;
            }
        }
        
        return $item; 
    }
    
    
    

    /**
     * Подготавливает параметр set для пере
     * @param \stdClass $instance представитель класса
     * @param array $bindParams массив со значениями параметров для последующей подстановки в выражениями
     * @param array $oldInstance представитель класса до последних изменений
     * @return string
     * @throws \Exception в случае ошибки
     */
    private function prepareSet(\stdClass $instance, &$bindParams, &$oldInstance = [])
    {
        $attributes = $this->attributes();
        if (count($attributes) == 0) {
            throw new \Exception('Safe params is not defined for model');
        }

        if (isset($instance->id)) {
            $oldInstance = $this->findOne("`id` = :id", ['id' => $instance->id]);
        } else {
            $oldInstance = null;
        }
        
        $bindParams = [];
        $query = '';
        foreach ($attributes as $attr => $params) {
            $propertyExist = property_exists($instance, $attr);
            
            if (isset($params['required']) and (!$propertyExist or empty($instance->$attr))) {
                throw new ValidationException($attr . ' is required');
            }
            
            if ($propertyExist and !empty($instance->$attr) and
                (is_null($oldInstance) or $instance->$attr != $oldInstance->$attr)) {
                $query .= "`{$attr}` = :{$attr}, ";
                $bindParams[$attr] = $instance->$attr;
            }
        }
        
        if (empty($query)) {
            return '';
        }

        return mb_substr('set ' . $query, 0, -2);
    }

    

    /**
     * Производит запрос на выборку данных
     * @param string $where
     * @param array $whereValues
     * @return \PDOStatement
     */
    private function selectQueryExecute($where = '', $whereValues = [])
    {
        $sth = $this->dbh->prepare(
            "select * from {$this->getTableName()}" . (!empty($where) ? " where " . $where : '') .
            " order by " . $this->orderBy()
        );
        $sth->setFetchMode(\PDO::FETCH_OBJ);
        $sth->execute($whereValues);
        return $sth;
    }

    /**
     * Выполняет запрос
     * @param \PDOStatement $sth
     * @param array $bindParams
     * @return bool
     * @throws \Exception
     */
    protected function execute(\PDOStatement $sth, array $bindParams)
    {
        if ($sth->execute($bindParams)) {
            return true;
        }

        throw new \Exception(implode(' ', $sth->errorInfo()));
    }

    /**
     * Возвращает параметр сортировки
     * @return string
     */
    protected function orderBy()
    {
        return "`id`";
    }

    /**
     * Вставляет значения в таблицу с историей записей
     * @param \stdClass $instance
     * @return bool
     */
    private function insertItemToHistoryTable(\stdClass $instance)
    {
        if (!$this->saveDataHistory()) {
            return true;
        }
        
        if (!isset($instance->id) or empty($instance->id)) {
            return false;
        }
        
        /** @var DataHistory $dataHistory */
        $dataHistory = $this->appHelper->getComponent('dataHistory');
        $item = $dataHistory->initItem([
            'table_name' => $this->getTableName(),
            'item_id' => $instance->id,
            'data' => serialize($instance),
        ]);
        
        return $dataHistory->insert($item);
    }

    
    /**
     * Возвращает название таиблицы в БД
     * @return string
     */
    abstract public function getTableName();

    /**
     * Параметры модели
     * Для валидации и автоматического присвоения
     * @return array
     */
    abstract public function attributes();

    /**
     * Параметр, отвечающий за хранение всей истории записей модели в специальной таблице
     * @return bool
     */
    abstract public function saveDataHistory();
}
