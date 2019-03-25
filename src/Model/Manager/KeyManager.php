<?php

namespace Model\Manager;


use Application\Exception\AppException;
use Application\Exception\HttpException;
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
     * Get a key from the database
     *
     * @param int $keyId
     * @param int|null $keyValue
     * @return Key
     * @throws AppException
     * @throws HttpException
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
            throw new HttpException('The key value ' . $keyValue . ' was not found in the database', 404);

        } else {
            throw new AppException('Lacks keyId or keyValue');
        }
    }

    /**
     * Check if a key is new
     *
     * @param Key $newKey
     * @return bool
     * @throws \Application\Exception\HttpException
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
     * @throws \Application\Exception\HttpException
     */
    public function getId(int $keyValue)
    {
        $query = 'SELECT key_id FROM bl_key WHERE key_value = :key';
        $requestId = $this->query($query, [
            'key' => $keyValue
        ]);

        return (int) $requestId->fetch(PDO::FETCH_NUM)[0];
    }
}
