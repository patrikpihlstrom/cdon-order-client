<?php
/**
 * Created by PhpStorm.
 * User: patrik
 * Date: 2018-08-20
 * Time: 13:07
 */

use Patrik\CdonOrderClient\OrderClient;

class TestOrder extends \PHPUnit\Framework\TestCase
{
    /** @var OrderClient $_client */
    private $_client;

    /**
     *
     */
    public function setUp()
    {
        $this->_client = new OrderClient();
        parent::setUp();
    }

    public function testGetOrderById()
    {
        $order = $this->_client->getOrder('480898112');
        $this::assertArrayHasKey('status', $order);
        $this::assertEquals($order['status'], 'success');
    }

    public function testDeliverOrderById()
    {
        $delivery = $this->_client->deliverOrder('480898112');
        $this::assertArrayHasKey('status', $delivery);
        $this::assertEquals($delivery['status'], 'success');
    }
}