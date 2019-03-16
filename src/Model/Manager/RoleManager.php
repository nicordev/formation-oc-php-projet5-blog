<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Role;
use PDO;

class RoleManager extends Manager
{
    /**
     * RoleManager constructor.
     */
    public function __construct()
    {
        $this->tableName = 'bl_role';
        $this->fields = [
            'id' => 'r_id',
            'name' => 'r_name'
        ];

        parent::__construct();
    }

    /**
     * Check if a role is new
     *
     * @param Role $newRole
     * @return bool
     * @throws \Application\Exception\HttpException
     */
    public function isNewRole(Role $newRole): bool
    {
        $roles = $this->getAll();

        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role->getName() === $newRole->getName()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the id of a role from its name
     *
     * @param string $roleName
     * @return mixed
     * @throws \Application\Exception\HttpException
     */
    public function getId(string $roleName)
    {
        $query = 'SELECT r_id FROM bl_role WHERE r_name = :role';
        $requestId = $this->query($query, [
            'role' => $roleName
        ]);

        $id = (int) $requestId->fetch(PDO::FETCH_NUM)[0];

        return $id;
    }

    /**
     * Check if the role is a valid one
     *
     * @param string $role
     * @return bool
     * @throws \Application\Exception\HttpException
     */
    public function isValid(string $role): bool
    {
        $roles = $this->getRoleNames();

        if (in_array($role, $roles)) {
            return true;
        }
        return false;
    }

    /**
     * Get the names of the roles
     *
     * @return array
     * @throws \Application\Exception\HttpException
     */
    public function getRoleNames()
    {
        $roles = $this->getAll();
        $roleNames = [];

        foreach ($roles as $role) {
            $roleNames[] = $role->getName();
        }

        return $roleNames;
    }
}