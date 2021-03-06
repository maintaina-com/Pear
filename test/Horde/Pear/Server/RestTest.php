<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
namespace Horde\Pear\Server;
use Horde\Pear\TestCase;

/**
 * Test the REST connector.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class RestTest extends TestCase
{
    private $_server;

    public function setUp(): void
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $config = self::getConfig('PEAR_TEST_CONFIG');
        if ($config && !empty($config['pear']['server'])) {
            $this->_server = $config['pear']['server'];
        } else {
            $this->markTestSkipped('Missing configuration!');
        }
    }

    public function testFetchPackageList()
    {
        $this->assertInternalType(
            'resource',
            $this->_getRest()->fetchPackageList()
        );
    }

    public function testPackageListResponse()
    {
        $response = $this->_getRest()->fetchPackageList();
        rewind($response);
        $this->assertContains(
            'Horde_Core',
            stream_get_contents($response)
        );
    }

    public function testPackageList()
    {
        $pl = new Horde_Pear_Rest_PackageList(
            $this->_getRest()->fetchPackageList()
        );
        $this->assertContains(
            'Horde_Core',
            $pl->listPackages()
        );
    }

    public function testFetchPackageInformation()
    {
        $this->assertInternalType(
            'resource',
            $this->_getRest()->fetchPackageInformation('Horde_Core')
        );
    }

    public function testPackageInformationResponse()
    {
        $response = $this->_getRest()->fetchPackageInformation('Horde_Core');
        rewind($response);
        $this->assertContains(
            'Core Horde Framework library',
            stream_get_contents($response)
        );
    }

    public function testFetchPackageReleases()
    {
        $this->assertInternalType(
            'resource',
            $this->_getRest()->fetchPackageReleases('Horde_Core')
        );
    }

    public function testPackageReleasesResponse()
    {
        $response = $this->_getRest()->fetchPackageReleases('Horde_Core');
        rewind($response);
        $this->assertContains(
            '1.1.0',
            stream_get_contents($response)
        );
    }

    public function testFetchLatestPackageReleases()
    {
        $this->assertInternalType(
            'array',
            $this->_getRest()->fetchLatestPackageReleases('Horde_Core')
        );
    }

    public function testPackageLatestReleasesResponse()
    {
        $result = $this->_getRest()->fetchLatestPackageReleases('Horde_Core');
        $this->assertEquals(
            array('stable', 'alpha', 'beta', 'devel'),
            array_keys($result)
        );
    }

    public function testFetchReleaseInformation()
    {
        $this->assertInternalType(
            'resource',
            $this->_getRest()->fetchReleaseInformation('Horde_Core', '1.0.0')
        );
    }

    public function testReleaseInformationResponse()
    {
        $response = $this->_getRest()->fetchReleaseInformation('Horde_Core', '1.0.0');
        rewind($response);
        $this->assertContains(
            'Core Horde Framework library',
            stream_get_contents($response)
        );
    }

    public function testFetchReleasePackageXml()
    {
        $this->assertInternalType(
            'resource',
            $this->_getRest()->fetchReleasePackageXml('Horde_Core', '1.0.0')
        );
    }

    public function testReleasePackageXmlResponse()
    {
        $response = $this->_getRest()->fetchReleasePackageXml('Horde_Core', '1.0.0');
        rewind($response);
        $this->assertContains(
            'Horde Core Framework libraries',
            stream_get_contents($response)
        );
    }

    public function testPackageDependencies()
    {
        $response = $this->_getRest()->fetchPackageDependencies('Horde_Translation', '1.0.0');
        $this->assertContains(
            'Horde_Exception',
            $response
        );
    }

    private function _getRest()
    {
        return new Horde_Pear_Rest(
            new Horde_Http_Client(),
            $this->_server
        );
    }
}
