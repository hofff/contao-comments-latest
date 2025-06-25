<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\Util;

use Contao\Model\Collection;
use Contao\News;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;

final class ContaoNewsUtil
{
    public static function getNewsURL(NewsModel $news): string
    {
        return News::generateNewsUrl($news);
    }

    /** @param list<int> $ids */
    public static function prefetchNewsModels(array $ids): void
    {
        $archives   = [];
        $collection = NewsModel::findMultipleByIds($ids);
        if (! $collection instanceof Collection) {
            return;
        }

        foreach ($collection as $news) {
            $archives[] = $news->pid;
        }

        $collection = NewsArchiveModel::findMultipleByIds($archives);
        if (! $collection instanceof Collection) {
            return;
        }

        $pages = [];

        foreach ($collection as $archive) {
            if (! $archive->jumpTo) {
                continue;
            }

            $pages[] = $archive->jumpTo;
        }

        PageModel::findMultipleByIds($pages);
    }
}
