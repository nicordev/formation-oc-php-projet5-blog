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
     * Add a new role in the database
     *
     * @param Role $newRole
     * @throws Exception
     */
    public function add($newRole): void
    {
        parent::add($newRole);
    }

    /**
     * Edit a role in the database
     *
     * @param Role $modifiedRole
     * @throws Exception
     */
    public function edit($modifiedRole): void
    {
        parent::edit($modifiedRole);
    }

    /**
     * Delete a role in the database
     *
     * @param int $roleId
     * @throws Exception
     */
    public function delete(int $roleId): void
    {
        parent::delete($roleId);
    }

    /**
     * Get a role from the database
     *
     * @param int $roleId
     * @return Role
     * @throws Exception
     */
    public function get(int $roleId): Role
    {
        return parent::get($roleId);
    }

    /**
     * Get all roles from the database
     *
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getAll(): array
    {
        return parent::getAll();
    }

    /**
     * Check if a role is new
     *
     * @param Role $newRole
     * @return bool
     * @throws \Application\Exception\BlogException
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
     * @throws \Application\Exception\BlogException
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
}