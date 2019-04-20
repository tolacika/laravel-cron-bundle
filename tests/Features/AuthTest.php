<?php


namespace Tolacika\CronBundle\Tests\Features;


use Tolacika\CronBundle\CronBundle;
use Tolacika\CronBundle\Http\Middleware\Authenticate;
use Tolacika\CronBundle\Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * @test
     */
    public function test_auth_callback_works()
    {
        $this->assertFalse(CronBundle::check('tolacika'));

        CronBundle::auth(function ($request) {
            return $request == 'tolacika';
        });

        $this->assertTrue(CronBundle::check('tolacika'));
        $this->assertFalse(CronBundle::check('not-tolacika'));
        $this->assertFalse(CronBundle::check(null));
    }

    /** @test */
    public function test_auth_middleware_works()
    {
        CronBundle::auth(function () {
            return true;
        });
        $middleware = new Authenticate;
        $response = $middleware->handle(
            new class {
            },
            function ($value) {
                return 'response';
            }
        );
        $this->assertEquals('response', $response);
    }
    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function test_auth_middleware_responds_with_403_on_failure()
    {
        CronBundle::auth(function () {
            return false;
        });
        $middleware = new Authenticate;
        $response = $middleware->handle(
            new class {
            },
            function ($value) {
                return 'response';
            }
        );
    }

    /**
     * @test
     */
    public function test_dashboard_availability() {
        CronBundle::auth(function () {
            return true;
        });
        $response = $this->get(route('cron-bundle.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_dashboard_availability_fails() {
        CronBundle::auth(function () {
            return false;
        });

        $response = $this->get(route('cron-bundle.index'));
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_user_id_getting_method()
    {
        CronBundle::setUser(function () {
            return 1;
        });

        self::assertEquals(1, CronBundle::getUser());
    }
}
