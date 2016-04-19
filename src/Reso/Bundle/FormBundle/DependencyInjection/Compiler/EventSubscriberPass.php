<?php

namespace Reso\Bundle\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventSubscriberPass implements CompilerPassInterface
{
	/**
	 * You can modify the container here before it is dumped to PHP code.
	 *
	 * @param ContainerBuilder $container
	 */
	public function process(ContainerBuilder $container)
	{
		$definition = $container->getDefinition('rs.form_handler.factory');

		foreach ($container->findTaggedServiceIds('rs.form_handler.event_subscriber') as $serviceId => $tags) {
			if (!$container->getDefinition($serviceId)->isPublic()) {
				throw new \InvalidArgumentException(sprintf('FormHandler event subscriber service "%s" must be public.', $serviceId));
			}

			$definition->addMethodCall('addHandlerEventSubscriberService', [$serviceId]);
		}
	}
}