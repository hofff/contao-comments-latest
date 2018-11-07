<?php

declare(strict_types=1);

use Hofff\Contao\CommentsLatest\EventListener\HookSubscriber;
use Hofff\Contao\CommentsLatest\Frontend\ModuleCommentsLatest;

$GLOBALS['FE_MOD']['application']['hofff_comments_latest'] = ModuleCommentsLatest::class;

$GLOBALS['TL_HOOKS']['hofff_comments_compile'][] = [HookSubscriber::class, 'hookCommentsCompilePage'];
$GLOBALS['TL_HOOKS']['hofff_comments_compile'][] = [HookSubscriber::class, 'hookCommentsCompileContent'];
$GLOBALS['TL_HOOKS']['hofff_comments_compile'][] = [HookSubscriber::class, 'hookCommentsCompileNews'];
