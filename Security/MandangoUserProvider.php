<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * MandangoUserProvider.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoUserProvider implements UserProviderInterface
{
    protected $class;
    protected $property;

    /**
     * Constructor.
     *
     * @param string $class The class.
     * @param string|null $property The property (optional).
     */
    public function __construct($class, $property = null)
    {
        $this->class = $class;
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $class = $this->class;
        $repository = $class::repository();

        if (null !== $this->property) {
            $user = $repository->query(array($this->property => $username))->one();
        } else {
            if (!$repository instanceof UserProviderInterface) {
                throw new \InvalidArgumentException(sprintf('The Mandango repository "%s" must implement UserProviderInterface.', get_class($repository)));
            }

            $user = $repository->loadUserByUsername($username);
        }

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUser(UserInterface $user)
    {
        if (!$user instanceof $this->class) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->class;
    }
}
