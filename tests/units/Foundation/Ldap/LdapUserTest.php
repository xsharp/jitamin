<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../../Base.php';

use Jitamin\Foundation\Ldap\Entries;
use Jitamin\Foundation\Ldap\Query;
use Jitamin\Foundation\Ldap\User;
use Jitamin\Foundation\Security\Role;
use Jitamin\Group\LdapGroupProvider;

class LdapUserTest extends Base
{
    private $query;
    private $client;
    private $user;
    private $group;

    public function setUp()
    {
        parent::setUp();

        $this->client = $this
            ->getMockBuilder('\Jitamin\Foundation\Ldap\Client')
            ->setMethods([
                'getConnection',
            ])
            ->getMock();

        $this->query = $this
            ->getMockBuilder('\Jitamin\Foundation\Ldap\Query')
            ->setConstructorArgs([$this->client])
            ->setMethods([
                'execute',
                'hasResult',
                'getEntries',
            ])
            ->getMock();

        $this->group = $this
            ->getMockBuilder('\Jitamin\Foundation\Ldap\Group')
            ->setConstructorArgs([new Query($this->client)])
            ->setMethods([
                'find',
            ])
            ->getMock();

        $this->user = $this
            ->getMockBuilder('\Jitamin\Foundation\Ldap\User')
            ->setConstructorArgs([$this->query, $this->group])
            ->setMethods([
                'getAttributeUsername',
                'getAttributeEmail',
                'getAttributeName',
                'getAttributeGroup',
                'getAttributePhoto',
                'getGroupUserFilter',
                'getGroupAdminDn',
                'getGroupManagerDn',
                'getBasDn',
            ])
            ->getMock();
    }

    public function testGetUserWithNoGroupConfigured()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count'       => 2,
                'dn'          => 'uid=my_ldap_user,ou=People,dc=jitamin,dc=local',
                'displayname' => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'samaccountname' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
            ],
        ]);

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=jitamin,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('ou=People,dc=jitamin,dc=local'));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=People,dc=jitamin,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(null, $user->getRole());
        $this->assertSame('', $user->getPhoto());
        $this->assertEquals([], $user->getExternalGroupIds());
        $this->assertEquals(['is_ldap_user' => 1], $user->getExtraAttributes());
    }

    public function testGetUserWithPhoto()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count'       => 2,
                'dn'          => 'uid=my_ldap_user,ou=People,dc=jitamin,dc=local',
                'displayname' => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'samaccountname' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                'jpegPhoto' => [
                    'count' => 1,
                    0       => 'my photo',
                ],
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
            ],
        ]);

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=jitamin,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributePhoto')
            ->will($this->returnValue('jpegPhoto'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('ou=People,dc=jitamin,dc=local'));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('my photo', $user->getPhoto());
    }

    public function testGetUserWithAdminRole()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count'       => 2,
                'dn'          => 'uid=my_ldap_user,ou=People,dc=jitamin,dc=local',
                'displayname' => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'samaccountname' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                'memberof' => [
                    'count' => 1,
                    0       => 'CN=Jitamin-Admins,CN=Users,DC=jitamin,DC=local',
                ],
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
                3 => 'memberof',
            ],
        ]);

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=jitamin,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue('memberof'));

        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('CN=Jitamin-Admins,CN=Users,DC=jitamin,DC=local'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('ou=People,dc=jitamin,dc=local'));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=People,dc=jitamin,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(Role::APP_ADMIN, $user->getRole());
        $this->assertEquals(['CN=Jitamin-Admins,CN=Users,DC=jitamin,DC=local'], $user->getExternalGroupIds());
        $this->assertEquals(['is_ldap_user' => 1], $user->getExtraAttributes());
    }

    public function testGetUserWithManagerRole()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count'       => 2,
                'dn'          => 'uid=my_ldap_user,ou=People,dc=jitamin,dc=local',
                'displayname' => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'samaccountname' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                'memberof' => [
                    'count' => 2,
                    0       => 'CN=Jitamin-Users,CN=Users,DC=jitamin,DC=local',
                    1       => 'CN=Jitamin-Managers,CN=Users,DC=jitamin,DC=local',
                ],
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
                3 => 'memberof',
            ],
        ]);

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=jitamin,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue('memberof'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('CN=Jitamin-Managers,CN=Users,DC=jitamin,DC=local'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('ou=People,dc=jitamin,dc=local'));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=People,dc=jitamin,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(Role::APP_MANAGER, $user->getRole());
        $this->assertEquals(['CN=Jitamin-Users,CN=Users,DC=jitamin,DC=local', 'CN=Jitamin-Managers,CN=Users,DC=jitamin,DC=local'], $user->getExternalGroupIds());
        $this->assertEquals(['is_ldap_user' => 1], $user->getExtraAttributes());
    }

    public function testGetUserNotFound()
    {
        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=jitamin,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(false));

        $this->query
            ->expects($this->never())
            ->method('getEntries');

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('ou=People,dc=jitamin,dc=local'));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertEquals(null, $user);
    }

    public function testGetUserWithAdminRoleAndPosixGroups()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count' => 2,
                'dn'    => 'uid=my_ldap_user,ou=Users,dc=jitamin,dc=local',
                'cn'    => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'uid' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                0 => 'cn',
                1 => 'mail',
                2 => 'uid',
            ],
        ]);

        $groups = [
            new LdapGroupProvider('CN=Jitamin Admins,OU=Groups,DC=jitamin,DC=local', 'Jitamin Admins'),
        ];

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('OU=Users,DC=jitamin,DC=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('uid'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('(&(objectClass=posixGroup)(memberUid=%s))'));

        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('cn=Jitamin Admins,ou=Groups,dc=jitamin,dc=local'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('OU=Users,DC=jitamin,DC=local'));

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->will($this->returnValue($groups));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=Users,dc=jitamin,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(['CN=Jitamin Admins,OU=Groups,DC=jitamin,DC=local'], $user->getExternalGroupIds());
        $this->assertEquals(Role::APP_ADMIN, $user->getRole());
        $this->assertEquals(['is_ldap_user' => 1], $user->getExtraAttributes());
    }

    public function testGetUserWithManagerRoleAndPosixGroups()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count' => 2,
                'dn'    => 'uid=my_ldap_user,ou=Users,dc=jitamin,dc=local',
                'cn'    => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'uid' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                0 => 'cn',
                1 => 'mail',
                2 => 'uid',
            ],
        ]);

        $groups = [
            new LdapGroupProvider('CN=Jitamin Users,OU=Groups,DC=jitamin,DC=local', 'Jitamin Users'),
            new LdapGroupProvider('CN=Jitamin Managers,OU=Groups,DC=jitamin,DC=local', 'Jitamin Managers'),
        ];

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('OU=Users,DC=jitamin,DC=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('uid'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('(&(objectClass=posixGroup)(memberUid=%s))'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('cn=Jitamin Managers,ou=Groups,dc=jitamin,dc=local'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('OU=Users,DC=jitamin,DC=local'));

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->will($this->returnValue($groups));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=Users,dc=jitamin,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(
            [
                'CN=Jitamin Users,OU=Groups,DC=jitamin,DC=local',
                'CN=Jitamin Managers,OU=Groups,DC=jitamin,DC=local',
            ],
            $user->getExternalGroupIds()
        );
        $this->assertEquals(Role::APP_MANAGER, $user->getRole());
        $this->assertEquals(['is_ldap_user' => 1], $user->getExtraAttributes());
    }

    public function testGetUserWithUserRoleAndPosixGroups()
    {
        $entries = new Entries([
            'count' => 1,
            0       => [
                'count' => 2,
                'dn'    => 'uid=my_ldap_user,ou=Users,dc=jitamin,dc=local',
                'cn'    => [
                    'count' => 1,
                    0       => 'My LDAP user',
                ],
                'mail' => [
                    'count' => 2,
                    0       => 'user1@localhost',
                    1       => 'user2@localhost',
                ],
                'uid' => [
                    'count' => 1,
                    0       => 'my_ldap_user',
                ],
                0 => 'cn',
                1 => 'mail',
                2 => 'uid',
            ],
        ]);

        $groups = [
            new LdapGroupProvider('CN=Jitamin Users,OU=Groups,DC=jitamin,DC=local', 'Jitamin Users'),
        ];

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('OU=Users,DC=jitamin,DC=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('uid'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('(&(objectClass=posixGroup)(memberUid=%s))'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('cn=Jitamin Managers,ou=Groups,dc=jitamin,dc=local'));

        $this->user
            ->expects($this->any())
            ->method('getBasDn')
            ->will($this->returnValue('OU=Users,DC=jitamin,DC=local'));

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->will($this->returnValue($groups));

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Jitamin\Services\Identity\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=Users,dc=jitamin,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(
            [
                'CN=Jitamin Users,OU=Groups,DC=jitamin,DC=local',
            ],
            $user->getExternalGroupIds()
        );
        $this->assertEquals(Role::APP_USER, $user->getRole());
        $this->assertEquals(['is_ldap_user' => 1], $user->getExtraAttributes());
    }

    public function testGetBaseDnNotConfigured()
    {
        $this->setExpectedException('\LogicException');

        $user = new User($this->query);
        $user->getBasDn();
    }

    public function testGetLdapUserPatternNotConfigured()
    {
        $this->setExpectedException('\LogicException');

        $user = new User($this->query);
        $user->getLdapUserPattern('test');
    }

    public function testGetLdapUserWithMultiplePlaceholders()
    {
        $filter = '(|(&(objectClass=user)(mail=%s))(&(objectClass=user)(sAMAccountName=%s)))';
        $expected = '(|(&(objectClass=user)(mail=test))(&(objectClass=user)(sAMAccountName=test)))';

        $user = new User($this->query);
        $this->assertEquals($expected, $user->getLdapUserPattern('test', $filter));
    }

    public function testGetLdapUserWithOnePlaceholder()
    {
        $filter = '(sAMAccountName=%s)';
        $expected = '(sAMAccountName=test)';

        $user = new User($this->query);
        $this->assertEquals($expected, $user->getLdapUserPattern('test', $filter));
    }

    public function testGetGroupUserFilter()
    {
        $user = new User($this->query);
        $this->assertSame('', $user->getGroupUserFilter());
    }

    public function testHasGroupUserFilterWithEmptyString()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue(''));

        $this->assertFalse($this->user->hasGroupUserFilter());
    }

    public function testHasGroupUserFilterWithNull()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue(null));

        $this->assertFalse($this->user->hasGroupUserFilter());
    }

    public function testHasGroupUserFilterWithValue()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('foobar'));

        $this->assertTrue($this->user->hasGroupUserFilter());
    }

    public function testHasGroupsConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('something'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('something'));

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupAdminDnConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('something'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue(''));

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupManagerDnConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('something'));

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupsNotConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue(''));

        $this->assertFalse($this->user->hasGroupsConfigured());
    }
}
