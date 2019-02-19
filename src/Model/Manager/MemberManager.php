<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Member;
use PDO;

class MemberManager extends Manager
{
    /**
     * MemberManager constructor.
     */
    public function __construct()
    {
        $this->tableName = 'bl_member';
        $this->fields = [
            'id' => 'm_id',
            'email' => 'm_email',
            'password' => 'm_password',
            'name' => 'm_name',
            'description' => 'm_description'
        ];

        parent::__construct();
    }

    /**
     * Add a new member in the database
     *
     * @param Member $newMember
     * @throws Exception
     */
    public function add($newMember): void
    {
        parent::add($newMember);

        // Associate roles
        $newMember->setId($this->getLastId());
        $this->associateMemberRoles($newMember);
    }

    /**
     * Edit a member in the database
     *
     * @param Member $modifiedMember
     * @throws Exception
     */
    public function edit($modifiedMember): void
    {
        parent::edit($modifiedMember);

        // Roles
        $this->associateMemberRoles($modifiedMember);
    }

    /**
     * Delete a member in the database
     *
     * @param int $memberId
     * @throws Exception
     */
    public function delete(int $memberId): void
    {
        parent::delete($memberId);
    }

    /**
     * Get a member from its email
     *
     * @param string $email
     * @return mixed
     * @throws \Application\Exception\BlogException
     */
    public function getFromEmail(string $email): ?Member
    {
        $query = 'SELECT * FROM bl_member
            WHERE m_email = :email';

        $requestMember = $this->query($query, ['email' => $email]);

        $memberData = $requestMember->fetch(PDO::FETCH_ASSOC);

        if ($memberData) {
            $member = $this->createEntityFromTableData($memberData);
            $roles = $this->getAssociatedRoles($member->getId());
            $member->setRoles($roles);

            return $member;

        } else {
            return null;
        }
    }

    /**
     * Get a member from the database
     *
     * @param int $memberId
     * @return Member
     * @throws Exception
     */
    public function get(int $memberId): Member
    {
        $member = parent::get($memberId);

        // Roles
        $roles = $this->getAssociatedRoles($member->getId());
        $member->setRoles($roles);

        return $member;
    }

    /**
     * Get all members from the database
     *
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getAll(): array
    {
        $members = parent::getAll();

        // Roles
        foreach ($members as $member) {
            $roles = $this->getAssociatedRoles($member->getId());
            $member->setRoles($roles);
        }

        return $members;
    }

    /**
     * Check if a member is new
     *
     * @param Member $newMember
     * @return bool
     * @throws \Application\Exception\BlogException
     */
    public function isNewMember(Member $newMember): bool
    {
        $members = $this->getAll();

        if (!empty($members)) {
            foreach ($members as $member) {
                if ($member->getEmail() === $newMember->getEmail()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the id of a member from its name
     *
     * @param string $memberName
     * @return mixed
     * @throws \Application\Exception\BlogException
     */
    public function getId(string $memberName)
    {
        $query = 'SELECT m_id FROM bl_member WHERE m_name = :member';
        $requestId = $this->query($query, [
            'member' => $memberName
        ]);

        $id = (int) $requestId->fetch(PDO::FETCH_NUM)[0];

        return $id;
    }

    /**
     * Get all the member of a given role
     *
     * @param string $role
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getMembersByRole(string $role = 'member')
    {
        $members = [];

        $query = 'SELECT * FROM bl_member
            WHERE m_id IN (
                SELECT rm_member_id_fk FROM bl_role_member
                WHERE rm_role_id_fk = (
                    SELECT r_id FROM bl_role
                    WHERE r_name = :role
                )
            )';

        $requestMembers = $this->query($query, ['role' => $role]);

        while ($memberData = $requestMembers->fetch(PDO::FETCH_ASSOC)) {
            $members[] = $this->createEntityFromTableData($memberData, 'Member');
        }

        return $members;
    }

    // Private

    /**
     * Fill the table bl_role_member
     *
     * @param Member $member
     * @throws \Application\Exception\BlogException
     */
    private function associateMemberRoles(Member $member)
    {
        // Delete
        $query = 'DELETE FROM bl_role_member WHERE rm_member_id_fk = :memberId';
        $this->query($query, ['memberId' => $member->getId()]);

        // Add
        $query = 'INSERT INTO bl_role_member(rm_member_id_fk, rm_role_id_fk)
                VALUES (:memberId, :roleId)';
        $requestAdd = $this->database->prepare($query);

        $roleManager = new RoleManager();
        $roles = $roleManager->getAll();

        foreach ($member->getRoles() as $roleName) {
            foreach ($roles as $role) {
                if ($role->getName() === $roleName) {
                    $requestAdd->execute([
                        'memberId' => $member->getId(),
                        'roleId' => $role->getId()
                    ]);
                }
            }
        }
    }

    /**
     * Get the roles of a member
     *
     * @param int $memberId
     * @param bool $namesOnly
     * @return array
     * @throws \Application\Exception\BlogException
     */
    private function getAssociatedRoles(int $memberId, bool $namesOnly = true)
    {
        $query = 'SELECT * FROM bl_role
            WHERE r_id IN (
                SELECT rm_role_id_fk FROM bl_role_member
                WHERE rm_member_id_fk = :id
            )';

        $requestRoles = $this->query($query, ['id' => $memberId]);

        $roles = [];
        while ($roleData = $requestRoles->fetch(PDO::FETCH_ASSOC)) {
            if ($namesOnly) {
                $roles[] = $roleData['r_name'];
            } else {
                $roles[] = $this->createEntityFromTableData($roleData, 'Role');
            }
        }

        return $roles;
    }
}