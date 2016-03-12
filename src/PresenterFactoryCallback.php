<?php

namespace WebChemistry\Application;

use Nette;
use Nette\Application\InvalidPresenterException;
use Nette\Bridges\ApplicationDI\PresenterFactoryCallback as NettePresenterFactoryCallback;

class PresenterFactoryCallback extends NettePresenterFactoryCallback {

	const DEFAULT_MAPPING = '*ExtendPresenter';
	const SCAN_FILTER = '~(.+)Presenter$~';

	/** @var string */
	private $mapping = self::DEFAULT_MAPPING;

	/** @var array */
	private $extra = [];

	public function __construct(Nette\DI\Container $container, $invalidLinkMode, $touchToRefresh, $mapping = NULL,
								array $extra = []) {
		parent::__construct($container, $invalidLinkMode, $touchToRefresh);
		$this->extra = $extra;
		if ($mapping) {
			$this->mapping = $mapping;
		}
	}

	/**
	 * @param string $class
	 * @return Nette\Application\IPresenter
	 * @throws InvalidPresenterException
	 */
	public function __invoke($class) {
		if (isset($this->extra[$class])) {
			$extendPresenter = $this->extra[$class];
		} else if (preg_match(self::SCAN_FILTER, $class, $matches)) {
			$extendPresenter = str_replace('*', $matches[1], $this->mapping);
		}

		if (isset($extendPresenter) && class_exists($extendPresenter)) {
			$extend = parent::__invoke($extendPresenter);
			if ($extend instanceof IExtendPresenter) {
				return $extend;
			} else if (isset($this->extra[$class])) {
				throw new InvalidPresenterException("Presenter '$extendPresenter' must implements interface WebChemistry\\Application\\IExtendPresenter.");
			}
		}

		if (($presenter = parent::__invoke($class)) instanceof IExtendPresenter) {
			throw new InvalidPresenterException("Cannot load presenter '$class', because extends other presenter.");
		}

		return $presenter;
	}

}
