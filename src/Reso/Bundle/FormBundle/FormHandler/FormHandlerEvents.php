<?php

namespace Reso\Bundle\FormBundle\FormHandler;

use Symfony\Component\Form\FormEvents;

final class FormHandlerEvents
{
	const PRE_SET_DATA = FormEvents::PRE_SET_DATA;
	const POST_SET_DATA = FormEvents::POST_SET_DATA;
	const PRE_SUBMIT = FormEvents::PRE_SUBMIT;
	const POST_SUBMIT = FormEvents::POST_SUBMIT;
	const PRE_FLUSH = 'gc.form_handler.pre_flush';
	const POST_FLUSH = 'gc.form_handler.post_flush';
	const PRE_SERIALIZE = 'gc.form_handler.pre_serialize';

	private function __construct()
	{
	}
}