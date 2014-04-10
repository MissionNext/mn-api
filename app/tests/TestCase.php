<?php
use Mockery as m;
use MissionNext\Facade\SecurityContext as FS;
use MissionNext\Api\Auth\SecurityContext as SC;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    protected $useDatabase = true;

    protected $routePrefix = '';

    protected $applicationKey = '123456';
    /** @var  \MissionNext\Api\Auth\SecurityContext */
    private $securityContext;

    protected function setRole($role)
    {
        $this->securityContext->getToken()->setRoles([$role]);
    }

    private function setSecurityContext(SC $securityContext)
    {
        $this->securityContext = $securityContext;

        return $this;
    }

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
        $this->setApp();
    }

    public function teardown()
    {
        m::close();
    }

    protected function setApp()
    {
        /** @var  $securityContext \MissionNext\Api\Auth\SecurityContext */
        $securityContext = FS::getInstance();
        $securityContext->setToken(new \MissionNext\Api\Auth\Token());
        $securityContext->getToken()
            ->setApp(\MissionNext\Models\Application\Application::wherePublicKey($this->applicationKey)->first());
        $this->routePrefix = \MissionNext\Routing\Routing::API_PREFIX;
        $this->setSecurityContext($securityContext);
    }

    /**
     * @return SC
     */
    protected function securityContext()
    {

        return $this->securityContext;
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
