<?php
namespace M12\Rollbar\Tests\Functional;

use M12\Rollbar\Rollbar;
use Neos\Utility\ObjectAccess;
use Neos\Flow\Security\Account;

class RollbarTest extends \Neos\Flow\Tests\FunctionalTestCase
{
    /**
     * @var Rollbar
     */
    protected $rollbar;


    public function setUp()
    {
        parent::setUp();
        $this->rollbar = $this->objectManager->get(Rollbar::class);
    }

    /**
     * @test
     */
    public function dummyInit()
    {
        $this->rollbar->init();
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function isEnabledForEnv()
    {
        $this->assertTrue($this->rollbar->isEnabledForEnv());
        $this->assertFalse($this->rollbar->isEnabledForEnv(false));
    }

    /**
     * @test
     */
    public function getRollbarJsSettings()
    {
        $settings = $this->rollbar->getRollbarJsSettings();

        $this->assertArrayHasKey('accessToken', $settings);
        $this->assertArrayHasKey('payload', $settings);
        $this->assertNotEmpty($settings['payload']);
        $this->assertNotEmpty($settings['payload']['environment']);
    }

    /**
     * @test
     */
    public function getRollbarSettings()
    {
        $settings = $this->rollbar->getRollbarSettings();

        $this->assertArrayHasKey('access_token', $settings);
        $this->assertNotEmpty('environment', $settings);
        $this->assertNotEmpty('root', $settings);
    }

    /**
     * @test
     */
    public function getRollbarSettings_whenSecurityContextNotInitialised()
    {
        $settings = $this->rollbar->getRollbarSettings();
        $person = call_user_func($settings['person_fn']);
        $this->assertTrue(is_array($person));
        $this->assertTrue(empty($person));
    }

    /**
     * @test
     */
    public function getRollbarSettings_whenSecurityContextInitialised()
    {
        $this->testableSecurityEnabled = true;
        $this->setupSecurity();

        // test for non-authenticated
        $settings = $this->rollbar->getRollbarSettings();
        $person = call_user_func($settings['person_fn']);
        $this->assertTrue(is_array($person));
        $this->assertTrue(empty($person));

        // test for authenticated user
        $account = new Account();
        $account->setAccountIdentifier('email@example.com');
        $this->authenticateAccount($account);

        $person = call_user_func($settings['person_fn']);
        $this->assertTrue(is_array($person));
        $this->assertFalse(empty($person));
        $this->assertEquals($person['id'], $account->getAccountIdentifier());
    }

    /**
     * @test
     */
    public function getEnvironment()
    {
        $this->assertEquals('testing', $this->rollbar->getEnvironment());

        // switch setting `includeSubContextInEnvironment`
        $settings = ObjectAccess::getProperty($this->rollbar, 'settings', true);
        $settings['includeSubContextInEnvironment'] = true;
        ObjectAccess::setProperty($this->rollbar, 'settings', $settings, true);

        $this->assertTrue(ObjectAccess::getProperty($this->rollbar, 'settings', true)['includeSubContextInEnvironment']);
        $this->assertEquals('testing', $this->rollbar->getEnvironment());
    }
}
