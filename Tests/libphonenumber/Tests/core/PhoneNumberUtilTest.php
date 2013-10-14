<?php

namespace libphonenumber\Tests\core;

use libphonenumber\CountryCodeToRegionCodeMapForTesting;
use libphonenumber\NumberFormat;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\RegionCode;


class PhoneNumberUtilTest extends \PHPUnit_Framework_TestCase
{

    const TEST_META_DATA_FILE_PREFIX = "../../../Tests/libphonenumber/Tests/core/data/PhoneNumberMetadataForTesting";
    private static $bsNumber = null;
    private static $internationalTollFree = null;
    private static $sgNumber = null;
    private static $usShortByOneNumber = null;
    private static $usTollFree = null;
    private static $usNumber = null;
    private static $usLocalNumber = null;
    private static $usLongNumber = null;
    private static $nzNumber = null;
    private static $usPremium = null;
    private static $usSpoof = null;
    private static $usSpoofWithRawInput = null;
    private static $gbMobile = null;
    private static $bsMobile = null;
    private static $gbNumber = null;
    private static $deShortNumber = null;
    private static $itMobile = null;
    private static $itNumber = null;
    private static $auNumber = null;
    private static $arMobile = null;
    private static $arNumber = null;
    private static $mxMobile1 = null;
    private static $mxNumber1 = null;
    private static $mxMobile2 = null;
    private static $mxNumber2 = null;
    private static $deNumber = null;
    private static $jpStarNumber = null;
    /**
     * @var PhoneNumberUtil
     */
    protected $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = self::initializePhoneUtilForTesting();
    }

    private static function initializePhoneUtilForTesting()
    {
        self::$bsNumber = new PhoneNumber();
        self::$bsNumber->setCountryCode(1)->setNationalNumber(2423651234);
        self::$bsMobile = new PhoneNumber();
        self::$bsMobile->setCountryCode(1)->setNationalNumber(2423591234);
        self::$internationalTollFree = new PhoneNumber();
        self::$internationalTollFree->setCountryCode(800)->setNationalNumber(12345678);
        self::$sgNumber = new PhoneNumber();
        self::$sgNumber->setCountryCode(65)->setNationalNumber(65218000);
        // A too-long and hence invalid US number.
        self::$usLongNumber = new PhoneNumber();
        self::$usLongNumber->setCountryCode(1)->setNationalNumber(65025300001);
        self::$usShortByOneNumber = new PhoneNumber();
        self::$usShortByOneNumber->setCountryCode(1)->setNationalNumber(650253000);
        self::$usTollFree = new PhoneNumber();
        self::$usTollFree->setCountryCode(1)->setNationalNumber(8002530000);
        self::$usNumber = new PhoneNumber();
        self::$usNumber->setCountryCode(1)->setNationalNumber(6502530000);
        self::$usLocalNumber = new PhoneNumber();
        self::$usLocalNumber->setCountryCode(1)->setNationalNumber(2530000);
        self::$nzNumber = new PhoneNumber();
        self::$nzNumber->setCountryCode(64)->setNationalNumber(33316005);
        self::$usPremium = new PhoneNumber();
        self::$usPremium->setCountryCode(1)->setNationalNumber(9002530000);
        self::$usSpoof = new PhoneNumber();
        self::$usSpoof->setCountryCode(1)->setNationalNumber(0);
        self::$usSpoofWithRawInput = new PhoneNumber();
        self::$usSpoofWithRawInput->setCountryCode(1)->setNationalNumber(0)->setRawInput("000-000-0000");
        self::$gbMobile = new PhoneNumber();
        self::$gbMobile->setCountryCode(44)->setNationalNumber(7912345678);
        self::$gbNumber = new PhoneNumber();
        self::$gbNumber->setCountryCode(44)->setNationalNumber(2070313000);
        self::$deShortNumber = new PhoneNumber();
        self::$deShortNumber->setCountryCode(49)->setNationalNumber(1234);
        self::$itMobile = new PhoneNumber();
        self::$itMobile->setCountryCode(39)->setNationalNumber(345678901);
        self::$itNumber = new PhoneNumber();
        self::$itNumber->setCountryCode(39)->setNationalNumber(236618300)->setItalianLeadingZero(true);
        self::$auNumber = new PhoneNumber();
        self::$auNumber->setCountryCode(61)->setNationalNumber(236618300);
        self::$arMobile = new PhoneNumber();
        self::$arMobile->setCountryCode(54)->setNationalNumber(91187654321);
        self::$arNumber = new PhoneNumber();
        self::$arNumber->setCountryCode(54)->setNationalNumber(1187654321);

        self::$mxMobile1 = new PhoneNumber();
        self::$mxMobile1->setCountryCode(52)->setNationalNumber(12345678900);
        self::$mxNumber1 = new PhoneNumber();
        self::$mxNumber1->setCountryCode(52)->setNationalNumber(3312345678);
        self::$mxMobile2 = new PhoneNumber();
        self::$mxMobile2->setCountryCode(52)->setNationalNumber(15512345678);
        self::$mxNumber2 = new PhoneNumber();
        self::$mxNumber2->setCountryCode(52)->setNationalNumber(8211234567);
        // Note that this is the same as the example number for DE in the metadata.
        self::$deNumber = new PhoneNumber();
        self::$deNumber->setCountryCode(49)->setNationalNumber(30123456);
        self::$jpStarNumber = new PhoneNumber();
        self::$jpStarNumber->setCountryCode(81)->setNationalNumber(2345);

        PhoneNumberUtil::resetInstance();
        return PhoneNumberUtil::getInstance(
            self::TEST_META_DATA_FILE_PREFIX,
            CountryCodeToRegionCodeMapForTesting::$countryCodeToRegionCodeMapForTesting
        );
    }

    public function testGetSupportedRegions()
    {
        $this->assertGreaterThan(0, count($this->phoneUtil->getSupportedRegions()));
    }

    public function testGetInstanceLoadUSMetadata()
    {
        $metadata = $this->phoneUtil->getMetadataForRegion(RegionCode::US);
        $this->assertEquals("US", $metadata->getId());
        $this->assertEquals(1, $metadata->getCountryCode());
        $this->assertEquals("011", $metadata->getInternationalPrefix());
        $this->assertTrue($metadata->hasNationalPrefix());
        $this->assertEquals(2, $metadata->numberFormatSize());
        $this->assertEquals("(\\d{3})(\\d{3})(\\d{4})", $metadata->getNumberFormat(1)->getPattern());
        $this->assertEquals("$1 $2 $3", $metadata->getNumberFormat(1)->getFormat());
        $this->assertEquals("[13-689]\\d{9}|2[0-35-9]\\d{8}", $metadata->getGeneralDesc()->getNationalNumberPattern());
        $this->assertEquals("\\d{7}(?:\\d{3})?", $metadata->getGeneralDesc()->getPossibleNumberPattern());
        $this->assertTrue($metadata->getGeneralDesc()->exactlySameAs($metadata->getFixedLine()));
        $this->assertEquals("\\d{10}", $metadata->getTollFree()->getPossibleNumberPattern());
        $this->assertEquals("900\\d{7}", $metadata->getPremiumRate()->getNationalNumberPattern());
        // No shared-cost data is available, so it should be initialised to "NA".
        $this->assertEquals("NA", $metadata->getSharedCost()->getNationalNumberPattern());
        $this->assertEquals("NA", $metadata->getSharedCost()->getPossibleNumberPattern());
    }

    public function testGetInstanceLoadDEMetadata()
    {
        $metadata = $this->phoneUtil->getMetadataForRegion(RegionCode::DE);
        $this->assertEquals("DE", $metadata->getId());
        $this->assertEquals(49, $metadata->getCountryCode());
        $this->assertEquals("00", $metadata->getInternationalPrefix());
        $this->assertEquals("0", $metadata->getNationalPrefix());
        $this->assertEquals(6, $metadata->numberFormatSize());
        $this->assertEquals(1, $metadata->getNumberFormat(5)->leadingDigitsPatternSize());
        $this->assertEquals("900", $metadata->getNumberFormat(5)->getLeadingDigitsPattern(0));
        $this->assertEquals("(\\d{3})(\\d{3,4})(\\d{4})", $metadata->getNumberFormat(5)->getPattern());
        $this->assertEquals("$1 $2 $3", $metadata->getNumberFormat(5)->getFormat());
        $this->assertEquals(
            "(?:[24-6]\\d{2}|3[03-9]\\d|[789](?:[1-9]\\d|0[2-9]))\\d{1,8}",
            $metadata->getFixedLine()->getNationalNumberPattern()
        );
        $this->assertEquals("\\d{2,14}", $metadata->getFixedLine()->getPossibleNumberPattern());
        $this->assertEquals("30123456", $metadata->getFixedLine()->getExampleNumber());
        $this->assertEquals("\\d{10}", $metadata->getTollFree()->getPossibleNumberPattern());
        $this->assertEquals("900([135]\\d{6}|9\\d{7})", $metadata->getPremiumRate()->getNationalNumberPattern());
    }

    public function testGetInstanceLoadARMetadata()
    {
        $metadata = $this->phoneUtil->getMetadataForRegion(RegionCode::AR);
        $this->assertEquals("AR", $metadata->getId());
        $this->assertEquals(54, $metadata->getCountryCode());
        $this->assertEquals("00", $metadata->getInternationalPrefix());
        $this->assertEquals("0", $metadata->getNationalPrefix());
        $this->assertEquals("0(?:(11|343|3715)15)?", $metadata->getNationalPrefixForParsing());
        $this->assertEquals("9$1", $metadata->getNationalPrefixTransformRule());
        $this->assertEquals("$2 15 $3-$4", $metadata->getNumberFormat(2)->getFormat());
        $this->assertEquals("(9)(\\d{4})(\\d{2})(\\d{4})", $metadata->getNumberFormat(3)->getPattern());
        $this->assertEquals("(9)(\\d{4})(\\d{2})(\\d{4})", $metadata->getIntlNumberFormat(3)->getPattern());
        $this->assertEquals("$1 $2 $3 $4", $metadata->getIntlNumberFormat(3)->getFormat());
    }

    public function testGetInstanceLoadInternationalTollFreeMetadata()
    {
        $metadata = $this->phoneUtil->getMetadataForNonGeographicalRegion(800);
        $this->assertEquals("001", $metadata->getId());
        $this->assertEquals(800, $metadata->getCountryCode());
        $this->assertEquals("$1 $2", $metadata->getNumberFormat(0)->getFormat());
        $this->assertEquals("(\\d{4})(\\d{4})", $metadata->getNumberFormat(0)->getPattern());
        $this->assertEquals("12345678", $metadata->getGeneralDesc()->getExampleNumber());
        $this->assertEquals("12345678", $metadata->getTollFree()->getExampleNumber());
    }

    public function testIsLeadingZeroPossible()
    {
        $this->assertTrue($this->phoneUtil->isLeadingZeroPossible(39)); // Italy
        $this->assertFalse($this->phoneUtil->isLeadingZeroPossible(1)); // USA
        $this->assertTrue($this->phoneUtil->isLeadingZeroPossible(800)); // International toll free numbers
        $this->assertFalse(
            $this->phoneUtil->isLeadingZeroPossible(888)
        ); // Not in metadata file, just default to false.
    }

    public function testGetLengthOfGeographicalAreaCode()
    {
        // Google MTV, which has area code "650".
        $this->assertEquals(3, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$usNumber));

        // A North America toll-free number, which has no area code.
        $this->assertEquals(0, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$usTollFree));

        // Google London, which has area code "20".
        $this->assertEquals(2, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$gbNumber));

        // A UK mobile phone, which has no area code.
        $this->assertEquals(0, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$gbMobile));

        // Google Buenos Aires, which has area code "11".
        $this->assertEquals(2, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$arNumber));

        // Google Sydney, which has area code "2".
        $this->assertEquals(1, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$auNumber));

        // Google Singapore. Singapore has no area code and no national prefix.
        $this->assertEquals(0, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$sgNumber));

        // An invalid US number (1 digit shorter), which has no area code.
        $this->assertEquals(0, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$usShortByOneNumber));

        // An international toll free number, which has no area code.
        $this->assertEquals(0, $this->phoneUtil->getLengthOfGeographicalAreaCode(self::$internationalTollFree));
    }

    public function testGetLengthOfNationalDestinationCode()
    {
        // Google MTV, which has national destination code (NDC) "650".
        $this->assertEquals(3, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$usNumber));

        // A North America toll-free number, which has NDC "800".
        $this->assertEquals(3, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$usTollFree));

        // Google London, which has NDC "20".
        $this->assertEquals(2, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$gbNumber));

        // A UK mobile phone, which has NDC "7912".
        $this->assertEquals(4, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$gbMobile));

        // Google Buenos Aires, which has NDC "11".
        $this->assertEquals(2, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$arNumber));

        // An Argentinian mobile which has NDC "911".
        $this->assertEquals(3, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$arMobile));

        // Google Sydney, which has NDC "2".
        $this->assertEquals(1, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$auNumber));

        // Google Singapore, which has NDC "6521".
        $this->assertEquals(4, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$sgNumber));

        // An invalid US number (1 digit shorter), which has no NDC.
        $this->assertEquals(0, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$usShortByOneNumber));

        // A number containing an invalid country calling code, which shouldn't have any NDC.
        $number = new PhoneNumber();
        $number->setCountryCode(123)->setNationalNumber(6502530000);
        $this->assertEquals(0, $this->phoneUtil->getLengthOfNationalDestinationCode($number));

        // An international toll free number, which has NDC "1234".
        $this->assertEquals(4, $this->phoneUtil->getLengthOfNationalDestinationCode(self::$internationalTollFree));
    }

    public function testGetCountryMobileToken()
    {
        $this->assertEquals("1", $this->phoneUtil->getCountryMobileToken($this->phoneUtil->getCountryCodeForRegion(RegionCode::MX)));

        // Country calling code for Sweden, which has no mobile token.
        $this->assertEquals("", $this->phoneUtil->getCountryMobileToken($this->phoneUtil->getCountryCodeForRegion(RegionCode::SE)));
    }

    public function testGetNationalSignificantNumber()
    {
        $this->assertEquals("6502530000", $this->phoneUtil->getNationalSignificantNumber(self::$usNumber));

        // An Italian mobile number.
        $this->assertEquals("345678901", $this->phoneUtil->getNationalSignificantNumber(self::$itMobile));

        // An Italian fixed line number.
        $this->assertEquals("0236618300", $this->phoneUtil->getNationalSignificantNumber(self::$itNumber));

        $this->assertEquals("12345678", $this->phoneUtil->getNationalSignificantNumber(self::$internationalTollFree));
    }

    public function testGetExampleNumber()
    {
        $this->assertEquals(self::$deNumber, $this->phoneUtil->getExampleNumber(RegionCode::DE));

        $this->assertEquals(
            self::$deNumber,
            $this->phoneUtil->getExampleNumberForType(RegionCode::DE, PhoneNumberType::FIXED_LINE)
        );
        $this->assertEquals(null, $this->phoneUtil->getExampleNumberForType(RegionCode::DE, PhoneNumberType::MOBILE));
        // For the US, the example number is placed under general description, and hence should be used
        // for both fixed line and mobile, so neither of these should return null.
        $this->assertNotNull($this->phoneUtil->getExampleNumberForType(RegionCode::US, PhoneNumberType::FIXED_LINE));
        $this->assertNotNull($this->phoneUtil->getExampleNumberForType(RegionCode::US, PhoneNumberType::MOBILE));
        // CS is an invalid region, so we have no data for it.
        $this->assertNull($this->phoneUtil->getExampleNumberForType(RegionCode::CS, PhoneNumberType::MOBILE));
        // RegionCode 001 is reserved for supporting non-geographical country calling code. We don't
        // support getting an example number for it with this method.
        $this->assertEquals(null, $this->phoneUtil->getExampleNumber(RegionCode::UN001));
    }

    public function testGetExampleNumberForNonGeoEntity()
    {
        $this->assertEquals(self::$internationalTollFree, $this->phoneUtil->getExampleNumberForNonGeoEntity(800));
    }

    public function testConvertAlphaCharactersInNumber()
    {
        $input = "1800-ABC-DEF";
        // Alpha chars are converted to digits; everything else is left untouched.
        $expectedOutput = "1800-222-333";
        $this->assertEquals($expectedOutput, $this->phoneUtil->convertAlphaCharactersInNumber($input));
    }

    public function  testNormaliseRemovePunctuation()
    {
        $inputNumber = "034-56&+#234";
        $expectedOutput = "03456234";
        $this->assertEquals(
            $expectedOutput,
            $this->phoneUtil->normalize($inputNumber),
            "Conversion did not correctly remove punctuation"
        );
    }

    public function testNormaliseReplaceAlphaCharacters()
    {
        $inputNumber = "034-I-am-HUNGRY";
        $expectedOutput = "034426486479";
        $this->assertEquals(
            $expectedOutput,
            $this->phoneUtil->normalize($inputNumber),
            "Conversion did not correctly replace alpha characters"
        );
    }

    public function testNormaliseOtherDigits()
    {
        $this->markTestSkipped('PHP UTF-8 toUpper incompatible');
        $inputNumber = "\xEF\xBC\x92" . "5\xD9\xA5" /* "２5٥" */
        ;
        $expectedOutput = "255";
        $this->assertEquals(
            $expectedOutput,
            $this->phoneUtil->normalize($inputNumber),
            "Conversion did not correctly replace non-latin digits"
        );
        // Eastern-Arabic digits.
        $inputNumber = "\xDB\xB5" . "2\xDB\xB0" /* "۵2۰" */
        ;
        $expectedOutput = "520";
        $this->assertEquals(
            $expectedOutput,
            $this->phoneUtil->normalize($inputNumber),
            "Conversion did not correctly replace non-latin digits"
        );
    }

    public function testNormaliseStripAlphaCharacters()
    {
        $inputNumber = "034-56&+a#234";
        $expectedOutput = "03456234";
        $this->assertEquals(
            $expectedOutput,
            $this->phoneUtil->normalizeDigitsOnly($inputNumber),
            "Conversion did not correctly remove alpha character"
        );
    }

    public function testNormaliseStripNonDiallableCharacters()
    {
        $inputNumber = "03*4-56&+a#234";
        $expectedOutput = "03*456+234";
        $this->assertEquals(
            $expectedOutput,
            $this->phoneUtil->normalizeDiallableCharsOnly($inputNumber),
            "Conversion did not correctly remove non-diallable characters"
        );
    }

    public function testFormatUSNumber()
    {
        $this->assertEquals("650 253 0000", $this->phoneUtil->format(self::$usNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+1 650 253 0000",
            $this->phoneUtil->format(self::$usNumber, PhoneNumberFormat::INTERNATIONAL)
        );

        $this->assertEquals("800 253 0000", $this->phoneUtil->format(self::$usTollFree, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+1 800 253 0000",
            $this->phoneUtil->format(self::$usTollFree, PhoneNumberFormat::INTERNATIONAL)
        );

        $this->assertEquals("900 253 0000", $this->phoneUtil->format(self::$usPremium, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+1 900 253 0000",
            $this->phoneUtil->format(self::$usPremium, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals(
            "tel:+1-900-253-0000",
            $this->phoneUtil->format(self::$usPremium, PhoneNumberFormat::RFC3966)
        );
        // Numbers with all zeros in the national number part will be formatted by using the raw_input
        // if that is available no matter which format is specified.
        $this->assertEquals(
            "000-000-0000",
            $this->phoneUtil->format(self::$usSpoofWithRawInput, PhoneNumberFormat::NATIONAL)
        );
        $this->assertEquals("0", $this->phoneUtil->format(self::$usSpoof, PhoneNumberFormat::NATIONAL));
    }

    public function testFormatBSNumber()
    {
        $this->assertEquals("242 365 1234", $this->phoneUtil->format(self::$bsNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+1 242 365 1234",
            $this->phoneUtil->format(self::$bsNumber, PhoneNumberFormat::INTERNATIONAL)
        );
    }

    public function testFormatGBNumber()
    {
        $this->assertEquals("(020) 7031 3000", $this->phoneUtil->format(self::$gbNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+44 20 7031 3000",
            $this->phoneUtil->format(self::$gbNumber, PhoneNumberFormat::INTERNATIONAL)
        );

        $this->assertEquals("(07912) 345 678", $this->phoneUtil->format(self::$gbMobile, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+44 7912 345 678",
            $this->phoneUtil->format(self::$gbMobile, PhoneNumberFormat::INTERNATIONAL)
        );
    }

    public function testFormatDENumber()
    {
        $deNumber = new PhoneNumber();
        $deNumber->setCountryCode(49)->setNationalNumber(301234);
        $this->assertEquals("030/1234", $this->phoneUtil->format($deNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals("+49 30/1234", $this->phoneUtil->format($deNumber, PhoneNumberFormat::INTERNATIONAL));
        $this->assertEquals("tel:+49-30-1234", $this->phoneUtil->format($deNumber, PhoneNumberFormat::RFC3966));

        $deNumber->clear();
        $deNumber->setCountryCode(49)->setNationalNumber(291123);
        $this->assertEquals("0291 123", $this->phoneUtil->format($deNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals("+49 291 123", $this->phoneUtil->format($deNumber, PhoneNumberFormat::INTERNATIONAL));

        $deNumber->clear();
        $deNumber->setCountryCode(49)->setNationalNumber(29112345678);
        $this->assertEquals("0291 12345678", $this->phoneUtil->format($deNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals("+49 291 12345678", $this->phoneUtil->format($deNumber, PhoneNumberFormat::INTERNATIONAL));

        $deNumber->clear();
        $deNumber->setCountryCode(49)->setNationalNumber(912312345);
        $this->assertEquals("09123 12345", $this->phoneUtil->format($deNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals("+49 9123 12345", $this->phoneUtil->format($deNumber, PhoneNumberFormat::INTERNATIONAL));
        $deNumber->clear();
        $deNumber->setCountryCode(49)->setNationalNumber(80212345);
        $this->assertEquals("08021 2345", $this->phoneUtil->format($deNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals("+49 8021 2345", $this->phoneUtil->format($deNumber, PhoneNumberFormat::INTERNATIONAL));
        // Note this number is correctly formatted without national prefix. Most of the numbers that
        // are treated as invalid numbers by the library are short numbers, and they are usually not
        // dialed with national prefix.
        $this->assertEquals("1234", $this->phoneUtil->format(self::$deShortNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+49 1234",
            $this->phoneUtil->format(self::$deShortNumber, PhoneNumberFormat::INTERNATIONAL)
        );

        $deNumber->clear();
        $deNumber->setCountryCode(49)->setNationalNumber(41341234);
        $this->assertEquals("04134 1234", $this->phoneUtil->format($deNumber, PhoneNumberFormat::NATIONAL));
    }

    public function testFormatITNumber()
    {
        $this->assertEquals("02 3661 8300", $this->phoneUtil->format(self::$itNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+39 02 3661 8300",
            $this->phoneUtil->format(self::$itNumber, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+390236618300", $this->phoneUtil->format(self::$itNumber, PhoneNumberFormat::E164));

        $this->assertEquals("345 678 901", $this->phoneUtil->format(self::$itMobile, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+39 345 678 901",
            $this->phoneUtil->format(self::$itMobile, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+39345678901", $this->phoneUtil->format(self::$itMobile, PhoneNumberFormat::E164));
    }

    public function testFormatAUNumber()
    {
        $this->assertEquals("02 3661 8300", $this->phoneUtil->format(self::$auNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+61 2 3661 8300",
            $this->phoneUtil->format(self::$auNumber, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+61236618300", $this->phoneUtil->format(self::$auNumber, PhoneNumberFormat::E164));

        $auNumber = new PhoneNumber();
        $auNumber->setCountryCode(61)->setNationalNumber(1800123456);
        $this->assertEquals("1800 123 456", $this->phoneUtil->format($auNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals("+61 1800 123 456", $this->phoneUtil->format($auNumber, PhoneNumberFormat::INTERNATIONAL));
        $this->assertEquals("+611800123456", $this->phoneUtil->format($auNumber, PhoneNumberFormat::E164));
    }

    public function testFormatARNumber()
    {
        $this->assertEquals("011 8765-4321", $this->phoneUtil->format(self::$arNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+54 11 8765-4321",
            $this->phoneUtil->format(self::$arNumber, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+541187654321", $this->phoneUtil->format(self::$arNumber, PhoneNumberFormat::E164));

        $this->assertEquals("011 15 8765-4321", $this->phoneUtil->format(self::$arMobile, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+54 9 11 8765 4321",
            $this->phoneUtil->format(self::$arMobile, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+5491187654321", $this->phoneUtil->format(self::$arMobile, PhoneNumberFormat::E164));
    }

    public function testFormatMXNumber()
    {
        $this->assertEquals(
            "045 234 567 8900",
            $this->phoneUtil->format(self::$mxMobile1, PhoneNumberFormat::NATIONAL)
        );
        $this->assertEquals(
            "+52 1 234 567 8900",
            $this->phoneUtil->format(self::$mxMobile1, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+5212345678900", $this->phoneUtil->format(self::$mxMobile1, PhoneNumberFormat::E164));

        $this->assertEquals(
            "045 55 1234 5678",
            $this->phoneUtil->format(self::$mxMobile2, PhoneNumberFormat::NATIONAL)
        );
        $this->assertEquals(
            "+52 1 55 1234 5678",
            $this->phoneUtil->format(self::$mxMobile2, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+5215512345678", $this->phoneUtil->format(self::$mxMobile2, PhoneNumberFormat::E164));

        $this->assertEquals("01 33 1234 5678", $this->phoneUtil->format(self::$mxNumber1, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+52 33 1234 5678",
            $this->phoneUtil->format(self::$mxNumber1, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+523312345678", $this->phoneUtil->format(self::$mxNumber1, PhoneNumberFormat::E164));

        $this->assertEquals("01 821 123 4567", $this->phoneUtil->format(self::$mxNumber2, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "+52 821 123 4567",
            $this->phoneUtil->format(self::$mxNumber2, PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertEquals("+528211234567", $this->phoneUtil->format(self::$mxNumber2, PhoneNumberFormat::E164));
    }

    public function testFormatOutOfCountryCallingNumber()
    {
        $this->assertEquals(
            "00 1 900 253 0000",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$usPremium, RegionCode::DE)
        );
        $this->assertEquals(
            "1 650 253 0000",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$usNumber, RegionCode::BS)
        );

        $this->assertEquals(
            "00 1 650 253 0000",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$usNumber, RegionCode::PL)
        );

        $this->assertEquals(
            "011 44 7912 345 678",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$gbMobile, RegionCode::US)
        );

        $this->assertEquals(
            "00 49 1234",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$deShortNumber, RegionCode::GB)
        );
        // Note this number is correctly formatted without national prefix. Most of the numbers that
        // are treated as invalid numbers by the library are short numbers, and they are usually not
        // dialed with national prefix.
        $this->assertEquals(
            "1234",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$deShortNumber, RegionCode::DE)
        );

        $this->assertEquals(
            "011 39 02 3661 8300",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$itNumber, RegionCode::US)
        );
        $this->assertEquals(
            "02 3661 8300",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$itNumber, RegionCode::IT)
        );
        $this->assertEquals(
            "+39 02 3661 8300",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$itNumber, RegionCode::SG)
        );

        $this->assertEquals(
            "6521 8000",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$sgNumber, RegionCode::SG)
        );

        $this->assertEquals(
            "011 54 9 11 8765 4321",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$arMobile, RegionCode::US)
        );
        $this->assertEquals(
            "011 800 1234 5678",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$internationalTollFree, RegionCode::US)
        );

        $arNumberWithExtn = new PhoneNumber();
        $arNumberWithExtn->mergeFrom(self::$arMobile)->setExtension("1234");
        $this->assertEquals(
            "011 54 9 11 8765 4321 ext. 1234",
            $this->phoneUtil->formatOutOfCountryCallingNumber($arNumberWithExtn, RegionCode::US)
        );
        $this->assertEquals(
            "0011 54 9 11 8765 4321 ext. 1234",
            $this->phoneUtil->formatOutOfCountryCallingNumber($arNumberWithExtn, RegionCode::AU)
        );
        $this->assertEquals(
            "011 15 8765-4321 ext. 1234",
            $this->phoneUtil->formatOutOfCountryCallingNumber($arNumberWithExtn, RegionCode::AR)
        );
    }

    public function testFormatOutOfCountryWithInvalidRegion()
    {
        // AQ/Antarctica isn't a valid region code for phone number formatting,
        // so this falls back to intl formatting.
        $this->assertEquals(
            "+1 650 253 0000",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$usNumber, RegionCode::AQ)
        );
        // For region code 001, the out-of-country format always turns into the international format.
        $this->assertEquals(
            "+1 650 253 0000",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$usNumber, RegionCode::UN001)
        );
    }

    public function testFormatOutOfCountryWithPreferredIntlPrefix()
    {
        // This should use 0011, since that is the preferred international prefix (both 0011 and 0012
        // are accepted as possible international prefixes in our test metadta.)
        $this->assertEquals(
            "0011 39 02 3661 8300",
            $this->phoneUtil->formatOutOfCountryCallingNumber(self::$itNumber, RegionCode::AU)
        );
    }

    public function testFormatOutOfCountryKeepingAlphaChars()
    {
        $alphaNumericNumber = new PhoneNumber();
        $alphaNumericNumber->setCountryCode(1)->setNationalNumber(8007493524)->setRawInput("1800 six-flag");
        $this->assertEquals(
            "0011 1 800 SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        $alphaNumericNumber->setRawInput("1-800-SIX-flag");
        $this->assertEquals(
            "0011 1 800-SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        $alphaNumericNumber->setRawInput("Call us from UK: 00 1 800 SIX-flag");
        $this->assertEquals(
            "0011 1 800 SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        $alphaNumericNumber->setRawInput("800 SIX-flag");
        $this->assertEquals(
            "0011 1 800 SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        // Formatting from within the NANPA region.
        $this->assertEquals(
            "1 800 SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::US)
        );

        $this->assertEquals(
            "1 800 SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::BS)
        );

        // Testing that if the raw input doesn't exist, it is formatted using
        // formatOutOfCountryCallingNumber.
        $alphaNumericNumber->clearRawInput();
        $this->assertEquals(
            "00 1 800 749 3524",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::DE)
        );

        // Testing AU alpha number formatted from Australia.
        $alphaNumericNumber->setCountryCode(61)->setNationalNumber(827493524)->setRawInput("+61 82749-FLAG");
        // This number should have the national prefix fixed.
        $this->assertEquals(
            "082749-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        $alphaNumericNumber->setRawInput("082749-FLAG");
        $this->assertEquals(
            "082749-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        $alphaNumericNumber->setNationalNumber(18007493524)->setRawInput("1-800-SIX-flag");
        // This number should not have the national prefix prefixed, in accordance with the override for
        // this specific formatting rule.
        $this->assertEquals(
            "1-800-SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AU)
        );

        // The metadata should not be permanently changed, since we copied it before modifying patterns.
        // Here we check this.
        $alphaNumericNumber->setNationalNumber(1800749352);
        $this->assertEquals(
            "1800 749 352",
            $this->phoneUtil->formatOutOfCountryCallingNumber($alphaNumericNumber, RegionCode::AU)
        );

        // Testing a region with multiple international prefixes.
        $this->assertEquals(
            "+61 1-800-SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::SG)
        );
        // Testing the case of calling from a non-supported region.
        $this->assertEquals(
            "+61 1-800-SIX-FLAG",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AQ)
        );

        // Testing the case with an invalid country calling code.
        $alphaNumericNumber->setCountryCode(0)->setNationalNumber(18007493524)->setRawInput("1-800-SIX-flag");
        // Uses the raw input only.
        $this->assertEquals(
            "1-800-SIX-flag",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::DE)
        );

        // Testing the case of an invalid alpha number.
        $alphaNumericNumber->setCountryCode(1)->setNationalNumber(80749)->setRawInput("180-SIX");
        // No country-code stripping can be done.
        $this->assertEquals(
            "00 1 180-SIX",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::DE)
        );

        // Testing the case of calling from a non-supported region.
        $alphaNumericNumber->setCountryCode(1)->setNationalNumber(80749)->setRawInput("180-SIX");
        // No country-code stripping can be done since the number is invalid.
        $this->assertEquals(
            "+1 180-SIX",
            $this->phoneUtil->formatOutOfCountryKeepingAlphaChars($alphaNumericNumber, RegionCode::AQ)
        );
    }

    public function testFormatWithCarrierCode()
    {
        // We only support this for AR in our test metadata, and only for mobile numbers starting with
        // certain values.
        $arMobile = new PhoneNumber();
        $arMobile->setCountryCode(54)->setNationalNumber(92234654321);
        $this->assertEquals("02234 65-4321", $this->phoneUtil->format($arMobile, PhoneNumberFormat::NATIONAL));
        // Here we force 14 as the carrier code.
        $this->assertEquals(
            "02234 14 65-4321",
            $this->phoneUtil->formatNationalNumberWithCarrierCode($arMobile, "14")
        );
        // Here we force the number to be shown with no carrier code.
        $this->assertEquals(
            "02234 65-4321",
            $this->phoneUtil->formatNationalNumberWithCarrierCode($arMobile, "")
        );
        // Here the international rule is used, so no carrier code should be present.
        $this->assertEquals("+5492234654321", $this->phoneUtil->format($arMobile, PhoneNumberFormat::E164));
        // We don't support this for the US so there should be no change.
        $this->assertEquals(
            "650 253 0000",
            $this->phoneUtil->formatNationalNumberWithCarrierCode(self::$usNumber, "15")
        );
    }

    public function testFormatWithPreferredCarrierCode()
    {
        // We only support this for AR in our test metadata.
        $arNumber = new PhoneNumber();
        $arNumber->setCountryCode(54)->setNationalNumber(91234125678);
        // Test formatting with no preferred carrier code stored in the number itself.
        $this->assertEquals(
            "01234 15 12-5678",
            $this->phoneUtil->formatNationalNumberWithPreferredCarrierCode($arNumber, "15")
        );
        $this->assertEquals(
            "01234 12-5678",
            $this->phoneUtil->formatNationalNumberWithPreferredCarrierCode($arNumber, "")
        );
        // Test formatting with preferred carrier code present.
        $arNumber->setPreferredDomesticCarrierCode("19");
        $this->assertEquals("01234 12-5678", $this->phoneUtil->format($arNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "01234 19 12-5678",
            $this->phoneUtil->formatNationalNumberWithPreferredCarrierCode($arNumber, "15")
        );
        $this->assertEquals(
            "01234 19 12-5678",
            $this->phoneUtil->formatNationalNumberWithPreferredCarrierCode($arNumber, "")
        );
        // When the preferred_domestic_carrier_code is present (even when it contains an empty string),
        // use it instead of the default carrier code passed in.
        $arNumber->setPreferredDomesticCarrierCode("");
        $this->assertEquals(
            "01234 12-5678",
            $this->phoneUtil->formatNationalNumberWithPreferredCarrierCode($arNumber, "15")
        );
        // We don't support this for the US so there should be no change.
        $usNumber = new PhoneNumber();
        $usNumber->setCountryCode(1)->setNationalNumber(4241231234)->setPreferredDomesticCarrierCode("99");
        $this->assertEquals("424 123 1234", $this->phoneUtil->format($usNumber, PhoneNumberFormat::NATIONAL));
        $this->assertEquals(
            "424 123 1234",
            $this->phoneUtil->formatNationalNumberWithPreferredCarrierCode($usNumber, "15")
        );
    }

    public function testFormatNumberForMobileDialing()
    {
        // US toll free numbers are marked as noInternationalDialling in the test metadata for testing
        // purposes.
        $this->assertEquals(
            "800 253 0000",
            $this->phoneUtil->formatNumberForMobileDialing(
                self::$usTollFree,
                RegionCode::US,
                true /*  keep formatting */
            )
        );
        $this->assertEquals(
            "",
            $this->phoneUtil->formatNumberForMobileDialing(self::$usTollFree, RegionCode::CN, true)
        );
        $this->assertEquals(
            "+1 650 253 0000",
            $this->phoneUtil->formatNumberForMobileDialing(self::$usNumber, RegionCode::US, true)
        );
        $usNumberWithExtn = new PhoneNumber();
        $usNumberWithExtn->mergeFrom(self::$usNumber)->setExtension("1234");
        $this->assertEquals(
            "+1 650 253 0000",
            $this->phoneUtil->formatNumberForMobileDialing($usNumberWithExtn, RegionCode::US, true)
        );

        $this->assertEquals(
            "8002530000",
            $this->phoneUtil->formatNumberForMobileDialing(
                self::$usTollFree,
                RegionCode::US,
                false /* remove formatting */
            )
        );
        $this->assertEquals(
            "",
            $this->phoneUtil->formatNumberForMobileDialing(self::$usTollFree, RegionCode::CN, false)
        );
        $this->assertEquals(
            "+16502530000",
            $this->phoneUtil->formatNumberForMobileDialing(self::$usNumber, RegionCode::US, false)
        );
        $this->assertEquals(
            "+16502530000",
            $this->phoneUtil->formatNumberForMobileDialing($usNumberWithExtn, RegionCode::US, false)
        );

        // An invalid US number, which is one digit too long.
        $this->assertEquals(
            "+165025300001",
            $this->phoneUtil->formatNumberForMobileDialing(self::$usLongNumber, RegionCode::US, false)
        );
        $this->assertEquals(
            "+1 65025300001",
            $this->phoneUtil->formatNumberForMobileDialing(self::$usLongNumber, RegionCode::US, true)
        );

        // Star numbers. In real life they appear in Israel, but we have them in JP in our test
        // metadata.
        $this->assertEquals(
            "*2345",
            $this->phoneUtil->formatNumberForMobileDialing(self::$jpStarNumber, RegionCode::JP, false)
        );
        $this->assertEquals(
            "*2345",
            $this->phoneUtil->formatNumberForMobileDialing(self::$jpStarNumber, RegionCode::JP, true)
        );

        $this->assertEquals(
            "+80012345678",
            $this->phoneUtil->formatNumberForMobileDialing(self::$internationalTollFree, RegionCode::JP, false)
        );
        $this->assertEquals(
            "+800 1234 5678",
            $this->phoneUtil->formatNumberForMobileDialing(self::$internationalTollFree, RegionCode::JP, true)
        );
    }

    public function testFormatByPattern()
    {
        $newNumFormat = new NumberFormat();
        $newNumFormat->setPattern("(\\d{3})(\\d{3})(\\d{4})");
        $newNumFormat->setFormat("($1) $2-$3");
        $newNumberFormats = array();
        $newNumberFormats[] = $newNumFormat;

        $this->assertEquals(
            "(650) 253-0000",
            $this->phoneUtil->formatByPattern(
                self::$usNumber,
                PhoneNumberFormat::NATIONAL,
                $newNumberFormats
            )
        );
        $this->assertEquals(
            "+1 (650) 253-0000",
            $this->phoneUtil->formatByPattern(
                self::$usNumber,
                PhoneNumberFormat::INTERNATIONAL,
                $newNumberFormats
            )
        );
        $this->assertEquals(
            "tel:+1-650-253-0000",
            $this->phoneUtil->formatByPattern(
                self::$usNumber,
                PhoneNumberFormat::RFC3966,
                $newNumberFormats
            )
        );

        // $NP is set to '1' for the US. Here we check that for other NANPA countries the US rules are
        // followed.
        $newNumFormat->setNationalPrefixFormattingRule('$NP ($FG)');
        $newNumFormat->setFormat("$1 $2-$3");
        $this->assertEquals(
            "1 (242) 365-1234",
            $this->phoneUtil->formatByPattern(
                self::$bsNumber,
                PhoneNumberFormat::NATIONAL,
                $newNumberFormats
            )
        );
        $this->assertEquals(
            "+1 242 365-1234",
            $this->phoneUtil->formatByPattern(
                self::$bsNumber,
                PhoneNumberFormat::INTERNATIONAL,
                $newNumberFormats
            )
        );

        $newNumFormat->setPattern("(\\d{2})(\\d{5})(\\d{3})");
        $newNumFormat->setFormat("$1-$2 $3");
        $newNumberFormats[0] = $newNumFormat;

        $this->assertEquals(
            "02-36618 300",
            $this->phoneUtil->formatByPattern(
                self::$itNumber,
                PhoneNumberFormat::NATIONAL,
                $newNumberFormats
            )
        );
        $this->assertEquals(
            "+39 02-36618 300",
            $this->phoneUtil->formatByPattern(
                self::$itNumber,
                PhoneNumberFormat::INTERNATIONAL,
                $newNumberFormats
            )
        );

        $newNumFormat->setNationalPrefixFormattingRule('$NP$FG');
        $newNumFormat->setPattern("(\\d{2})(\\d{4})(\\d{4})");
        $newNumFormat->setFormat("$1 $2 $3");
        $newNumberFormats[0] = $newNumFormat;
        $this->assertEquals(
            "020 7031 3000",
            $this->phoneUtil->formatByPattern(
                self::$gbNumber,
                PhoneNumberFormat::NATIONAL,
                $newNumberFormats
            )
        );

        $newNumFormat->setNationalPrefixFormattingRule('($NP$FG)');
        $this->assertEquals(
            "(020) 7031 3000",
            $this->phoneUtil->formatByPattern(
                self::$gbNumber,
                PhoneNumberFormat::NATIONAL,
                $newNumberFormats
            )
        );

        $newNumFormat->setNationalPrefixFormattingRule("");
        $this->assertEquals(
            "20 7031 3000",
            $this->phoneUtil->formatByPattern(
                self::$gbNumber,
                PhoneNumberFormat::NATIONAL,
                $newNumberFormats
            )
        );

        $this->assertEquals(
            "+44 20 7031 3000",
            $this->phoneUtil->formatByPattern(
                self::$gbNumber,
                PhoneNumberFormat::INTERNATIONAL,
                $newNumberFormats
            )
        );
    }

    public function testFormatE164Number()
    {
        $this->assertEquals("+16502530000", $this->phoneUtil->format(self::$usNumber, PhoneNumberFormat::E164));
        $this->assertEquals("+4930123456", $this->phoneUtil->format(self::$deNumber, PhoneNumberFormat::E164));
        $this->assertEquals(
            "+80012345678",
            $this->phoneUtil->format(self::$internationalTollFree, PhoneNumberFormat::E164)
        );
    }

    public function testFormatNumberWithExtension()
    {
        $nzNumber = new PhoneNumber();
        $nzNumber->mergeFrom(self::$nzNumber)->setExtension("1234");
        // Uses default extension prefix:
        $this->assertEquals("03-331 6005 ext. 1234", $this->phoneUtil->format($nzNumber, PhoneNumberFormat::NATIONAL));
        // Uses RFC 3966 syntax.
        $this->assertEquals(
            "tel:+64-3-331-6005;ext=1234",
            $this->phoneUtil->format($nzNumber, PhoneNumberFormat::RFC3966)
        );
        // Extension prefix overridden in the territory information for the US:
        $usNumberWithExtension = new PhoneNumber();
        $usNumberWithExtension->mergeFrom(self::$usNumber)->setExtension("4567");
        $this->assertEquals(
            "650 253 0000 extn. 4567",
            $this->phoneUtil->format($usNumberWithExtension, PhoneNumberFormat::NATIONAL)
        );
    }

    public function testFormatInOriginalFormat()
    {
        $number1 = $this->phoneUtil->parseAndKeepRawInput("+442087654321", RegionCode::GB);
        $this->assertEquals("+44 20 8765 4321", $this->phoneUtil->formatInOriginalFormat($number1, RegionCode::GB));

        $number2 = $this->phoneUtil->parseAndKeepRawInput("02087654321", RegionCode::GB);
        $this->assertEquals("(020) 8765 4321", $this->phoneUtil->formatInOriginalFormat($number2, RegionCode::GB));

        $number3 = $this->phoneUtil->parseAndKeepRawInput("011442087654321", RegionCode::US);
        $this->assertEquals("011 44 20 8765 4321", $this->phoneUtil->formatInOriginalFormat($number3, RegionCode::US));

        $number4 = $this->phoneUtil->parseAndKeepRawInput("442087654321", RegionCode::GB);
        $this->assertEquals("44 20 8765 4321", $this->phoneUtil->formatInOriginalFormat($number4, RegionCode::GB));

        $number5 = $this->phoneUtil->parse("+442087654321", RegionCode::GB);
        $this->assertEquals("(020) 8765 4321", $this->phoneUtil->formatInOriginalFormat($number5, RegionCode::GB));

        // Invalid numbers that we have a formatting pattern for should be formatted properly. Note area
        // codes starting with 7 are intentionally excluded in the test metadata for testing purposes.
        $number6 = $this->phoneUtil->parseAndKeepRawInput("7345678901", RegionCode::US);
        $this->assertEquals("734 567 8901", $this->phoneUtil->formatInOriginalFormat($number6, RegionCode::US));

        // US is not a leading zero country, and the presence of the leading zero leads us to format the
        // number using raw_input.
        $number7 = $this->phoneUtil->parseAndKeepRawInput("0734567 8901", RegionCode::US);
        $this->assertEquals("0734567 8901", $this->phoneUtil->formatInOriginalFormat($number7, RegionCode::US));

        // This number is valid, but we don't have a formatting pattern for it. Fall back to the raw
        // input.
        $number8 = $this->phoneUtil->parseAndKeepRawInput("02-4567-8900", RegionCode::KR);
        $this->assertEquals("02-4567-8900", $this->phoneUtil->formatInOriginalFormat($number8, RegionCode::KR));

        $number9 = $this->phoneUtil->parseAndKeepRawInput("01180012345678", RegionCode::US);
        $this->assertEquals("011 800 1234 5678", $this->phoneUtil->formatInOriginalFormat($number9, RegionCode::US));

        $number10 = $this->phoneUtil->parseAndKeepRawInput("+80012345678", RegionCode::KR);
        $this->assertEquals("+800 1234 5678", $this->phoneUtil->formatInOriginalFormat($number10, RegionCode::KR));

        // US local numbers are formatted correctly, as we have formatting patterns for them.
        $localNumberUS = $this->phoneUtil->parseAndKeepRawInput("2530000", RegionCode::US);
        $this->assertEquals("253 0000", $this->phoneUtil->formatInOriginalFormat($localNumberUS, RegionCode::US));

        $numberWithNationalPrefixUS =
            $this->phoneUtil->parseAndKeepRawInput("18003456789", RegionCode::US);
        $this->assertEquals(
            "1 800 345 6789",
            $this->phoneUtil->formatInOriginalFormat($numberWithNationalPrefixUS, RegionCode::US)
        );

        $numberWithoutNationalPrefixGB =
            $this->phoneUtil->parseAndKeepRawInput("2087654321", RegionCode::GB);
        $this->assertEquals(
            "20 8765 4321",
            $this->phoneUtil->formatInOriginalFormat($numberWithoutNationalPrefixGB, RegionCode::GB)
        );
        // Make sure no metadata is modified as a result of the previous function call.
        $this->assertEquals("(020) 8765 4321", $this->phoneUtil->formatInOriginalFormat($number5, RegionCode::GB));

        $numberWithNationalPrefixMX =
            $this->phoneUtil->parseAndKeepRawInput("013312345678", RegionCode::MX);
        $this->assertEquals(
            "013312345678",
            $this->phoneUtil->formatInOriginalFormat($numberWithNationalPrefixMX, RegionCode::MX)
        );

        $numberWithoutNationalPrefixMX =
            $this->phoneUtil->parseAndKeepRawInput("3312345678", RegionCode::MX);
        $this->assertEquals(
            "33 1234 5678",
            $this->phoneUtil->formatInOriginalFormat($numberWithoutNationalPrefixMX, RegionCode::MX)
        );

        $italianFixedLineNumber =
            $this->phoneUtil->parseAndKeepRawInput("0212345678", RegionCode::IT);
        $this->assertEquals(
            "02 1234 5678",
            $this->phoneUtil->formatInOriginalFormat($italianFixedLineNumber, RegionCode::IT)
        );

        $numberWithNationalPrefixJP =
            $this->phoneUtil->parseAndKeepRawInput("00777012", RegionCode::JP);
        $this->assertEquals(
            "0077-7012",
            $this->phoneUtil->formatInOriginalFormat($numberWithNationalPrefixJP, RegionCode::JP)
        );

        $numberWithoutNationalPrefixJP =
            $this->phoneUtil->parseAndKeepRawInput("0777012", RegionCode::JP);
        $this->assertEquals(
            "0777012",
            $this->phoneUtil->formatInOriginalFormat($numberWithoutNationalPrefixJP, RegionCode::JP)
        );

        $numberWithCarrierCodeBR =
            $this->phoneUtil->parseAndKeepRawInput("012 3121286979", RegionCode::BR);
        $this->assertEquals(
            "012 3121286979",
            $this->phoneUtil->formatInOriginalFormat($numberWithCarrierCodeBR, RegionCode::BR)
        );

        // The default national prefix used in this case is 045. When a number with national prefix 044
        // is entered, we return the raw input as we don't want to change the number entered.
        $numberWithNationalPrefixMX1 =
            $this->phoneUtil->parseAndKeepRawInput("044(33)1234-5678", RegionCode::MX);
        $this->assertEquals(
            "044(33)1234-5678",
            $this->phoneUtil->formatInOriginalFormat($numberWithNationalPrefixMX1, RegionCode::MX)
        );

        $numberWithNationalPrefixMX2 =
            $this->phoneUtil->parseAndKeepRawInput("045(33)1234-5678", RegionCode::MX);
        $this->assertEquals(
            "045 33 1234 5678",
            $this->phoneUtil->formatInOriginalFormat($numberWithNationalPrefixMX2, RegionCode::MX)
        );

        // The default international prefix used in this case is 0011. When a number with international
        // prefix 0012 is entered, we return the raw input as we don't want to change the number
        // entered.
        $outOfCountryNumberFromAU1 =
            $this->phoneUtil->parseAndKeepRawInput("0012 16502530000", RegionCode::AU);
        $this->assertEquals(
            "0012 16502530000",
            $this->phoneUtil->formatInOriginalFormat($outOfCountryNumberFromAU1, RegionCode::AU)
        );

        $outOfCountryNumberFromAU2 =
            $this->phoneUtil->parseAndKeepRawInput("0011 16502530000", RegionCode::AU);
        $this->assertEquals(
            "0011 1 650 253 0000",
            $this->phoneUtil->formatInOriginalFormat($outOfCountryNumberFromAU2, RegionCode::AU)
        );

    }

    public function testIsPremiumRate()
    {
        $this->assertEquals(PhoneNumberType::PREMIUM_RATE, $this->phoneUtil->getNumberType(self::$usPremium));

        $premiumRateNumber = new PhoneNumber();
        $premiumRateNumber->setCountryCode(39)->setNationalNumber(892123);
        $this->assertEquals(
            PhoneNumberType::PREMIUM_RATE,
            $this->phoneUtil->getNumberType($premiumRateNumber)
        );

        $premiumRateNumber->clear();
        $premiumRateNumber->setCountryCode(44)->setNationalNumber(9187654321);
        $this->assertEquals(
            PhoneNumberType::PREMIUM_RATE,
            $this->phoneUtil->getNumberType($premiumRateNumber)
        );

        $premiumRateNumber->clear();
        $premiumRateNumber->setCountryCode(49)->setNationalNumber(9001654321);
        $this->assertEquals(
            PhoneNumberType::PREMIUM_RATE,
            $this->phoneUtil->getNumberType($premiumRateNumber)
        );

        $premiumRateNumber->clear();
        $premiumRateNumber->setCountryCode(49)->setNationalNumber(90091234567);
        $this->assertEquals(
            PhoneNumberType::PREMIUM_RATE,
            $this->phoneUtil->getNumberType($premiumRateNumber)
        );
    }

    public function testIsTollFree()
    {
        $tollFreeNumber = new PhoneNumber();

        $tollFreeNumber->setCountryCode(1)->setNationalNumber(8881234567);
        $this->assertEquals(
            PhoneNumberType::TOLL_FREE,
            $this->phoneUtil->getNumberType($tollFreeNumber)
        );

        $tollFreeNumber->clear();
        $tollFreeNumber->setCountryCode(39)->setNationalNumber(803123);
        $this->assertEquals(
            PhoneNumberType::TOLL_FREE,
            $this->phoneUtil->getNumberType($tollFreeNumber)
        );

        $tollFreeNumber->clear();
        $tollFreeNumber->setCountryCode(44)->setNationalNumber(8012345678);
        $this->assertEquals(
            PhoneNumberType::TOLL_FREE,
            $this->phoneUtil->getNumberType($tollFreeNumber)
        );

        $tollFreeNumber->clear();
        $tollFreeNumber->setCountryCode(49)->setNationalNumber(8001234567);
        $this->assertEquals(
            PhoneNumberType::TOLL_FREE,
            $this->phoneUtil->getNumberType($tollFreeNumber)
        );

        $this->assertEquals(
            PhoneNumberType::TOLL_FREE,
            $this->phoneUtil->getNumberType(self::$internationalTollFree)
        );
    }

    public function testIsMobile()
    {
        $this->assertEquals(PhoneNumberType::MOBILE, $this->phoneUtil->getNumberType(self::$bsMobile));
        $this->assertEquals(PhoneNumberType::MOBILE, $this->phoneUtil->getNumberType(self::$gbMobile));
        $this->assertEquals(PhoneNumberType::MOBILE, $this->phoneUtil->getNumberType(self::$itMobile));
        $this->assertEquals(PhoneNumberType::MOBILE, $this->phoneUtil->getNumberType(self::$arMobile));

        $mobileNumber = new PhoneNumber();
        $mobileNumber->setCountryCode(49)->setNationalNumber(15123456789);
        $this->assertEquals(PhoneNumberType::MOBILE, $this->phoneUtil->getNumberType($mobileNumber));
    }

    public function testIsFixedLine()
    {
        $this->assertEquals(PhoneNumberType::FIXED_LINE, $this->phoneUtil->getNumberType(self::$bsNumber));
        $this->assertEquals(PhoneNumberType::FIXED_LINE, $this->phoneUtil->getNumberType(self::$itNumber));
        $this->assertEquals(PhoneNumberType::FIXED_LINE, $this->phoneUtil->getNumberType(self::$gbNumber));
        $this->assertEquals(PhoneNumberType::FIXED_LINE, $this->phoneUtil->getNumberType(self::$deNumber));
    }

    public function testIsFixedLineAndMobile()
    {
        $this->assertEquals(PhoneNumberType::FIXED_LINE_OR_MOBILE, $this->phoneUtil->getNumberType(self::$usNumber));

        $fixedLineAndMobileNumber = new PhoneNumber();
        $fixedLineAndMobileNumber->setCountryCode(54)->setNationalNumber(1987654321);
        $this->assertEquals(
            PhoneNumberType::FIXED_LINE_OR_MOBILE,
            $this->phoneUtil->getNumberType($fixedLineAndMobileNumber)
        );
    }

    /**
     *
     */
    public function testIsValidNumberForRegion()
    {
        // This number is valid for the Bahamas, but is not a valid US number.
        $this->assertTrue($this->phoneUtil->isValidNumber(self::$bsNumber));
        $this->assertTrue($this->phoneUtil->isValidNumberForRegion(self::$bsNumber, RegionCode::BS));
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion(self::$bsNumber, RegionCode::US));
        $bsInvalidNumber = new PhoneNumber();
        $bsInvalidNumber->setCountryCode(1)->setNationalNumber(2421232345);
        // This number is no longer valid.
        $this->assertFalse($this->phoneUtil->isValidNumber($bsInvalidNumber));

        // La Mayotte and Reunion use 'leadingDigits' to differentiate them.
        $reNumber = new PhoneNumber();
        $reNumber->setCountryCode(262)->setNationalNumber(262123456);
        $this->assertTrue($this->phoneUtil->isValidNumber($reNumber));
        $this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
        // Now change the number to be a number for La Mayotte.
        $reNumber->setNationalNumber(269601234);
        $this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
        // This number is no longer valid for La Reunion.
        $reNumber->setNationalNumber(269123456);
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
        $this->assertFalse($this->phoneUtil->isValidNumber($reNumber));
        // However, it should be recognised as from La Mayotte, since it is valid for this region.
        $this->assertEquals(RegionCode::YT, $this->phoneUtil->getRegionCodeForNumber($reNumber));
        // This number is valid in both places.
        $reNumber->setNationalNumber(800123456);
        $this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
        $this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
        $this->assertTrue($this->phoneUtil->isValidNumberForRegion(self::$internationalTollFree, RegionCode::UN001));
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion(self::$internationalTollFree, RegionCode::US));
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion(self::$internationalTollFree, RegionCode::ZZ));

        $invalidNumber = new PhoneNumber();
        // Invalid country calling codes.
        $invalidNumber->setCountryCode(3923)->setNationalNumber(2366);
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::ZZ));
        $invalidNumber->setCountryCode(3923)->setNationalNumber(2366);
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::UN001));
        $invalidNumber->setCountryCode(0)->setNationalNumber(2366);
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::UN001));
        $invalidNumber->setCountryCode(0);
        $this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::ZZ));
    }

    public function testGetRegionCodeForNumber()
    {
        $this->assertEquals(RegionCode::BS, $this->phoneUtil->getRegionCodeForNumber(self::$bsNumber));
        $this->assertEquals(RegionCode::US, $this->phoneUtil->getRegionCodeForNumber(self::$usNumber));
        $this->assertEquals(RegionCode::GB, $this->phoneUtil->getRegionCodeForNumber(self::$gbMobile));
        $this->assertEquals(RegionCode::UN001, $this->phoneUtil->getRegionCodeForNumber(self::$internationalTollFree));
    }

    public function testCanBeInternationallyDialled()
    {
        // We have no-international-dialling rules for the US in our test metadata that say that
        // toll-free numbers cannot be dialled internationally.
        $this->assertFalse($this->phoneUtil->canBeInternationallyDialled(self::$usTollFree));
        // Normal US numbers can be internationally dialled.
        $this->assertTrue($this->phoneUtil->canBeInternationallyDialled(self::$usNumber));

        // Invalid number.
        $this->assertTrue($this->phoneUtil->canBeInternationallyDialled(self::$usLocalNumber));

        // We have no data for NZ - should return true.
        $this->assertTrue($this->phoneUtil->canBeInternationallyDialled(self::$nzNumber));
        $this->assertTrue($this->phoneUtil->canBeInternationallyDialled(self::$internationalTollFree));
    }

    public function testParseNationalNumber()
    {
        // National prefix attached.
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("033316005", RegionCode::NZ));
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("33316005", RegionCode::NZ));
        // National prefix attached and some formatting present.
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("03-331 6005", RegionCode::NZ));
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("03 331 6005", RegionCode::NZ));

        // Testing international prefixes.
        // Should strip country calling code.
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("0064 3 331 6005", RegionCode::NZ));
        // Try again, but this time we have an international number with Region Code US. It should
        // recognise the country calling code and parse accordingly.
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("01164 3 331 6005", RegionCode::US));
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("+64 3 331 6005", RegionCode::US));
        // We should ignore the leading plus here, since it is not followed by a valid country code but
        // instead is followed by the IDD for the US.
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("+01164 3 331 6005", RegionCode::US));
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("+0064 3 331 6005", RegionCode::NZ));
        $this->assertEquals(self::$nzNumber, $this->phoneUtil->parse("+ 00 64 3 331 6005", RegionCode::NZ));

        $nzNumber = new PhoneNumber();
        $nzNumber->setCountryCode(64)->setNationalNumber(64123456);
        $this->assertEquals($nzNumber, $this->phoneUtil->parse("64(0)64123456", RegionCode::NZ));
        // Check that using a "/" is fine in a phone number.
        $this->assertEquals(self::$deNumber, $this->phoneUtil->parse("301/23456", RegionCode::DE));

        $usNumber = new PhoneNumber();
        // Check it doesn't use the '1' as a country calling code when parsing if the phone number was
        // already possible.
        $usNumber->setCountryCode(1)->setNationalNumber(1234567890);
        $this->assertEquals($usNumber, $this->phoneUtil->parse("123-456-7890", RegionCode::US));

        // Test star numbers. Although this is not strictly valid, we would like to make sure we can
        // parse the output we produce when formatting the number.
        $this->assertEquals(self::$jpStarNumber, $this->phoneUtil->parse("+81 *2345", RegionCode::JP));

        $shortNumber = new PhoneNumber();
        $shortNumber->setCountryCode(64)->setNationalNumber(12);
        $this->assertEquals($shortNumber, $this->phoneUtil->parse("12", RegionCode::NZ));

    }

    public function testIsAlphaNumber()
    {
        $this->assertTrue($this->phoneUtil->isAlphaNumber("1800 six-flags"));
        $this->assertTrue($this->phoneUtil->isAlphaNumber("1800 six-flags ext. 1234"));
        $this->assertTrue($this->phoneUtil->isAlphaNumber("+800 six-flags"));
        $this->assertFalse($this->phoneUtil->isAlphaNumber("1800 123-1234"));
        $this->assertFalse($this->phoneUtil->isAlphaNumber("1800 123-1234 extension: 1234"));
        $this->assertFalse($this->phoneUtil->isAlphaNumber("+800 1234-1234"));
    }

    public function testIsMobileNumberPortableRegion()
    {
        $this->assertTrue($this->phoneUtil->isMobileNumberPortableRegion(RegionCode::US));
        $this->assertTrue($this->phoneUtil->isMobileNumberPortableRegion(RegionCode::GB));
        $this->assertFalse($this->phoneUtil->isMobileNumberPortableRegion(RegionCode::AE));
        $this->assertFalse($this->phoneUtil->isMobileNumberPortableRegion(RegionCode::BS));
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

}
