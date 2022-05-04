<?php

namespace ConsulConfigManager\Testing;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\Contracts\TestCase as TestCaseContract;

/**
 * Class TestCase
 * @package ConsulConfigManager\Testing
 */
abstract class TestCase extends Orchestra implements TestCaseContract
{
    use DatabaseMigrations;

    /**
     * List of package providers to be loaded
     * @var array
     */
    protected array $packageProviders = [];

    /**
     * List of application aliases to be overridden
     * @var array
     */
    protected array $packageAliases = [];

    /**
     * List of packages whose providers should be ignored
     * @var array
     */
    protected array $ignoreDiscoveriesFrom = [];

    /**
     * Indicates whether configuration should be loaded from `.env` file
     * @var bool
     */
    protected bool $configurationFromEnvironment = false;

    /**
     * Path to directory containing `.env` file
     * @var string
     */
    protected string $configurationFromFile = __DIR__ . '/..';

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->runBeforeSetup();
        parent::setUp();
        $this->registerConcerns();
        $this->runAfterSetup();
    }

    /**
     * Code which should be executed BEFORE `setUp` event
     * @return void
     */
    public function runBeforeSetUp(): void
    {
    }

    /**
     * Register concerns after application has been created
     * @return void
     */
    private function registerConcerns(): void
    {
        if (method_exists($this, 'createQueueTables')) {
            $this->createQueueTables();
        }
        if (method_exists($this, 'createEventSourcingTables')) {
            $this->createEventSourcingTables();
        }
        if (method_exists($this, 'createPermissionsTables')) {
            $this->createPermissionsTables();
        }
    }

    /**
     * Code which should be executed AFTER `setUp` event
     * @return void
     */
    public function runAfterSetUp(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->runBeforeTearDown();
        $this->deregisterConcerns();
        parent::tearDown();
        $this->runAfterTearDown();
    }

    /**
     * Code which should be executed BEFORE `tearDown` event
     * @return void
     */
    public function runBeforeTearDown(): void
    {
    }

    /**
     * Deregister concerns before application is torn down
     * @return void
     */
    private function deregisterConcerns(): void
    {
        if (method_exists($this, 'dropQueueTables')) {
            $this->dropQueueTables();
        }
        if (method_exists($this, 'dropEventSourcingTables')) {
            $this->dropEventSourcingTables();
        }
        if (method_exists($this, 'dropPermissionsTables')) {
            $this->dropPermissionsTables();
        }
    }

    /**
     * Code which should be executed AFTER `tearDown` event
     * @return void
     */
    public function runAfterTearDown(): void
    {
    }

    /**
     * Get application package providers
     * @param Application $app
     * @return array
     */
    public function getPackageProviders($app): array
    {
        return $this->packageProviders;
    }

    /**
     * Get list of application aliases overrides
     * @param Application $app
     * @return array
     */
    public function getPackageAliases($app): array
    {
        return $this->packageAliases;
    }

    /**
     * Get list of packages whose providers should not be auto-discovered
     * @return array
     */
    public function ignorePackageDiscoveriesFrom(): array
    {
        return $this->ignoreDiscoveriesFrom;
    }

    /**
     * Allows to configure base application environment
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        if ($this->configurationFromEnvironment) {
            $app->useEnvironmentPath($this->configurationFromFile);
            $app->bootstrapWith([
                LoadEnvironmentVariables::class,
            ]);
        }

        $this
            ->setConfigurationValue('app.env', 'testing', $app)
            ->setConfigurationValue('app.debug', true, $app)
            ->setConfigurationValue('auth.default', 'api', $app)
            ->setConfigurationValue('cache.default', 'array', $app)
            ->setConfigurationValue('hashing.bcrypt.round', 4, $app)
            ->setConfigurationValue('database.default', 'sqlite', $app)
            ->setConfigurationValue('database.connections.sqlite', [
                'driver'        =>  'sqlite',
                'database'      =>  ':memory:',
                'prefix'        =>  '',
            ], $app);

        $this->setUpEnvironment($app);

        parent::getEnvironmentSetUp($app);
    }

    /**
     * Method which allows to configure application environment
     * @param Application $app
     * @return void
     */
    public function setUpEnvironment(Application $app): void
    {
    }

    /**
     * Set configuration value
     * @param string $key
     * @param mixed $value
     * @param Application|null $application
     * @return $this
     */
    public function setConfigurationValue(string $key, mixed $value, ?Application $application = null): self
    {
        $application = $application ?? $this->app;
        /**
         * @var Repository $repository
         */
        $repository = $application['config'];
        $repository->set($key, $value);
        return $this;
    }
}
