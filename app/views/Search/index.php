<?php
/** @var $this View */
/** @var $products array */
/** @var $total int */
/** @var $pagination Pagination */
/** @var $searchQuery string */

use ishop\Pagination;
use ishop\View;

?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light p-2">
            <li class="breadcrumb-item">
                <a href="<?= getLangCorrectBaseUrl() ?>">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="breadcrumb-item">
                <?= getPhrase('search_index_search_title') ?>
            </li>
        </ol>
    </nav>
</div>

<div class="container py-3">
    <div class="row">

        <div class="col-lg-12 category-content">
            <h1 class="section-title">
                <?= getPhrase('search_index_search_title'); ?>
            </h1>

            <h4>
                <?= getPhrase('search_index_search_query') . h($searchQuery) ?>
            </h4>

            <div class="row">
                <?php if (!empty($products)): ?>
                    <?php $this->getPart('parts/products_loop', compact('products')); ?>

                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <?= count($products) ?>
                                <?= getPhrase('tpl_total_pagination') ?>
                                <?= $total ?>
                            </p>
                            <?php if ($pagination->countPages > 1): ?>
                                <?= $pagination ?>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <p>
                        <?= getPhrase('search_index_not_found') ?>
                    </p>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>
