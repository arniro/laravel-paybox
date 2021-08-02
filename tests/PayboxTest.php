<?php

namespace Arniro\Paybox\Tests;

use Arniro\Paybox\Facades\Paybox;
use Arniro\Paybox\PayboxServiceProvider;
use Illuminate\Support\Arr;
use Orchestra\Testbench\TestCase;

class PayboxTest extends TestCase
{
    /** @var \Arniro\Paybox\Paybox */
    protected $paybox;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('paybox.merchant_id', 'merchant');
    }

    protected function getPackageProviders($app)
    {
        return [PayboxServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->paybox = Paybox::generateUrl([
            'price' => 500,
            'description' => 'Description',
            'order_id' => 123456,
            'email' => 'test@test.com',
            'phone' => '123456789',
            'name' => 'Test Test',
            'address' => 'Test address'
        ]);
    }

    /** @test */
    public function it_adds_config_values_to_the_request()
    {
        $this->assertEquals('merchant', Arr::get($this->paybox->getRequest(), 'pg_merchant_id'));
        $this->assertEquals(url('paybox/result'), Arr::get($this->paybox->getRequest(), 'pg_result_url'));
    }

    /** @test */
    public function it_adds_payment_values_to_the_request()
    {
        $this->assertEquals(500, Arr::get($this->paybox->getRequest(), 'pg_amount'));
        $this->assertEquals(123456, Arr::get($this->paybox->getRequest(), 'pg_order_id'));
    }

    /** @test */
    public function additional_params_can_be_set()
    {
        $this->paybox->generateUrl([], ['foo' => 'bar']);
        $this->assertEquals('bar', Arr::get($this->paybox->getRequest(), 'foo'));
    }

    /** @test */
    public function config_params_can_be_overridden_on_the_go()
    {
        $this->assertEquals('KGS', Arr::get($this->paybox->getRequest(), 'pg_currency'));

        $this->paybox->generateUrl(['currency' => 'KZT']);

        $this->assertEquals('KZT', Arr::get($this->paybox->getRequest(), 'pg_currency'));
    }

    /** @test */
    public function params_can_be_overridden_separately()
    {
        $this->paybox->generateUrl(['price' => 600, 'success_url' => 'paybox/success']);

        $this->assertEquals(600, Arr::get($this->paybox->getRequest(), 'pg_amount'));
        $this->assertEquals(url('paybox/success'), Arr::get($this->paybox->getRequest(), 'pg_success_url'));
    }
}
