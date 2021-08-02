<?php


namespace Arniro\Paybox;


use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class Paybox
{
    const BASE_URL = 'https://api.paybox.money/payment.php';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $request = [];

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function generateUrl($params, $additionalParams = [])
    {
        $this->request = array_merge($this->request, $this->getParams($params, $additionalParams));

        $this->url = implode('?', [static::BASE_URL, $this->buildQuery($this->request)]);

        return $this;
    }

    /**
     * @return RedirectResponse
     */
    public function redirect()
    {
        return redirect($this->url);
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $params
     * @return array
     */
    public function getParams($params, $additionalParams = [])
    {
        return array_merge(
            $this->configParams($params),
            $this->paymentParams($params, $additionalParams)
        );
    }

    /**
     * @param array $params
     * @return array
     */
    protected function configParams($params)
    {
        return [
            'pg_merchant_id' => Arr::get($this->config, 'merchant_id'),
            'pg_result_url' => $this->getParamUrl($params, 'result_url'),
            'pg_success_url' => $this->getParamUrl($params, 'success_url', 'result_url'),
            'pg_failure_url' => $this->getParamUrl($params, 'failure_url', 'result_url'),
            'pg_testing_mode' => $this->getParam($params, 'testing_mode'),
            'pg_currency' => $this->getParam($params, 'currency'),
            'pg_success_url_method' => $this->getParam($this->config, 'success_url_method', 'url_method'),
            'pg_failure_url_method' => $this->getParam($this->config, 'failure_url_method', 'url_method'),
            'pg_salt' => $this->getParam($params, 'salt')
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    protected function paymentParams($params, $additionalParams = [])
    {
        return array_merge([
            'pg_amount' => Arr::get($params, 'price', 0),
            'pg_description' => Arr::get($params, 'description'),
            'pg_order_id' => Arr::get($params, 'order_id'),
            'pg_user_email' => Arr::get($params, 'email'),
            'pg_user_phone' => Arr::get($params, 'phone'),
            'client_name' => Arr::get($params, 'name'),
            'client_address' => Arr::get($params, 'address')
        ], $additionalParams);
    }

    /**
     * @param $params
     * @param $key
     * @param null $fallbackKey
     * @return string|int|float
     */
    protected function getParam($params, $key, $fallbackKey = null)
    {
        if (Arr::hasAny($params, [$key, $fallbackKey])) {
            return Arr::get($params, $key) ?: Arr::get($params, $fallbackKey);
        }

        return Arr::get($this->config, $key) ?: Arr::get($this->config, $fallbackKey);
    }

    /**
     * @param $params
     * @param $key
     * @param null $fallbackKey
     * @return string
     */
    protected function getParamUrl($params, $key, $fallbackKey = null)
    {
        if (! $url = $this->getParam(...func_get_args())) {
            return null;
        }

        return url($url);
    }

    /**
     * @param array $request
     * @return string
     */
    protected function buildQuery($request)
    {
        ksort($request);
        array_unshift($request, 'payment.php');
        array_push($request, Arr::get($this->config, 'secret_key'));

        $request['pg_sig'] = md5(implode(';', $request));

        unset($request[0], $request[1]);

        return http_build_query($request);
    }
}

