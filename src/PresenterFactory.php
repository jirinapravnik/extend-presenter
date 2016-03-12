<?php

namespace WebChemistry\Application;

use Nette\Application\PresenterFactory as NettePresenterFactory;
use Nette\Application\InvalidPresenterException;
use WebChemistry\Application\IExtendPresenter;

class PresenterFactory extends NettePresenterFactory {

	const DEFAULT_MAPPING = '*ExtendPresenter';
	const SCAN_FILTER = '~(.+)Presenter$~';

	/** @var array */
	private $extra = [];

	/** @var string */
	private $mapping = self::DEFAULT_MAPPING;

	/**
	 * @param callable $factory
	 * @param string $mapping
	 * @param array $extra
	 */
	public function __construct($factory, $mapping = NULL, array $extra = []) {
		parent::__construct($factory);
		$this->extra = $extra;
		if ($mapping) {
			$this->mapping = $mapping;
		}
	}

	/**
	 * @param string $name
	 * @return string
	 * @throws InvalidPresenterException
	 */
	public function getPresenterClass(& $name) {
		$class = parent::getPresenterClass($name);

		if (isset($this->extra[$class])) {
			$extendPresenter = $this->extra[$class];
		} else if (preg_match(self::SCAN_FILTER, $class, $matches)) {
			$extendPresenter = str_replace('*', $matches[1], $this->mapping);
		}

		if (isset($extendPresenter) && class_exists($extendPresenter)) {
			$implements = class_implements($extendPresenter);
			if (isset($implements['WebChemistry\Application\IExtendPresenter'])) {
				return $extendPresenter;
			} else if (isset($this->extra[$class])) {
				throw new InvalidPresenterException("Presenter '$extendPresenter' must implements interface WebChemistry\\Application\\IExtendPresenter.");
			}
		}

		$implements = class_implements($class);
		if (isset($implements['WebChemistry\Application\IExtendPresenter'])) {
			throw new InvalidPresenterException("Cannot load presenter '$class', because extends other presenter.");
		}

		return $class;
	}

}
