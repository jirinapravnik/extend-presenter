<?php

namespace WebChemistry\Application\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use WebChemistry\Application\PresenterFactory;

class ExtendPresenterExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'mapping' => PresenterFactory::DEFAULT_MAPPING,
		'extra' => []
	];

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$def = $builder->getDefinition($builder->getByType('Nette\Application\IPresenterFactory'));
		$original = $def->getFactory();
		$original->arguments[] = $config['mapping'];
		$original->arguments[] = $config['extra'];
		$def->setFactory('WebChemistry\Application\PresenterFactory', $original->arguments);
	}

}
