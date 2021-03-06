<?php

namespace libphonenumber\Tests;

use libphonenumber\CountryCodeToRegionCodeMapForTesting;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\RegionCode;
use libphonenumber\ShortNumberCost;
use libphonenumber\ShortNumberUtil;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/PhoneNumberUtilTest.php';


class ShortNumberUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShortNumberUtil
     */
    private $shortUtil;

    public function setUp()
    {
        PhoneNumberUtil::resetInstance();
        $this->shortUtil = new ShortNumberUtil(PhoneNumberUtil::getInstance(
            PhoneNumberUtilTest::TEST_META_DATA_FILE_PREFIX,
            CountryCodeToRegionCodeMapForTesting::$countryCodeToRegionCodeMapForTesting
        ));
    }

    public function testGetExampleShortNumber()
    {
        $this->assertEquals("8711", $this->shortUtil->getExampleShortNumber(RegionCode::AM));
        $this->assertEquals("1010", $this->shortUtil->getExampleShortNumber(RegionCode::FR));
        $this->assertEquals("", $this->shortUtil->getExampleShortNumber(RegionCode::UN001));
        $this->assertEquals("", $this->shortUtil->getExampleShortNumber(null));
    }

    public function testGetExampleShortNumberForCost()
    {
        $this->assertEquals(
            "3010",
            $this->shortUtil->getExampleShortNumberForCost(RegionCode::FR, ShortNumberCost::TOLL_FREE)
        );
        $this->assertEquals(
            "118777",
            $this->shortUtil->getExampleShortNumberForCost(RegionCode::FR, ShortNumberCost::STANDARD_RATE)
        );
        $this->assertEquals(
            "3200",
            $this->shortUtil->getExampleShortNumberForCost(RegionCode::FR, ShortNumberCost::PREMIUM_RATE)
        );
        $this->assertEquals(
            "",
            $this->shortUtil->getExampleShortNumberForCost(RegionCode::FR, ShortNumberCost::UNKNOWN_COST)
        );
    }

    public function testConnectsToEmergencyNumber_US()
    {
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("911", RegionCode::US));
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("119", RegionCode::US));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("999", RegionCode::US));
    }

    public function testConnectsToEmergencyNumberLongNumber_US()
    {
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("9116666666", RegionCode::US));
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("1196666666", RegionCode::US));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("9996666666", RegionCode::US));
    }

    public function testConnectsToEmergencyNumberWithFormatting_US()
    {
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("9-1-1", RegionCode::US));
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("1-1-9", RegionCode::US));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("9-9-9", RegionCode::US));
    }

    public function testConnectsToEmergencyNumberWithPlusSign_US()
    {
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("+911", RegionCode::US));
        $this->assertFalse(
            $this->shortUtil->connectsToEmergencyNumber("\uFF0B911", RegionCode::US)
        ); // @todo Fix string
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber(" +911", RegionCode::US));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("+119", RegionCode::US));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("+999", RegionCode::US));
    }

    public function testConnectsToEmergencyNumber_BR()
    {
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("911", RegionCode::BR));
        $this->assertTrue($this->shortUtil->connectsToEmergencyNumber("190", RegionCode::BR));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("999", RegionCode::BR));
    }

    public function testConnectsToEmergencyNumberLongNumber_BR()
    {
        // Brazilian emergency numbers don't work when additional digits are appended.
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("9111", RegionCode::BR));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("1900", RegionCode::BR));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("9996", RegionCode::BR));
    }

    public function testConnectsToEmergencyNumber_AO()
    {
        // Angola doesn't have any metadata for emergency numbers in the test metadata.
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("911", RegionCode::AO));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("222123456", RegionCode::BR));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("923123456", RegionCode::BR));
    }

    public function testConnectsToEmergencyNumber_ZW()
    {
        // Zimbabwe doesn't have any metadata in the test metadata.
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("911", RegionCode::ZW));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("01312345", RegionCode::ZW));
        $this->assertFalse($this->shortUtil->connectsToEmergencyNumber("0711234567", RegionCode::ZW));
    }

    public function testIsEmergencyNumber_US()
    {
        $this->assertTrue($this->shortUtil->isEmergencyNumber("911", RegionCode::US));
        $this->assertTrue($this->shortUtil->isEmergencyNumber("119", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("999", RegionCode::US));
    }

    public function testIsEmergencyNumberLongNumber_US()
    {
        $this->assertFalse($this->shortUtil->isEmergencyNumber("9116666666", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("1196666666", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("9996666666", RegionCode::US));
    }

    public function testIsEmergencyNumberWithFormatting_US()
    {
        $this->assertTrue($this->shortUtil->isEmergencyNumber("9-1-1", RegionCode::US));
        $this->assertTrue($this->shortUtil->isEmergencyNumber("*911", RegionCode::US));
        $this->assertTrue($this->shortUtil->isEmergencyNumber("1-1-9", RegionCode::US));
        $this->assertTrue($this->shortUtil->isEmergencyNumber("*119", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("9-9-9", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("*999", RegionCode::US));
    }

    public function testIsEmergencyNumberWithPlusSign_US()
    {
        $this->assertFalse($this->shortUtil->isEmergencyNumber("+911", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("\uFF0B911", RegionCode::US)); // @todo Fix string
        $this->assertFalse($this->shortUtil->isEmergencyNumber(" +911", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("+119", RegionCode::US));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("+999", RegionCode::US));
    }

    public function testIsEmergencyNumber_BR()
    {
        $this->assertTrue($this->shortUtil->isEmergencyNumber("911", RegionCode::BR));
        $this->assertTrue($this->shortUtil->isEmergencyNumber("190", RegionCode::BR));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("999", RegionCode::BR));
    }

    public function testIsEmergencyNumberLongNumber_BR()
    {
        $this->assertFalse($this->shortUtil->isEmergencyNumber("9111", RegionCode::BR));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("1900", RegionCode::BR));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("9996", RegionCode::BR));
    }

    public function testIsEmergencyNumber_AO()
    {
        // Angola doesn't have any metadata for emergency numbers in the test metadata.
        $this->assertFalse($this->shortUtil->isEmergencyNumber("911", RegionCode::AO));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("222123456", RegionCode::AO));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("923123456", RegionCode::AO));
    }

    public function testIsEmergencyNumber_ZW()
    {
        // Zimbabwe doesn't have any metadata in the test metadata.
        $this->assertFalse($this->shortUtil->isEmergencyNumber("911", RegionCode::ZW));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("01312345", RegionCode::ZW));
        $this->assertFalse($this->shortUtil->isEmergencyNumber("0711234567", RegionCode::ZW));
    }
}

/* EOF */