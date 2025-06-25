<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\Util;

use Contao\Model\Collection;
use Contao\News;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;

use function assert;

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
            assert($news instanceof NewsModel);

            $archives[] = $news->pid;
        }

        $collection = NewsArchiveModel::findMultipleByIds($archives);
        if (! $collection instanceof Collection) {
            return;
        }

        $pages = [];

        foreach ($collection as $archive) {
            assert($archive instanceof NewsArchiveModel);

            if (! $archive->jumpTo) {
                continue;
            }

            $pages[] = $archive->jumpTo;
        }

        PageModel::findMultipleByIds($pages);
    }
}
