<?php

namespace schedule\Model {

    use Doctrine\DBAL\Connection;
    use Pimple\Container;

    abstract class AbstractDbModel
    {

        /**
         * @var Container
         */
        protected $container;

        /**
         * Префикс
         *
         * @var string
         */
        protected $prefix = '';

        /**
         * Имя таблицы
         *
         * @var string
         */
        protected $tableName = 'table';

        /**
         * @var string
         */
        protected $keyField = 'id';

        /**
         * @var array
         */
        protected $fieldMap = array(
            'id' => 'id',
        );

        /**
         * @var array
         */
        protected $dirtyFieldStatus = array();

        /**
         * @param Container $container
         */
        public function __construct(Container $container)
        {
            $this->container = $container;
            $this->prefix = isset($container['settings']['db']['prefix']) ? $container['settings']['db']['prefix'] : '';
            $this->resetDirtyStatus();
        }

        /**
         * @return Container
         */
        public function getDIContainer()
        {
            return $this->container;
        }

        /**
         *
         */
        protected function resetDirtyStatus()
        {
            foreach ($this->fieldMap as $field => $prop) {
                $this->dirtyFieldStatus[$field] = false;
            }
        }

        /**
         * @param $fieldName
         */
        protected function dirtyField($fieldName)
        {
            if (array_key_exists($fieldName, $this->fieldMap)) {
                $this->dirtyFieldStatus[$fieldName] = true;
            }
        }

        /**
         * @return string
         */
        public function getTableName()
        {
            return $this->prefix . $this->tableName;
        }


        public function setConnection()
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            try {
                $sql = 'SHOW TABLES';
                $stmt = $db->query($sql);
                $stmt->fetchAll();
            } catch (\Exception  $e) {
                $db->close();
                $db->connect();
            }
            return true;
        }

        public function replace()
        {
            $sql = "REPLACE INTO `{$this->getTableName()}` ";
            $fields = array();
            $values = array();
            foreach ($this->fieldMap as $field => $prop) {
                if ($this->$prop !== null) {
                    $fields[] = "`{$field}`";
                    $values[] = $this->container['db']->quote(is_bool($this->$prop) ? intval($this->$prop) : $this->$prop);
                }
            }
            if (!empty($fields)) {
                $sql .= " (" . implode(', ', $fields) . ")";
            }
            $sql .= " VALUES (" . implode(', ', $values) . ")";

            $res = $this->container['db']->query($sql) !== false;
            return $res;
        }

        /**
         * @return bool
         */
        public function delete()
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            $fieldsMap = $this->fieldMap;
            $sql = "DELETE FROM `{$this->getTableName()}`  ";
            $keyFields = array();
            if (is_array($this->keyField)) {
                $where = array();
                foreach ($this->keyField as $key) {
                    $keyFields[$key] = $key;
                    $prop = $fieldsMap[$key];
                    $where[] = "`{$key}` = " . $db->quote($this->$prop);
                }
                $sqlWhere = " WHERE " . implode(" AND ", $where);
            } else {
                $key = $this->keyField;
                $keyFields[$key] = $key;
                $prop = $fieldsMap[$key];
                $sqlWhere = " WHERE `{$key}` = " . $db->quote($this->$prop);
            }


            $res = false;
            $sql .= $sqlWhere;
            $res = $db->query($sql) !== false;

            return $res;
        }


        /**
         * @param array $conds
         *
         * @return array
         */
        public function getWhere($conds = array())
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            $where = array();
            foreach ($conds as $field => $value) {
                $field = "`{$field}`";
                if (!is_array($value)) {
                    if ($value === null) {
                        $where[] = " $field is null";
                    } else {
                        $value = $db->quote($value, $db::PARAM_STR_ARRAY);
                        $where[] = " $field = $value";
                    }
                } else {
                    if (array_key_exists('type', $value)) {
                        switch ($value['type']) {
                            case "like":
                                $v = $db->quote($value['value']);
                                $where[] = "$field LIKE $v";
                                break;
                            case ">":
                                $v = $db->quote($value['value']);
                                $where[] = "$field > $v";
                                break;
                            case "<":
                                $v = $db->quote($value['value']);
                                $where[] = "$field < $v";
                                break;
                            case ">=":
                                $v = $db->quote($value['value']);
                                $where[] = "$field >= $v";
                                break;
                            case "<=":
                                $v = $db->quote($value['value']);
                                $where[] = "$field <= $v";
                                break;
                            case "!=":
                                $v = $db->quote($value['value']);
                                $where[] = "$field != $v";
                                break;
                            case "between":
                                $vStart = $db->quote($value['valueStart']);
                                $vFinish = $db->quote($value['valueFinish']);
                                $where[] = "$field between $vStart and $vFinish";
                                break;
                        }
                    } else {
                        $listItem = array();
                        $localWhere = array();
                        $find = false;
                        foreach ($value as $v) {
                            if (is_array($v) && array_key_exists('type', $v)) {
                                $find = true;
                                switch ($v['type']) {
                                    case "like":
                                        $v = $db->quote($v['value']);
                                        $where[] = "$field LIKE $v";
                                        break;
                                    case ">":
                                        $v = $db->quote($v['value']);
                                        $where[] = "$field > $v";
                                        break;
                                    case "<":
                                        $v = $db->quote($v['value']);
                                        $where[] = "$field < $v";
                                        break;
                                    case ">=":
                                        $v = $db->quote($v['value']);
                                        $where[] = "$field >= $v";
                                        break;
                                    case "<=":
                                        $v = $db->quote($v['value']);
                                        $where[] = "$field <= $v";
                                        break;
                                }
                            } else {
                                if ($v === null) {
                                    $localWhere[] = " $field is null";
                                } else {
                                    $listItem[] = $db->quote($v, $db::PARAM_STR_ARRAY);
                                }
                            }
                        }
                        if (empty($listItem) && !$find) {
                            $listItem[] = '"````"';
                        }
                        $list = implode(', ', $listItem);
                        if (!empty($list)) {
                            if (empty($localWhere)) {
                                $where[] = "$field in ($list)";
                            } else {
                                $where[] = "($field in ($list) OR " . implode(" AND ", $localWhere) . ')';
                            }
                        }
                    }
                }
            }
            return $where;
        }

        public function startTransaction()
        {
            /** @var Connection $dbh */
            $dbh = $this->container['db'];
            $dbh->beginTransaction();
        }

        public function commitTransaction()
        {
            /** @var Connection $dbh */
            $dbh = $this->container['db'];
            $dbh->commit();
        }

        public function rollBackTransaction()
        {
            /** @var Connection $dbh */
            $dbh = $this->container['db'];
            $dbh->rollBack();
        }

        /**
         * @param array $conds
         * @param array $order
         * @param int $limit
         * @param int $offset
         * @param array $fields
         * @return array
         */
        public function search($conds = array(), $order = array(), $limit = 1000, $offset = 0, $fields = array())
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            $tableName = static::getTableName();
            $where = $this->getWhere($conds);
            $fieldMap = $this->fieldMap;
            $orderList = array();
            foreach ($order as $field => $dir) {
                if (array_key_exists($field, $fieldMap)) {
                    $field = "`{$field}`";
                    if (strtolower($dir) != 'asc') {
                        $dir = 'desc';
                    }
                    $orderList[] = "$field $dir";
                }
            }

            $limits = '';

            if ($limit > 0) {
                $limits = intval($limit);
            }

            if (!empty($limits) && $offset > 0) {
                $limits = intval($offset) . ", $limits";
            }
            $fields = '';
            if (is_array($fields)) {
                $fields = implode(',', $fields);
            } else {
                $fields = '*';
            }

            $sql = "SELECT $fields FROM $tableName";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }

            if (!empty($orderList)) {
                $sql .= " ORDER BY " . implode(', ', $orderList);
            }

            if (!empty($limits)) {
                $sql .= " LIMIT {$limits}";
            }
            $stmt = $db->query($sql);
            if (!$stmt) {
                $error = $db->errorInfo();
                throw new \Exception("Error in sql-query: \"{$sql}\": ({$db->errorCode()}) " . $error[2]);
            }
            $stmt->execute();

            $result = array();
            $rows = $db->fetchAll($sql);
            return $rows;
        }

        /**
         * Получить количество по условию
         *
         * @param array $conds
         * @return int
         * @throws \Exception
         */
        public function searchCount($conds = array())
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            $tableName = static::getTableName();

            $where = static::getWhere($conds);

            $sql = "SELECT count(*) FROM $tableName";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $stmt = $db->query($sql);
            if (!$stmt) {
                $error = $db->errorInfo();
                throw new \Exception("Error in sql-query: \"{$sql}\": ({$db->errorCode()}) " . $error[2]);
            }
            $stmt->execute();

            return intval($stmt->fetch(\PDO::FETCH_COLUMN));
        }


        /**
         * Извлекает все записи
         *
         * @param array $order
         * @return array
         */
        public function fetchAll(array $order = array())
        {
            $result = array();
            /** @var Connection $db */
            $db = $this->container['db'];
            $sql = "SELECT * FROM `{$this->getTableName()}`";
            if (!empty($order)) {
                $orderValues = array();
                foreach ($order as $field => $dir) {
                    if (!array_key_exists($field, $this->fieldMap)) {
                        throw new \Exception("Unknown field \"{$field}\" in " . __CLASS__);
                    }
                    if (strtolower($dir) !== 'asc' && strtolower($dir) !== 'desc') {
                        throw new \Exception("Unknown order dir \"{$dir}\" in " . __CLASS__);
                    }
                    $orderValues[] = "{$db->quoteIdentifier($field)} {$dir}";
                }
                $sql .= " ORDER BY " . implode(", ", $orderValues);
            }
            $rows = $db->fetchAll($sql);
            foreach ($rows as $row) {
                $className = get_class($this);
                /** @var AbstractDbModel $obj */
                $obj = new $className($this->container);
                $obj->fillFromArray($row);
                $obj->resetDirtyStatus();
                $result[] = $obj;
            }

            return $result;
        }

        /**
         * Извлекает запись по ID
         *
         * @param mixed $value
         * @return AbstractDbModel
         */
        public function getByPk($value)
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            if (is_array($this->keyField)) {
                $list = array();
                foreach ($this->keyField as $field) {
                    $list[] = "`{$field}` = " . $db->quote($value[$field]);
                }
                $where = "WHERE " . implode(" AND ", $list);
            } else {
                $where = "WHERE `{$this->keyField}` = " . $db->quote($value);
            }
            $rows = $db->fetchAll("SELECT * FROM `{$this->getTableName()}` {$where} LIMIT 1");
            $result = null;
            if (!empty($rows)) {
                $className = get_class($this);
                /** @var AbstractDbModel $result */
                $result = new $className($this->container);
                $result->fillFromArray($rows[0]);
                $result->resetDirtyStatus();
            }

            return $result;
        }

        /**
         * Извлекает запись по полям
         *
         * @param mixed $fields
         * @param mixed $value
         * @return AbstractDbModel
         *
         * @throws \Exception
         */
        public function getByField($fields, $value)
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            if (is_array($fields)) {
                $list = array();
                foreach ($fields as $field) {
                    if (!array_key_exists($field, $this->fieldMap)) {
                        throw new \Exception("Unknown field \"{$field}\" in model \"" . get_class($this) . "\"");
                    }
                    $list[] = "`{$field}` = " . $db->quote($value[$field]);
                }
                $where = "WHERE " . implode(" AND ", $list);
            } else {
                if (!array_key_exists($fields, $this->fieldMap)) {
                    throw new \Exception("Unknown field \"{$fields}\" in model \"" . get_class($this) . "\"");
                }
                $where = "WHERE `{$fields}` = " . $db->quote($value);
            }
            $rows = $db->fetchAll("SELECT * FROM `{$this->getTableName()}` {$where} LIMIT 1");
            $result = null;
            if (!empty($rows)) {
                $className = get_class($this);
                /** @var AbstractDbModel $result */
                $result = new $className($this->container);
                $result->fillFromArray($rows[0]);
                $result->resetDirtyStatus();
            }

            return $result;
        }

        /**
         * @param $data
         */
        public function fillFromArray($data)
        {
            foreach ($data as $field => $value) {
                if (array_key_exists($field, $this->fieldMap)) {
                    $prop = $this->fieldMap[$field];
                    if ($value != $this->$prop) {
                        $this->dirtyFieldStatus[$field] = true;
                    }
                    $this->$prop = $value;
                }
            }
        }

        /**
         * @return array
         */
        public function toArray()
        {
            $result = array();
            foreach ($this->fieldMap as $field => $prop) {
                $result[$field] = $this->$prop;
            }
            return $result;
        }

        /**
         * @return bool
         */
        public function insert()
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            $data = $this->toArray();
            foreach ($data as &$value) {
                if (is_bool($value)) {
                    $value = intval($value);
                }
            }
            return $db->insert($this->getTableName(), $data) > 0;
        }

        /**
         * @return bool
         */
        public function update()
        {
            /** @var Connection $db */
            $db = $this->container['db'];
            $data = array();
            foreach ($this->dirtyFieldStatus as $field => $dirty) {
                if ($dirty) {
                    $prop = $this->fieldMap[$field];
                    if ($this->$prop === null) {
                        $data[] = "{$db->quoteIdentifier($field)} = NULL";
                    } else {
                        if (is_bool($this->$prop)) {
                            $this->$prop = intval($this->$prop);
                        }
                        $data[] = "{$db->quoteIdentifier($field)} = {$db->quote($this->$prop)}";
                    }
                }
            }
            if (!empty($data)) {
                $whereConds = array();
                if (is_array($this->keyField)) {
                    foreach ($this->keyField as $field) {
                        $keyProp = $this->fieldMap[$field];
                        $whereConds[] = "{$db->quoteIdentifier($field)} = {$db->quote($this->$keyProp)}";
                    }
                } else {
                    $keyProp = $this->fieldMap[$this->keyField];
                    $whereConds[] = "{$db->quoteIdentifier($this->keyField)} = {$db->quote($this->$keyProp)}";
                }
                $where = implode(" AND ", $whereConds);
                $sql = "UPDATE {$this->getTableName()} SET " . implode(", ", $data) . " WHERE {$where}";
                return $db->exec($sql) > 0;
            }
            return true;
        }

    }
}