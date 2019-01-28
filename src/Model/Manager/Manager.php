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
     */
    public function add(Entity $entity): void
    {
        $properties = self::getEntityProperties($entity);
        $fields = $this->filterEmptyFields($entity);

        $query = 'INSERT INTO ' . $this->tableName . '(' . implode(', ', $fields) . ')
            VALUES (:' . implode(', :', array_keys($properties)) .')';

        $requestAdd = $this->database->prepare($query);

        if (!$requestAdd->execute($properties)) {
            throw new BlogException('Error when trying to add the new entity in the database.');
        }
    }

    public function edit(Entity $modifiedEntity): void
    {
        $properties = self::getEntityProperties($modifiedEntity);
        $keys = self::getEntityKeys($modifiedEntity);
        $fields = $this->filterEmptyFields($modifiedEntity);

        var_dump($properties, $keys, $fields);
        die;

        $query = 'UPDATE ' . $this->tableName . '
            SET 
            WHERE';

        $requestAdd = $this->database->prepare($query);

        if (!$requestAdd->execute($properties)) {
            throw new BlogException('Error when trying to add the new entity in the database.');
        }
    }

    // Private

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
            if ($entity->$key !== null) {
                $fields[] = $this->fields[$key];
            }
        }

        return $fields;
    }

    /**
     * Get filled properties of an Entity (filter null values)
     *
     * @param Entity $entity
     * @return array
     */
    private static function getEntityProperties(Entity $entity)
    {
        $properties = [];

        foreach ($entity as $key => $value) {
            if ($value !== null)
                $properties[$key] = $value;
        }

        return $properties;
    }

    /**
     * @param Entity $entity
     * @return array
     */
    private static function getEntityKeys(Entity $entity)
    {
        $keys = [];

        foreach ($entity as $key => $value) {
            if ($value !== null)
                $keys[] = $key;
        }

        return $keys;
    }
}