<?php

namespace Model\Manager;

use Application\Exception\BlogException;
use Model\Entity\Entity;
use \PDO;
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

        $this->prepareThenExecuteQuery($query, $properties);
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

        $this->prepareThenExecuteQuery($query, $properties);
    }

    /**
     * Delete an Entity in the database
     *
     * @param int $entityId
     * @throws BlogException
     */
    public function delete(int $entityId)
    {
        $query = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $this->fields['id'] . ' = ?';

        $this->prepareThenExecuteQuery($query, [$entityId]);
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

        $request = $this->prepareThenExecuteQuery($query, [$entityId]);
        $tableData = $request->fetch(PDO::FETCH_ASSOC);

        return $this->createEntityFromTableData($tableData);
    }

    /**
     * Get all Entities form database
     *
     * @return array
     */
    public function getAll(): array
    {
        $entities = [];
        $query = "SELECT * FROM " . $this->tableName;

        $requestAllEntities = $this->database->query($query);
        $tableData = $requestAllEntities->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tableData as $tableDatum) {
            $entities[] = $this->createEntityFromTableData($tableDatum);
        }

        return $entities;
    }


    // Private

    /**
     * Create an Entity child from database data
     *
     * @param array $tableData
     * @return mixed
     */
    private function createEntityFromTableData(array $tableData)
    {
        $entityClass = self::getEntityClass();
        $entityData = [];

        foreach ($this->fields as $key => $value) {
            $entityData[$key] = $tableData[$value];
        }

        return new $entityClass($entityData);
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
        $class = 'Model\\Entity\\' . substr($class, 0, -(strlen('Manager')));

        return $class;
    }

    /**
     * Prepare then execute a SQL query with parameters
     *
     * @param string $query
     * @param array $params
     * @return bool|\PDOStatement
     * @throws BlogException
     */
    private function prepareThenExecuteQuery(string $query, array $params)
    {
        $request = $this->database->prepare($query);

        if (!$request->execute($params)) {
            throw new BlogException('Error when trying to execute the query ' . $query . ' with params ' . print_r($params, true));
        }

        return $request;
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
     */
    private function filterEmptyFields(Entity $entity)
    {
        $fields = [];

        foreach ($this->fields as $key => $value) {
            $getter = 'get' . ucfirst($key);
            if ($entity->$getter() !== null) {
                $fields[$key] = $this->fields[$key];
            }
        }

        return $fields;
    }

    /**
     * Get filled properties of an Entity (filter null values)
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
                if ($value !== null)
                    $properties[lcfirst(substr($reflectionMethod->name, 3))] = $value;
            }
        }

        return $properties;
    }
}