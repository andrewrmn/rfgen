<?php

namespace WLMStripe\Service\Checkout;

class SessionService extends \WLMStripe\Service\AbstractService
{
    /**
     * Returns a list of Checkout Sessions.
     *
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Collection
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/checkout/sessions', $params, $opts);
    }

    /**
     * When retrieving a Checkout Session, there is an includable
     * <strong>line_items</strong> property containing the first handful of those
     * items. There is also a URL where you can retrieve the full (paginated) list of
     * line items.
     *
     * @param string $parentId
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Collection
     */
    public function allLineItems($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/checkout/sessions/%s/line_items', $parentId), $params, $opts);
    }

    /**
     * Creates a Session object.
     *
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Checkout\Session
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/checkout/sessions', $params, $opts);
    }

    /**
     * Retrieves a Session object.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Checkout\Session
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/checkout/sessions/%s', $id), $params, $opts);
    }
}