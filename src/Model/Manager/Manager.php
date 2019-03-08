<?php

namespace Model\Manager;

use Application\Exception\BlogException;
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

    /**
     * @var bool|PDO
     */
    protected $database;
    protected $databaseName = 'oc_projet5_blog';
    protected $host = 'localhost';
    protected $user = 'root';
    protected $password = '';

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
    public function __construct($host = '', $databaseName = '', $user = '', $password = '', $charset = 'utf8')
    {
        if (!empty($host)) {
            $this->host = $host;
        }

        if (!empty($databaseName)) {
            $this->databaseName = $databaseName;
        }

        if (!empty($user)) {
            $this->user = $user;
        }

        if (!empty($password)) {
            $this->password = $password;
        }

        $this->database = self::getPdo($this->host, $this->databaseName, $this->user, $this->password, $charset);
    }

    /**
     * @param string $host
     * @param string $databaseName
     * @param string $user
     * @param string $password
     * @param string $charset
     * @return bool|PDO
     */
    public static function getPdo($host = 'localhost', $databaseName = 'test', $user = 'root', $password = '', $charset = 'utf8')
    {
        try
        {
            $database = new PDO('mysql:host=' . $host . ';dbname=' . $databaseName . ';charset=' . $charset, $user, $password);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(Exception $e)
        {
            return false;
        }
        return $database;
    }

    /**
     * Add an Entity in the database
     *
     * @param Entity $entity
     * @throws BlogException
     * @throws \ReflectionException
     */
    public function add(Entity $entity): void
    {
        $properties = self::getEntityProperties($entity);
        $fields = $this->filterEmptyFields($entity);

        $query = 'INSERT INTO ' . $this->tableName . '(' . implode(', ', $fields) . ')
            VALUES (:' . implode(', :', array_keys($properties)) .')';

        $this->query($query, $properties);
    }

    /**
     * Edit an Entity in the database
     *
     * @param Entity $modifiedEntity
     * @throws BlogException
     * @throws \ReflectionException
     */
    public function edit(Entity $modifiedEntity): void
    {
        $properties = self::getEntityProperties($modifiedEntity);
        $fields = $this->filterEmptyFields($modifiedEntity);

        $query = 'UPDATE ' . $this->tableName . '
            SET ' . self::buildSqlSet($fields) . '
            WHERE ' . $fields['id'] . ' = :id';

        $this->query($query, $properties);
    }

    /**
     * Delete a line in the table
     *
     * @param int $entityId
     * @throws BlogException
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
     * @throws BlogException
     */
    public function get(int $entityId)
    {
        $query = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $this->fields['id'] . ' = ?';

        $request = $this->query($query, [$entityId]);
        $tableData = $request->fetch(PDO::FETCH_ASSOC);

        return $this->createEntityFromTableData($tableData);
    }

    /**
     * Get all Entities form database
     *
     * @param int|null $numberOfLines
     * @param int|null $start
     * @return array
     * @throws BlogException
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
     * @throws BlogException
     */
    public function getLastId(): int
    {
        $query = 'SELECT MAX(' . $this->fields['id'] . ') FROM ' . $this->tableName;
        $requestLastId = $this->query($query);

        $lastId = (int) $requestLastId->fetch(PDO::FETCH_NUM)[0];

        return $lastId;
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
     * @throws BlogException
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

            if (!$request->execute($params)) {
                throw new BlogException('Error when trying to execute the query ' . $query . ' with params ' . print_r($params, true));
            }
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

    /**
     * Get the Entity child class
     *
     * @return string
     */
    private static function getEntityClass(): string
    {
        $class = explode('\\', get_called_class());
        $class = end($class);
        $class = self::ENTITY_NAMESPACE . substr($class, 0, -(strlen('Manager')));

        return $class;
    }

    /**
     * Get the manager class name of an Entity
     *
     * @param string $entityClassName without the namespace
     * @return string
     */
    private static function getManagerClass(string $entityClassName): string
    {
        $class = __NAMESPACE__ . '\\' . $entityClassName . 'Manager';

        return $class;
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
        $fields = [];

        foreach ($this->fields as $key => $value) {
            $getter = 'get' . ucfirst($key);
            if (self::isAMethodOf($entity, $getter)) {
                if ($entity->$getter() !== null) {
                    $fields[$key] = $this->fields[$key];
                }
            } else {
                $getter = 'is' . ucfirst($key);
                if (self::isAMethodOf($entity, $getter) && $entity->$getter() !== null) {
                    $fields[$key] = $this->fields[$key];
                }
            }
        }

        return $fields;
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