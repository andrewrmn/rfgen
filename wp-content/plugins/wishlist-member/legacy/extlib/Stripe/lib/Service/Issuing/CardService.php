<?php

namespace WLMStripe\Service\Issuing;

class CardService extends \WLMStripe\Service\AbstractService
{
    /**
     * Returns a list of Issuing <code>Card</code> objects. The objects are sorted in
     * descending order by creation date, with the most recently created object
     * appearing first.
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
        return $this->requestCollection('get', '/v1/issuing/cards', $params, $opts);
    }

    /**
     * Creates an Issuing <code>Card</code> object.
     *
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Issuing\Card
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/issuing/cards', $params, $opts);
    }

    /**
     * Retrieves an Issuing <code>Card</code> object.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Issuing\Card
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/issuing/cards/%s', $id), $params, $opts);
    }

    /**
     * Updates the specified Issuing <code>Card</code> object by setting the values of
     * the parameters passed. Any parameters not provided will be left unchanged.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\WLMStripe\Util\RequestOptions $opts
     *
     * @throws \WLMStripe\Exception\ApiErrorException if the request fails
     *
     * @return \WLMStripe\Issuing\Card
     */
    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/cards/%s', $id), $params, $opts);
    }
}