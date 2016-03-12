<?php

namespace WebChemistry\Application\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use WebChemistry\Application\PresenterFactoryCallback;

class ExtendPresenterExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'mapping' => PresenterFactoryCallback::DEFAULT_MAPPING,
		'extra' => []
	];

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$def = $builder->getDefinition($builder->getByType('Nette\Application\IPresenterFactory'));
		/** @var Statement $factory */
		$factory = $def->getFactory()->arguments[0];
		$factory->setEntity('WebChemistry\Application\PresenterFactoryCallback');
		$factory->arguments[] = $config['mapping'];
		$factory->arguments[] = $config['extra'];
	}

}
