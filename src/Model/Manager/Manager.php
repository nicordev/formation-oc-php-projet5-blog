<?php

namespace Model\Manager;

use Application\Exception\HttpException;
use Exception;
use Model\Entity\Entity;
use \PDO;
use PDOException;
use ReflectionClass;

/**
 * Class Manager
 * @package Model
 */
abstract class Manager
{
    protected $tableName = '';
    protected $fields = [];
    protected $database;

    protected const ENTITY_NAMESPACE = 'Model\\Entity\\';

    /**
     * Manager constructor.
     *
     * @param string $host
     * @param string $databaseName
     * @param string $user
     * @param string $password
     * @param string $charset
     */
    public function __construct()
    {
        $this->database = Database::getPdo();
    }

    /**
     * Add an Entity in the database
     *
     * @param Entity $entity
     * @throws HttpException
     * @throws \ReflectionException
     */
    public function add(Entity $entity): void
    {
        $properties = self::getEntityProperties($entity);
        $fullFields = $this->filterEmptyFields($entity);

        ksort($properties);
        ksort($fullFields);

        $query = 'INSERT INTO ' . $this->tableName . '(' . implode(', ', $fullFields) . ')
            VALUES (:' . implode(', :', array_keys($properties)) .')';

        $this->query($query, $properties);
    }

    /**
     * Edit an Entity in the database
     *
     * @param Entity $modifiedEntity
     * @throws HttpException
     * @throws \ReflectionException
     */
    public function edit(Entity $modifiedEntity): void
    {
        $properties = self::getEntityProperties($modifiedEntity);
        $fullFields = $this->filterEmptyFields($modifiedEntity);

        ksort($properties);
        ksort($fullFields);

        $query = 'UPDATE ' . $this->tableName . '
            SET ' . self::buildSqlSet($fullFields) . '
            WHERE ' . $fullFields['id'] . ' = :id';

        $this->query($query, $properties);
    }

    /**
     * Delete a line in the table
     *
     * @param int $entityId
     * @throws HttpException
     */
    public function delete(int $entityId)
    {
        $query = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $this->fields['id'] . ' = ?';

        $this->query($query, [$entityId]);
    }

    /**
     * Delete all lines in the table
     */
    public function deleteAll()
    {
        $query = 'DELETE FROM ' . $this->tableName;

        $this->query($query);
    }

    /**
     * Get an Entity from the database
     *
     * @param int $entityId
     * @return mixed
     * @throws HttpException
     */
    public function get(int $entityId)
    {
        $query = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $this->fields['id'] . ' = ?';

        $request = $this->query($query, [$entityId]);
        $tableData = $request->fetch(PDO::FETCH_ASSOC);
        if ($tableData) {
            return $this->createEntityFromTableData($tableData);
        }
        throw new HttpException('The id ' . $entityId . ' was not found in the database', 500);
    }

    /**
     * Get all Entities form database (or just some Entities)
     *
     * @param int|null $numberOfLines
     * @param int|null $start
     * @return array
     * @throws HttpException
     */
    public function getAll(?int $numberOfLines = null, ?int $start = null): array
    {
        $entities = [];
        $query = "SELECT * FROM " . $this->tableName;
        if ($numberOfLines) {
            self::addLimitToQuery($query, $numberOfLines, $start);
        }

        $requestAllEntities = $this->query($query);

        $tableData = $requestAllEntities->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tableData as $tableDatum) {
            $entity = $this->createEntityFromTableData($tableDatum);
            $entities[$entity->getId()] = $entity;
        }

        return $entities;
    }

    /**
     * Get the last id.
     *
     * @return int
     * @throws HttpException
     */
    public function getLastId(): int
    {
        $query = 'SELECT MAX(' . $this->fields['id'] . ') FROM ' . $this->tableName;
        $requestLastId = $this->query($query);

        return (int) $requestLastId->fetch(PDO::FETCH_NUM)[0];
    }

    /**
     * Count the number of lines
     *
     * @return int
     * @throws HttpException
     */
    public function countLines(): int
    {
        $query = 'SELECT COUNT(' . $this->fields['id'] . ') FROM ' . $this->tableName;
        $requestCount = $this->query($query);

        return (int) $requestCount->fetch(PDO::FETCH_NUM)[0];
    }


    // Protected

    /**
     * Create an Entity child from database data
     *
     * @param array $tableData
     * @param string|null $className If null, the method will use the entity linked to the calling manager
     * @return mixed
     */
    protected function createEntityFromTableData(array $tableData, ?string $className = null)
    {
        $entityData = [];

        if ($className !== null) {
            $entityClass = self::ENTITY_NAMESPACE . $className;
            // Find the right manager
            $managerClass = self::getManagerClass($className);
            $entityManager = new $managerClass();

            foreach ($entityManager->fields as $key => $value) {
                $entityData[$key] = $tableData[$value];
            }

        } else {
            $entityClass = self::getEntityClass();

            foreach ($this->fields as $key => $value) {
                $entityData[$key] = $tableData[$value];
            }
        }

        return new $entityClass($entityData);
    }

    /**
     * Prepare then execute a SQL query with parameters or execute a simple query
     *
     * @param string $query
     * @param array $params
     * @return bool|\PDOStatement
     * @throws HttpException
     */
    protected function query(string $query, ?array $params = null)
    {
        if ($params !== null) {

            $request = $this->database->prepare($query);

            foreach ($params as &$param) {
                if (is_bool($param)) {
                    if ($param) {
                        $param = 1;
                    } else {
                        $param = 0;
                    }
                }
            }
            self::executePreparedQuery($request, $params);

        } else {
            $request = $this->database->query($query);
        }

        return $request;
    }

    /**
     * Add a LIMIT and OFFSET statement to a query
     *
     * @param string $query
     * @param int $numberOfLines
     * @param int|null $start
     */
    protected static function addLimitToQuery(string &$query, int $numberOfLines, ?int $start = null)
    {
        $query .= ' LIMIT ' . $numberOfLines;
        if ($start) {
            $query .= ' OFFSET ' . $start;
        }
    }


    // Private

    private static function executePreparedQuery(\PDOStatement $request, array $params)
    {
        try {
            if (!$request->execute($params)) {
                throw new HttpException('Error when trying to execute the query ' . $request["queryString"] . ' with params ' . print_r($params, true), 500);
            }
        } catch (PDOException $e) {
            throw new HttpException('Error when trying to execute the query ' . $request["queryString"] . ' with params ' . print_r($params, true), 500, $e);
        }
    }

    /**
     * Get the Entity child class
     *
     * @return string
     */
    private static function getEntityClass(): string
    {
        $class = explode('\\', get_called_class());
        $class = end($class);
        return self::ENTITY_NAMESPACE . substr($class, 0, -(strlen('Manager')));
    }

    /**
     * Get the manager class name of an Entity
     *
     * @param string $entityClassName without the namespace
     * @return string
     */
    private static function getManagerClass(string $entityClassName): string
    {
        return __NAMESPACE__ . '\\' . $entityClassName . 'Manager';
    }

    /**
     * Return a string to use in SQL query SET
     *
     * @param array $fields
     * @return string
     */
    private static function buildSqlSet(array $fields): string
    {
        $pieces = [];

        foreach ($fields as $key => $value) {
            $pieces[] = $value . ' = :' . $key;
        }

        return implode(', ', $pieces);
    }

    /**
     * Return an array with only filled fields
     *
     * @param Entity $entity
     * @return array
     * @throws \ReflectionException
     */
    private function filterEmptyFields(Entity $entity)
    {
        $entityFields = [];

        foreach ($this->fields as $key => $value) {
            $getter = 'get' . ucfirst($key);
            if (self::isAMethodOf($entity, $getter)) {
                if ($entity->$getter() !== null) {
                    $entityFields[$key] = $this->fields[$key];
                }
            } else {
                $getter = 'is' . ucfirst($key);
                if (self::isAMethodOf($entity, $getter) && $entity->$getter() !== null) {
                    $entityFields[$key] = $this->fields[$key];
                }
            }
        }

        return $entityFields;
    }

    /**
     * Check if a method exists in an Entity
     *
     * @param Entity $entity
     * @param string $method
     * @return bool
     * @throws \ReflectionException
     */
    private static function isAMethodOf(Entity $entity, string $method)
    {
        $refClass = new ReflectionClass($entity);

        return $refClass->hasMethod($method);
    }

    /**
     * Get filled properties of an Entity (filter null values and arrays)
     *
     * @param Entity $entity
     * @return array
     * @throws \ReflectionException
     */
    private static function getEntityProperties(Entity $entity)
    {
        $properties = [];
        $reflectionEntity = new ReflectionClass($entity);
        $reflectionMethods = $reflectionEntity->getMethods();

        foreach ($reflectionMethods as $reflectionMethod) {
            if (strpos($reflectionMethod, 'get')) {
                $value = $reflectionMethod->invoke($entity);
                if (
                    $value !== null &&
                    !is_array($value
                    )) {
                    $properties[lcfirst(substr($reflectionMethod->name, 3))] = $value;
                }
            } elseif (strpos($reflectionMethod, 'is')) {
                $value = $reflectionMethod->invoke($entity);
                if (
                    $value !== null &&
                    !is_array($value
                    )) {
                    $properties[lcfirst(substr($reflectionMethod->name, 2))] = $value;
                }
            }
        }

        return $properties;
    }
}