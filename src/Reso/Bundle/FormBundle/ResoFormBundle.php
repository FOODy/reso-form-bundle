<?php

namespace Reso\Bundle\FormBundle;

use Reso\Bundle\FormBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ResoFormBundle extends Bundle
{
	/**
	 * @param ContainerBuilder $container
	 */
	public function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new Compiler\EventSubscriberPass());
	}
}