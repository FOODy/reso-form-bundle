<?php

namespace Reso\Bundle\FormBundle\FormHandler\Factory;

final class FormHandlerFactoryEvents
{
	const CONFIGURE_OPTIONS = 'rs.form_handler.factory.configure_options';
	const PRE_CREATE_HANDLER = 'rs.form_handler.factory.pre_create_handler';
	const POST_CREATE_HANDLER = 'rs.form_handler.factory.post_create_handler';

	private function __construct()
	{
	}
}