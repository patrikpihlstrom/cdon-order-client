<?php
/**
 * Created by PhpStorm.
 * User: patrik
 * Date: 2018-08-20
 * Time: 11:58
 */

namespace Patrik\CdonOrderClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


class OrderClient extends Client
{
    public function __construct($config = [])
    {
        if ($config == [])
        {
            $config = include __DIR__ . '/../../api.php';
        }

        parent::__construct($config);
    }

    /**
     * @param $id
     * @return array
     */
    public function getOrder($id)
    {
        try
        {
            $response = $this->request('GET', 'order/' . $id);
            return ['status' => 'success', 'message' => json_decode($response->getBody(), true)];
        } catch (GuzzleException $e)
        {
            return ['status' => $e->getCode, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param $id
     * @param null $packageCarrierId
     * @param null $packageId
     * @return array
     * @throws \Exception
     */
    public function deliverOrder($id, $packageCarrierId = null, $packageId = null)
    {
        $order = $this->getOrder($id);
        if ($order['status'] != 'success')
        {
            throw new \Exception($order['message']);
        }

        try
        {
            $request = $this->_createDeliverOrderRequest($order['message'], $packageId,
                                                         $packageCarrierId);
            $response = $this->post('orderdelivery/',
                                    [\GuzzleHttp\RequestOptions::JSON => $request]);
            return ['status' => 'success', 'message' => json_decode($response->getBody(), true)];
        } catch (GuzzleException $e)
        {
            return ['status' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }

    public function invoiceOrder($id)
    {
        throw new \Exception('Not yet implemented.');
    }

    public function returnOrder($id)
    {
        throw new \Exception('Not yet implemented.');
    }

    public function cancelOrder($id)
    {
        throw new \Exception('Not yet implemented.');
    }

    public function packageOrder($id)
    {
        throw new \Exception('Not yet implemented.');
    }

    /**
     * @param $order
     * @param $packageId
     * @param $packageCarrierId
     * @return array
     */
    private function _createDeliverOrderRequest($order, $packageId, $packageCarrierId)
    {
        $request = [];
        $request['OrderId'] = $order['OrderDetails']['OrderId'];
        $orderRows = $order['OrderDetails']['OrderRows'];
        $request['Products'] = [];
        foreach ($orderRows as $row)
        {
            $request['Products'][] = [
                'OrderRowId' => $row['OrderRowId'],
                'QuantityToDeliver' => $row['Quantity'] - $row['DeliveredQuantity'] - $row['CancelledQuantity'],
                'PackageId' => $packageId == null ? '' : $packageId,
                'PackageCarrierId' => $packageCarrierId == null ? '' : $packageCarrierId
            ];
        }

        return $request;
    }
}
