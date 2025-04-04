<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Neonkit;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Nette\DI\Compiler;
use Nettrine\Cache\DI\CacheExtension;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

if (!function_exists('apcu_exists')) {
	Environment::skip('Autoselect driver unreachable when apcu is not available');
}

Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.cache', new CacheExtension());
			$compiler->addConfig(Neonkit::load('
				nettrine.cache:
			'));
		})
		->build();

	Assert::type(DoctrineProvider::class, $container->getByType(Cache::class));
	Assert::type(ApcuAdapter::class, $container->getService('nettrine.cache.driver')->getPool());
});
