<?php

class PresenterTest extends \Codeception\TestCase\Test {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/** @var \WebChemistry\Application\PresenterFactory */
	protected $presenterFactory;

	protected function _before() {
		$container = new ContainerMock();
		$presenterFactory = new \WebChemistry\Application\PresenterFactory(new \Nette\Bridges\ApplicationDI\PresenterFactoryCallback(
			$container, FALSE, NULL
		), NULL, [
			'Foo3Presenter' => 'Bar3Presenter',
		]);
		$this->presenterFactory = $presenterFactory;
	}

	protected function _after() {
	}

	public function testCreateNormal() {
		$this->assertInstanceOf('Foo1Presenter', $this->presenterFactory->createPresenter('Foo1'));
	}

	public function testCreateExtend() {
		$this->assertInstanceOf('Foo2Presenter', $this->presenterFactory->createPresenter('Foo2'));
		$this->assertInstanceOf('Foo2ExtendPresenter', $this->presenterFactory->createPresenter('Foo2'));
	}

	public function testCallExtendPresenter() {
		try {
			$this->presenterFactory->createPresenter('Foo2Extend');
			$this->fail('CallExtendPresenter: PresenterFactory not throws exception.');
		} catch (\Exception $e) {
			$this->assertInstanceOf('Nette\Application\InvalidPresenterException', $e);
			$this->assertSame('Cannot load presenter \'Foo2ExtendPresenter\', because extends other presenter.',
				$e->getMessage());
		}
	}

	public function testExtra() {
		$presenter = $this->presenterFactory->createPresenter('Foo3');
		$this->assertInstanceOf('Foo3Presenter', $presenter);
		$this->assertInstanceOf('Bar3Presenter', $presenter);
	}

	public function testCustomMapping() {
		$presenterFactory = new \WebChemistry\Application\PresenterFactory(new \Nette\Bridges\ApplicationDI\PresenterFactoryCallback(
			new ContainerMock(), FALSE, NULL
		), '*CustomPresenter');
		$presenter = $presenterFactory->createPresenter('Foo3');
		$this->assertInstanceOf('Foo3Presenter', $presenter);
		$this->assertInstanceOf('Foo3CustomPresenter', $presenter);
	}

	public function testExtraWithoutInterface() {
		$presenterFactory = new \WebChemistry\Application\PresenterFactory(new \Nette\Bridges\ApplicationDI\PresenterFactoryCallback(
			new ContainerMock(), FALSE, NULL
		), NULL, [
			'Foo3Presenter' => 'Extra3Presenter',
		]);
		try {
			$presenterFactory->createPresenter('Foo3');
			$this->fail('ExtraWithoutInterface must throws exception.');
		} catch (\Exception $e) {
			$this->assertInstanceOf('Nette\Application\InvalidPresenterException', $e);
			$this->assertSame('Presenter \'Extra3Presenter\' must implements interface WebChemistry\Application\IExtendPresenter.', $e->getMessage());
		}
	}

}

class ContainerMock extends \Nette\DI\Container {

	public function __construct(array $params = []) {
		parent::__construct($params);
		$this->meta[self::TAGS]['nette.presenter'] = [
			'presenter1' => 'Foo1Presenter',
			'presenter2' => 'Foo2Presenter',
			'presenter3' => 'Foo3Presenter',
			'presenter1Extend' => 'Foo1ExtendPresenter',
			'presenter2Extend' => 'Foo2ExtendPresenter',
			'presenterBar3' => 'Bar3Presenter',
			'customPresenter' => 'Foo3CustomPresenter',
			'extraPresenter' => 'Extra3Presenter'
		];
	}

	public function createServicePresenter1() {
		return new Foo1Presenter();
	}

	public function createServicePresenter2() {
		return new Foo2Presenter();
	}

	public function createServicePresenter3() {
		return new Foo3Presenter();
	}

	public function createServicePresenterBar3() {
		return new Bar3Presenter();
	}

	public function createServicePresenter2Extend() {
		return new Foo2ExtendPresenter();
	}

	public function createServicePresenter1Extend() {
		return new Foo1ExtendPresenter();
	}

	public function createServiceCustomPresenter() {
		return new Foo3CustomPresenter();
	}

	public function createServiceExtraPresenter() {
		return new Extra3Presenter();
	}

}

class Foo1Presenter extends \Nette\Application\UI\Presenter {

}

class Foo1ExtendPresenter extends \Nette\Application\UI\Presenter {

}

class Foo2Presenter extends \Nette\Application\UI\Presenter {

}

class Foo2ExtendPresenter extends Foo2Presenter implements \WebChemistry\Application\IExtendPresenter {

}

class Foo3Presenter extends \Nette\Application\UI\Presenter {

}

class Bar3Presenter extends Foo3Presenter implements \WebChemistry\Application\IExtendPresenter {

}

class Foo3CustomPresenter extends Foo3Presenter implements \WebChemistry\Application\IExtendPresenter {

}

class Extra3Presenter extends Foo3Presenter {

}