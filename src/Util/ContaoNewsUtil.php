<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\Util;

use Contao\News;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class ContaoNewsUtil
{

    /**
     * @param NewsModel $news
     *
     * @return string
     */
    public static function getNewsURL(NewsModel $news): string
    {
        return News::generateNewsUrl($news);
    }

    /**
     * @param array $ids
     *
     * @return void
     */
    public static function prefetchNewsModels(array $ids): void
    {
        $archives = [];
        foreach (NewsModel::findMultipleByIds(array_values($ids)) as $news) {
            $archives[] = $news->pid;
        }

        $pages = [];
        foreach (NewsArchiveModel::findMultipleByIds($archives) as $archive) {
            $archive->jumpTo && $pages[] = $archive->jumpTo;
        }

        PageModel::findMultipleByIds($pages);
    }
}
