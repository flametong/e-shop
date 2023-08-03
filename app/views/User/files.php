<?php

/** @var $pagination Pagination */

/** @var $total int */

use ishop\Pagination;

?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light p-2">
            <li class="breadcrumb-item">
                <a href="./">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="user/account">
                    <?= getPhrase('tpl_account') ?>
                </a>
            </li>
            <li class="breadcrumb-item active">
                <?= getPhrase('user_files_title') ?>
            </li>
        </ol>
    </nav>
</div>

<div class="container py-3">
    <div class="row">

        <div class="col-12">
            <h1 class="section-title">
                <?= getPhrase('user_files_title') ?>
            </h1>
        </div>

        <?php $this->getPart('parts/account_sidebar'); ?>

        <div class="col-md-9 order-md-1">

            <?php if (!empty($files)): ?>

                <div class="table-responsive">
                    <table class="table text-start table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">
                                <?= getPhrase('user_files_num_order') ?>
                            </th>
                            <th scope="col">
                                <?= getPhrase('user_files_name') ?>
                            </th>
                            <th scope="col">
                                <?= getPhrase('user_files_download') ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files as $file): ?>
                            <?php if (is_array($file)): ?>
                                <tr>
                                    <td>

                                        <a href="user/order?id=<?= $file['order_id'] ?>">
                                            <?= $file['order_id'] ?>
                                        </a>

                                    </td>
                                    <td>
                                        <?= $file['name'] ?>
                                    </td>
                                    <td>
                                        <a href="user/download?id=<?= $file['id'] ?>">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p><?= count($files) ?> <?= getPhrase('user_files_total_pagination') ?> <?= $total ?></p>
                        <?php if ($pagination->countPages > 1): ?>
                            <?= $pagination ?>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <p><?= getPhrase('user_files_empty') ?></p>
            <?php endif; ?>

        </div>
    </div>
</div>

