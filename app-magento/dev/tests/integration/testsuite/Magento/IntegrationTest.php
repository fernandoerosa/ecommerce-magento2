<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento;

use Magento\TestFramework\SkippableInterface;
use Magento\TestFramework\Workaround\Override\Config;
use Magento\TestFramework\Workaround\Override\WrapperGenerator;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\TestSuiteMapper;
use PHPUnit\TextUI\XmlConfiguration\Configuration;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use PHPUnit\TextUI\Configuration\TestSuite as TestSuiteConfiguration;
use PHPUnit\TextUI\Configuration\TestSuiteCollection;

/**
 * Integration tests wrapper.
 */
class IntegrationTest extends TestCase
{
    /**
     * @param string $className
     *
     * @return TestSuite
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testCustomSuite($className = null)
    {
        $this->markTestSkipped('Skipping test because of suite method has been depreciated in phpunit10.');
        $generator = new WrapperGenerator();
        $overrideConfig = Config::getInstance();
        $configuration = self::getConfiguration();
        $suitesConfig = $configuration->testSuite();
        $suite = TestSuite::fromClassName($className);
        foreach ($suitesConfig as $suiteConfig) {
            if ($suiteConfig->name() === 'Magento Integration Tests') {
                continue;
            }
            $suites = self::getSuites($suiteConfig);
            /** @var TestSuite $testSuite */
            foreach ($suites as $testSuite) {
                /** @var TestSuite $test */
                foreach ($testSuite as $test) {
                    $testName = $test->getName();

                    if ($overrideConfig->hasSkippedTest($testName) && !$test instanceof SkippableInterface) {
                        $reflectionClass = new \ReflectionClass($testName);
                        $resultTest = $generator->generateTestWrapper($reflectionClass);
                        $suite->addTest(TestSuite::fromClassName($resultTest));
                    } else {
                        $suite->addTest($test);
                    }
                }
            }
        }

        return $suite;
    }

    /**
     * Returns config file name from command line params.
     *
     * @return string
     */
    private static function getConfigurationFile(): string
    {
        $params = getopt('c:', ['configuration:']);
        $defaultConfigFile = file_exists(__DIR__ . '../../phpunit.xml')
            ? __DIR__ . '/../../phpunit.xml'
            : __DIR__ . '/../../phpunit.xml';
        $longConfig = $params['configuration'] ?? $defaultConfigFile;
        $shortConfig = $params['c'] ?? '';

        return $shortConfig ?: $longConfig;
    }

    /**
     * Retrieve configuration.
     *
     * @return Configuration
     */
    private static function getConfiguration()
    {
        return (new Loader())->load(self::getConfigurationFile());
    }

    /**
     * Retrieve test suites by suite config.
     *
     * @param TestSuiteConfiguration $suiteConfig
     *
     * @return TestSuite
     */
    private static function getSuites($suiteConfig)
    {
        return (new TestSuiteMapper())->map(self::getConfigurationFile(),
            TestSuiteCollection::fromArray([$suiteConfig]),'', ''
        );
    }
}