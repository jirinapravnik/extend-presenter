# Rozšířování presenterů
[![Build Status](https://travis-ci.org/WebChemistry/ExtendPresenter.svg?branch=master)](https://travis-ci.org/WebChemistry/ExtendPresenter)

registrace:
```yaml
extensions:
	extendPresenter: WebChemistry\Application\DI\ExtendPresenterExtension
	mapping: '*ExtendPresenter' ## Výchozí nastavení
```

## Použití

Výchozí mapování: *ExtendPresenter. Když se aplikace pokouší nalézt BarPresenter, rozšíření hledá BarExtendPresenter, pokud nalezne a implementuje rozhrání IExtendPresenter vrátí tuto třídu, jinak výchozí.

```php
class FooPresenter extends BasePresenter {

}

class FooExtendPresenter extends FooPresenter implements IExtendPresenter {

}
```

Nalezeno a vrátí FooExtendPresenter místo FooPresenter. Příme volání FooExtendPresenter způsobí vyhození vyjímky.

```php
class FooPresenter extends BasePresenter {

}

class FooExtendPresenter extends FooPresenter {

}
```

Nenalezeno a vrátí FooPresenter. Přímé volání FooExtendPresenter povoleno.

## Specifické presentery

```yaml
extendPresneter:
	BarPresenter: OtherPresenter
```

```php
class BarPresenter extends BasePresenter {

}

class OtherPresenter extends BarPresenter implements IExtendPresenter {

}
```

Vrátí OtherPresenter místo BarPresenter. Přímé volání OtherPresenter zakázáno.
