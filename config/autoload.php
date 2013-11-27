<?php

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'CommentsAjax'            => 'system/modules/comments_ajax/classes/CommentsAjax.php',

	// Modules
	'ModuleCommentsAjax'      => 'system/modules/comments_ajax/modules/ModuleCommentsAjax.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_comments_ajax' => 'system/modules/comments_ajax/templates'
));
