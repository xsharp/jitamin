<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Group;

use Jitamin\Foundation\Group\GroupProviderInterface;

/**
 * LDAP Group Provider.
 */
class LdapGroupProvider implements GroupProviderInterface
{
    /**
     * Group DN.
     *
     * @var string
     */
    private $dn = '';

    /**
     * Group Name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Constructor.
     *
     * @param string $dn
     * @param string $name
     */
    public function __construct($dn, $name)
    {
        $this->dn = $dn;
        $this->name = $name;
    }

    /**
     * Get internal id.
     *
     * @return int
     */
    public function getInternalId()
    {
        return '';
    }

    /**
     * Get external id.
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->dn;
    }

    /**
     * Get group name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
