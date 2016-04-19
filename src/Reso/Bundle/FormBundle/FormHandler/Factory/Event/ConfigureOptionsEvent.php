<?php

namespace Reso\Bundle\FormBundle\FormHandler\Factory\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigureOptionsEvent extends Event
{
	/**
	 * @var OptionsResolver
	 */
	private $optionsResolver;

	/**
	 * Constructor.
	 *
	 * @param OptionsResolver $optionsResolver
	 */
	public function __construct(OptionsResolver $optionsResolver)
	{
		$this->optionsResolver = $optionsResolver;
	}

	/**
	 * @return OptionsResolver
	 */
	public function getOptionsResolver()
	{
		return $this->optionsResolver;
	}
}