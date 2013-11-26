<?php


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'ModuleCommentsAjax'      => 'system/modules/comments_ajax/modules/ModuleCommentsAjax.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
#	'com_default'      => 'system/modules/comments/templates/comments',
	'ce_comments'      => 'system/modules/comments_ajax/templates',
#	'mod_comment_form' => 'system/modules/comments/templates/modules',
));
