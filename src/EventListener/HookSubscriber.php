<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\EventListener;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\CommentsLatest\Util\ContaoNewsUtil;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class HookSubscriber
{

    /**
     * @param array $items
     *
     * @return array
     */
    public function hookCommentsCompilePage(array $items): array
    {
        $compiled = [];

        foreach ($items as $item) {
            if ($item['comment']->source != 'tl_page') {
                $compiled[] = $item;
                continue;
            }

            try {
                $page = PageModel::findById($item['comment']->parent);

                $item['href']  = $page->getFrontendUrl();
                $item['title'] = $page->pageTitle ?: $page->title;
            } catch (\Throwable $e) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }

    /**
     * @param array $items
     *
     * @return array
     */
    public function hookCommentsCompileContent(array $items): array
    {
        $compiled = [];

        foreach ($items as $item) {
            if ($item['comment']->source != 'tl_content') {
                $compiled[] = $item;
                continue;
            }

            try {
                $content = ContentModel::findById($item['comment']->parent);
                $article = ArticleModel::findById($content->pid);
                $page    = PageModel::findById($article->pid);

                $item['href']  = $page->getFrontendUrl();
                $item['title'] = $page->pageTitle ?: $page->title;
            } catch (\Throwable $e) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }

    /**
     * @param array $items
     *
     * @return array
     */
    public function hookCommentsCompileNews(array $items): array
    {
        $compiled = [];

        foreach ($items as $item) {
            if ($item['comment']->source != 'tl_news') {
                $compiled[] = $item;
                continue;
            }

            try {
                $news = NewsModel::findById($item['comment']->parent);

                $item['href']  = ContaoNewsUtil::getNewsURL($news);
                $item['title'] = $news->headline;
            } catch (\Throwable $e) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }
}
