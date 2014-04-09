<?php
use Mockery as m;
use MissionNext\Facade\SecurityContext as FS;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    protected $useDatabase = true;

    protected $routePrefix = '';

    protected $applicationKey = '123456';

    public function call()
    {
        $args = func_get_args();
        $args[1] = $this->routePrefix . '/' . $args[1];
        call_user_func_array(array($this->client, 'request'), $args);

        return $this->client->getResponse();
    }

    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__ . '/../../bootstrap/start.php';
    }

    public function setUp()
    {
        parent::setUp();
        if ($this->useDatabase) {
            $this->setUpDb();
        }
        /** @var  $securityContext \MissionNext\Api\Auth\SecurityContext */
        $securityContext = FS::getInstance();
        $securityContext->setToken(new \MissionNext\Api\Auth\Token());
        $securityContext->getToken()
            ->setApp(\MissionNext\Models\Application\Application::wherePublicKey($this->applicationKey)->first());
        $this->routePrefix = \MissionNext\Routing\Routing::API_PREFIX;
    }

    public function teardown()
    {
        m::close();
    }

    public function setUpDb()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');

    }

    public function teardownDb()
    {
        Artisan::call('migrate:reset');
    }

}
