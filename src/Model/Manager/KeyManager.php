<?php

namespace Model\Manager;


use Application\Exception\AppException;
use Application\Exception\BlogException;
use Exception;
use Model\Entity\Key;
use PDO;

class KeyManager extends Manager
{
    /**
     * KeyManager constructor.
     */
    public function __construct()
    {
        $this->tableName = 'bl_key';
        $this->fields = [
            'id' => 'key_id',
            'value' => 'key_value'
        ];

        parent::__construct();
    }

    /**
     * Add a new key in the database
     *
     * @param Key $newKey
     * @throws Exception
     */
    public function add($newKey): void
    {
        parent::add($newKey);
    }

    /**
     * Edit a key in the database
     *
     * @param Key $modifiedKey
     * @throws Exception
     */
    public function edit($modifiedKey): void
    {
        parent::edit($modifiedKey);
    }

    /**
     * Delete a key in the database
     *
     * @param int $keyId
     * @throws Exception
     */
    public function delete(int $keyId): void
    {
        parent::delete($keyId);
    }

    /**
     * Get a key from the database
     *
     * @param int $keyId
     * @param int|null $keyValue
     * @return Key
     * @throws AppException
     * @throws BlogException
     */
    public function get(int $keyId = null, ?int $keyValue = null): Key
    {
        if ($keyId) {
            return parent::get($keyId);
        } elseif ($keyValue) {
            $query = 'SELECT * FROM bl_key WHERE key_value = :keyValue';
            $requestKey = $this->query($query, ['keyValue' => $keyValue]);
            $keyData = $requestKey->fetch(PDO::FETCH_ASSOC);
            if ($keyData) {
                return $this->createEntityFromTableData($keyData, 'Key');
            }
            throw new BlogException('The key value ' . $keyValue . ' was not found in the database');

        } else {
            throw new AppException('Lacks keyId or keyValue');
        }
    }

    /**
     * Get all keys from the database
     *
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getAll(): array
    {
        return parent::getAll();
    }

    /**
     * Check if a key is new
     *
     * @param Key $newKey
     * @return bool
     * @throws \Application\Exception\BlogException
     */
    public function isNewKey(Key $newKey): bool
    {
        $keys = $this->getAll();

        if (!empty($keys)) {
            foreach ($keys as $key) {
                if ($key->getValue() === $newKey->getValue()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the id of a key from its name
     *
     * @param int $keyValue
     * @return mixed
     * @throws \Application\Exception\BlogException
     */
    public function getId(int $keyValue)
    {
        $query = 'SELECT key_id FROM bl_key WHERE key_value = :key';
        $requestId = $this->query($query, [
            'key' => $keyValue
        ]);

        $id = (int) $requestId->fetch(PDO::FETCH_NUM)[0];

        return $id;
    }
}