<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\ContaoManager;

use Contao\CommentsBundle\ContaoCommentsBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Hofff\Contao\CommentsLatest\HofffContaoCommentsLatestBundle;
use Override;

final class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HofffContaoCommentsLatestBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, ContaoCommentsBundle::class])
                ->setReplace(['hofff_comments_latest']),
        ];
    }
}
