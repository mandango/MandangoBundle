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
use Mandango\Mandango;

/**
 * MandangoUserProvider.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoUserProvider implements UserProviderInterface
{
    private $mandango;
    private $class;
    private $property;

    /**
     * Constructor.
     *
     * @param Mandango    $mandango The mandango.
     * @param string      $class    The class.
     * @param string|null $property The property (optional).
     */
    public function __construct(Mandango $mandango, $class, $property = null)
    {
        $this->mandango = $mandango;
        $this->class = $class;
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $class = $this->class;
        $repository = $this->mandango->getRepository($class);

        if (null !== $this->property) {
            $user = $repository->createQuery(array($this->property => $username))->one();
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
    public function refreshUser(UserInterface $user)
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
