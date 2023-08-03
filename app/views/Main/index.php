<?php

use ishop\View;

/** @var $this View */

?>

<?php if (!empty($slides)): ?>
    <div class="container-fluid my-carousel">

        <?php
        $this->getPart(
            'parts/slider_loop',
            compact('slides')
        );
        ?>

    </div>
<?php endif; ?>

<?php if (!empty($products)): ?>
    <section class="featured-products">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="section-title"><?= getPhrase('main_index_featured_products') ?></h3>
                </div>

                <?php
                $this->getPart(
                    'parts/products_loop',
                    compact('products')
                );
                ?>

            </div>
        </div>
    </section>
<?php endif; ?>

<section class="services">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="section-title">
                    <?= getPhrase('main_index_our_advantages') ?>
                </h3>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="service-item">
                    <p class="text-center">
                        <i class="fas fa-shipping-fast"></i>
                    </p>
                    <p class="text-center">
                        <?= getPhrase('main_index_direct_deliveries') ?>
                    </p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="service-item">
                    <p class="text-center">
                        <i class="fas fa-cubes"></i>
                    </p>
                    <p class="text-center">
                        <?= getPhrase('main_index_wide_range') ?>
                    </p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="service-item">
                    <p class="text-center">
                        <i class="fas fa-hand-holding-usd"></i>
                    </p>
                    <p class="text-center">
                        <?= getPhrase('main_index_pleasant_prices') ?>
                    </p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="service-item">
                    <p class="text-center">
                        <i class="fas fa-user-cog"></i>
                    </p>
                    <p class="text-center">
                        <?= getPhrase('main_index_consultation') ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>