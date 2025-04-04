<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Neonkit;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Nette\DI\Compiler;
use Nettrine\Cache\DI\CacheExtension;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// Adapter as statement
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.cache', new CacheExtension());
			$compiler->addConfig(Neonkit::load('
				nettrine.cache:
					adapter: Symfony\Component\Cache\Adapter\NullAdapter()
			'));
			$compiler->addDependencies([__FILE__]);
		})
		->build();

	Assert::type(DoctrineProvider::class, $container->getByType(Cache::class));
	Assert::type(NullAdapter::class, $container->getService('nettrine.cache.driver')->getPool());
});

// Adapter as service
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.cache', new CacheExtension());
			$compiler->addConfig(Neonkit::load('
				nettrine.cache:
					adapter: @adapter

				services:
					adapter: Symfony\Component\Cache\Adapter\NullAdapter()
			'));
			$compiler->addDependencies([__FILE__]);
		})
		->build();

	Assert::type(DoctrineProvider::class, $container->getByType(Cache::class));
	Assert::type(NullAdapter::class, $container->getService('nettrine.cache.driver')->getPool());
});
