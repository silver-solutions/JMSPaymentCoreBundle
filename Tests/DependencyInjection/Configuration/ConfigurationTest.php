<?php

namespace JMS\Payment\CoreBundle\Tests\DependencyInjection\Configuration;

use JMS\Payment\CoreBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testNoSecret()
    {
        $this->assertConfigurationIsValid([]);
        $this->assertConfigurationIsInvalid([['secret' => '']]);

        $this->assertProcessedConfigurationEquals(
            [],
            [
                'encryption' => [
                    'enabled' => false,
                    'provider' => 'defuse_php_encryption',
                ],
            ]
        );
    }

    public function testSecret()
    {
        $this->assertConfigurationIsValid([['secret' => 'foo']]);

        $this->assertProcessedConfigurationEquals(
            [['secret' => 'foo']],
            [
                'secret' => 'foo',
                'encryption' => [
                    'enabled' => true,
                    'secret' => 'foo',
                    'provider' => 'mcrypt',
                ],
            ]
        );
    }

    public function testEncryptionDisabled()
    {
        $this->assertConfigurationIsValid([]);
        $this->assertConfigurationIsValid([['encryption' => false]]);

        $this->assertProcessedConfigurationEquals(
            [],
            [
                'encryption' => [
                    'enabled' => false,
                    'provider' => 'defuse_php_encryption',
                ],
            ]
        );

        $this->assertProcessedConfigurationEquals(
            [['encryption' => false]],
            [
                'encryption' => [
                    'enabled' => false,
                    'provider' => 'defuse_php_encryption',
                ],
            ]
        );

        $this->assertProcessedConfigurationEquals(
            [
                [
                    'encryption' => [
                        'enabled' => false,
                    ],
                ],
            ],
            [
                'encryption' => [
                    'enabled' => false,
                    'provider' => 'defuse_php_encryption',
                ],
            ]
        );
    }

    public function testEncryptionEnabled()
    {
        $this->assertConfigurationIsInvalid([['encryption' => true]]);

        $this->assertConfigurationIsInvalid([
            [
                'encryption' => [
                    'enabled' => true,
                ],
            ],
        ]);

        $this->assertConfigurationIsValid([
            [
                'encryption' => [
                    'enabled' => true,
                    'secret' => 'foo',
                ],
            ],
        ]);

        $this->assertConfigurationIsValid([
            [
                'encryption' => [
                    'secret' => 'foo',
                ],
            ],
        ]);

        $this->assertProcessedConfigurationEquals(
            [
                [
                    'encryption' => [
                        'secret' => 'foo',
                    ],
                ],
            ],
            [
                'encryption' => [
                    'enabled' => true,
                    'secret' => 'foo',
                    'provider' => 'defuse_php_encryption',
                ],
            ]
        );

        $this->assertProcessedConfigurationEquals(
            [
                [
                    'encryption' => [
                        'enabled' => true,
                        'secret' => 'foo',
                    ],
                ],
            ],
            [
                'encryption' => [
                    'enabled' => true,
                    'secret' => 'foo',
                    'provider' => 'defuse_php_encryption',
                ],
            ]
        );
    }

    protected function getConfiguration()
    {
        return new Configuration('jms_payment_core');
    }
}
