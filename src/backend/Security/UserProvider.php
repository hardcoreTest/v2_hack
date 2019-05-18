<?php
namespace schedule\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use schedule\Model\User as DbUser;
use schedule\Model\UserAccess;
use schedule\Model\UserGroup as DbUserGroup;
use schedule\Model\UserGroupAccess;


class UserProvider implements UserProviderInterface
{
    /**
     * @var DbUser
     */
    private $userModel;

    /**
     * @var DbUserGroup
     */
    private $groupModel;

    /**
     * @var UserGroupAccess
     */
    private $userGroupAccess;

    /**
     * @var UserAccess
     */
    private $userAccess;

    public function __construct(
        DbUser $userModel, DbUserGroup $groupModel,
        UserGroupAccess $userGroupAccess, UserAccess $userAccess
    )
    {
        $this->userModel = $userModel;
        $this->groupModel = $groupModel;
        $this->userGroupAccess = $userGroupAccess;
        $this->userAccess = $userAccess;
    }

    public function loadUserByUsername($username)
    {
        /** @var DbUser $dbUser */
        $dbUser = $this->userModel->getByField(DbUser::FIELD_USERNAME, $username);

        if (!$dbUser) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        $roles = array();
        $userGroupId = $dbUser->getGroupId();;
        $userGroupAccess = $this->userGroupAccess->getByUserGroupId($userGroupId);
        $userAccess = $this->userAccess->getByUserId($dbUser->getUserId());
        $data = array();
        if ($userGroupAccess) {
            /** @var UserGroupAccess $value */
            foreach ($userGroupAccess as $value) {
                $data[$value->getAccessType()] = $value->getAccessType();
            }
        }
        if ($userAccess) {
            /** @var UserAccess $value */
            foreach ($userAccess as $value) {
                if ($value->getAccessStatus() == $value::STATUS_ENABLED) {
                    $data[$value->getAccessType()] = $value->getAccessType();
                } else {
                    unset($data[$value->getAccessType()]);
                }
            }
        }
        foreach ($data as $value) {
            $roles[] = $value;
        }
        return new \schedule\Security\User($dbUser->getUserId(), $dbUser->getUsername(), $dbUser->getPassword(), $roles);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}