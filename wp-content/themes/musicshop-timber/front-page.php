<?php

$context = Timber::context();

$context['post'] = new Timber\Post();
Timber::render(array('views/front-page.twig', 'page.twig'), $context);
