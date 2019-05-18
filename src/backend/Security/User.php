<?php

namespace schedule\Security {

    use Symfony\Component\Security\Core\User\UserInterface;

    class User implements UserInterface
    {

        /**
         * @var string
         */
        private $userId;

        /**
         * @var string
         */
        private $userName;

        /**
         * @var string
         */
        private $userPassword;

        /**
         * @var string[]
         */
        private $userRoles;

        public function __construct($userId, $userName, $userPassword, array $userRoles)
        {
            $this->userId = $userId;
            $this->userName = $userName;
            $this->userPassword = $userPassword;
            $this->userRoles = $userRoles;
        }

        /**
         * Returns the roles granted to the user.
         *
         * <code>
         * public function getRoles()
         * {
         *     return array('ROLE_USER');
         * }
         * </code>
         *
         * Alternatively, the roles might be stored on a ``roles`` property,
         * and populated in any number of different ways when the user object
         * is created.
         *
         * @return string[] The user roles
         */
        public function getRoles()
        {
            return $this->userRoles;
        }

        /**
         * Returns the password used to authenticate the user.
         *
         * This should be the encoded password. On authentication, a plain-text
         * password will be salted, encoded, and then compared to this value.
         *
         * @return string The password
         */
        public function getPassword()
        {
            return $this->userPassword;
        }

        /**
         * Returns the salt that was originally used to encode the password.
         *
         * This can return null if the password was not encoded using a salt.
         *
         * @return string|null The salt
         */
        public function getSalt()
        {
            return $this->userId;
        }

        /**
         * Returns the username used to authenticate the user.
         *
         * @return string The username
         */
        public function getUsername()
        {
            return $this->userName;
        }

        /**
         * Returns the username used to authenticate the user.
         *
         * @return string The username
         */
        public function getUserId()
        {
            return $this->userId;
        }

        /**
         * Removes sensitive data from the user.
         *
         * This is important if, at any given point, sensitive information like
         * the plain-text password is stored on this object.
         */
        public function eraseCredentials()
        {

        }
    }
}