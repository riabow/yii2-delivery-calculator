<?php
/**
 * Created by PhpStorm.
 * User:    Евегний Емельянов <e9139905539@gmail.com>
 */

namespace uranum\delivery\tests\unit;


use Codeception\Test\Unit;
use uranum\delivery\DeliveryCargoData;
use uranum\delivery\services\CourierDelivery;
use uranum\delivery\services\PickupDelivery;


class DeliveryServiceTest extends Unit
{
	private $data;
	
	public function setUp()
	{
		$this->data = new DeliveryCargoData(659300, 'Бийск', 2000, 1000, 275);
	}
	
	public function testCreateService()
	{
		$pickup = new PickupDelivery($this->data, 'pickup');
		$this->assertInstanceOf('uranum\delivery\services\PickupDelivery', $pickup);
	}
	
	public function testRightCalculation()
	{
		$pickup = new PickupDelivery($this->data, 'pickup');
		$pickup->calculate();
		$result = $pickup->getResult();
		$this->assertEquals([
			'name'  => 'Самовывоз',
			'terms' => false,
			'cost'  => 0,
			'info'  => 'Самостоятельно забираете в офисе.',
		], $result);
	}
	
	public function testCantDeliveryInAnotherCity()
	{
		$pickup = new CourierDelivery($this->data, 'courier');
		$pickup->calculate();
		$result = $pickup->getResult();
		$this->assertEquals(false, $result);
	}
	
	public function testCanDeliveryInSameCity()
	{
		$pickup = new CourierDelivery(new DeliveryCargoData(659300, 'Новосибирск', 2000, 1000, 275), 'courier');
		$pickup->calculate();
		$result = $pickup->getResult();
		$this->assertEquals([
			'name'  => 'Курьер',
			'terms' => '1 день',
			'cost'  => 200,
			'info'  => '',
		], $result);
	}
}