<?php

namespace Model\Manager;


use Application\Exception\AppException;
use Application\Exception\BlogException;
use Exception;
use Model\Entity\ConnectionTry;
use PDO;

class ConnectionTryManager extends Manager
{
    /**
     * ConnectionTryManager constructor.
     */
    public function __construct()
    {
        $this->tableName = 'bl_connection_try';
        $this->fields = [
            'id' => 'cot_id',
            'count' => 'cot_count',
            'lastTry' => 'cot_last_try',
            'user' => 'cot_user'
        ];

        parent::__construct();
    }

    /**
     * Add a new ConnectionTry in the database
     *
     * @param ConnectionTry $newConnectionTry
     * @throws Exception
     */
    public function add($newConnectionTry): void
    {
        parent::add($newConnectionTry);
    }

    /**
     * Edit a ConnectionTry in the database
     *
     * @param ConnectionTry $modifiedConnectionTry
     * @throws Exception
     */
    public function edit($modifiedConnectionTry): void
    {
        parent::edit($modifiedConnectionTry);
    }

    /**
     * Delete a ConnectionTry in the database
     *
     * @param int $connectionTryId
     * @throws Exception
     */
    public function delete(int $connectionTryId): void
    {
        parent::delete($connectionTryId);
    }

    /**
     * Get a ConnectionTry from the database
     *
     * @param int $connectionTryId
     * @param string|null $user
     * @return ConnectionTry
     * @throws AppException
     * @throws BlogException
     */
    public function get(int $connectionTryId = null, ?string $user = null): ConnectionTry
    {
        if ($connectionTryId) {
            return parent::get($connectionTryId);
        } elseif ($user) {
            $query = 'SELECT * FROM bl_connection_try WHERE cot_user = :connectionTryUser';
            $requestConnectionTry = $this->query($query, ['connectionTryUser' => $user]);
            $connectionTryData = $requestConnectionTry->fetch(PDO::FETCH_ASSOC);
            if ($connectionTryData) {
                return $this->createEntityFromTableData($connectionTryData, 'ConnectionTry');
            }
            throw new BlogException('The ConnectionTry of ' . $user . ' was not found in the database');

        } else {
            throw new AppException('Lacks connectionTryId or user');
        }
    }

    /**
     * Get all ConnectionTries from the database
     *
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getAll(): array
    {
        return parent::getAll();
    }

    /**
     * Check if a ConnectionTry is new
     *
     * @param ConnectionTry $newConnectionTry
     * @return bool
     * @throws \Application\Exception\BlogException
     */
    public function isNewConnectionTry(ConnectionTry $newConnectionTry): bool
    {
        $connectionTries = $this->getAll();

        if (!empty($connectionTries)) {
            foreach ($connectionTries as $connectionTry) {
                if ($connectionTry->getUser() === $newConnectionTry->getUser()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the id of a ConnectionTry from its name
     *
     * @param int $user
     * @return mixed
     * @throws BlogException
     */
    public function getId(int $user)
    {
        $query = 'SELECT cot_id FROM bl_connection_try WHERE cot_user = :connectionTryUser';
        $requestId = $this->query($query, [
            'connectionTryUser' => $user
        ]);

        $id = (int) $requestId->fetch(PDO::FETCH_NUM)[0];

        return $id;
    }
}