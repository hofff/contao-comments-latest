<?php

$GLOBALS['FE_MOD']['application']['hofff_comments_latest']
	= 'Hofff\\Contao\\CommentsLatest\\Frontend\\ModuleCommentsLatest';

$GLOBALS['TL_HOOKS']['hofff_comments_compile'][]
	= [ 'Hofff\\Contao\\CommentsLatest\\Hooks', 'hookCommentsCompilePage' ];
$GLOBALS['TL_HOOKS']['hofff_comments_compile'][]
	= [ 'Hofff\\Contao\\CommentsLatest\\Hooks', 'hookCommentsCompileContent' ];
$GLOBALS['TL_HOOKS']['hofff_comments_compile'][]
	= [ 'Hofff\\Contao\\CommentsLatest\\Hooks', 'hookCommentsCompileNews' ];
