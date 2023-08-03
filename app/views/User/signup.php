<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light p-2">
            <li class="breadcrumb-item">
                <a href="./">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="breadcrumb-item active">
                <?= getPhrase('tpl_signup') ?>
            </li>
        </ol>
    </nav>
</div>

<div class="container py-3">
    <div class="row">

        <div class="col-lg-12 category-content">
            <h1 class="section-title">
                <?= getPhrase('tpl_signup') ?>
            </h1>

            <form class="row g-3" method="post">

                <div class="col-md-6 offset-md-3">
                    <div class="form-floating mb-3">
                        <input
                                type="email"
                                name="email"
                                value="<?= getFieldValue('email') ?>"
                                class="form-control"
                                id="email"
                                placeholder="name@example.com"
                                required
                        >
                        <label class="required" for="email">
                            <?= getPhrase('tpl_signup_email_input'); ?>
                        </label>
                    </div>
                </div>

                <div class="col-md-6 offset-md-3">
                    <div class="form-floating mb-3">
                        <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="password"
                                placeholder="password"
                                required
                        >
                        <label class="required" for="password">
                            <?= getPhrase('tpl_signup_password_input') ?>
                        </label>
                    </div>
                </div>

                <div class="col-md-6 offset-md-3">
                    <div class="form-floating mb-3">
                        <input
                                type="text"
                                name="name"
                                value="<?= getFieldValue('name') ?>"
                                class="form-control"
                                id="name"
                                placeholder="Name"
                                required
                        >
                        <label class="required" for="name">
                            <?= getPhrase('tpl_signup_name_input') ?>
                        </label>
                    </div>
                </div>

                <div class="col-md-6 offset-md-3">
                    <div class="form-floating mb-3">
                        <input
                                type="text"
                                name="address"
                                value="<?= getFieldValue('address') ?>"
                                class="form-control"
                                id="address"
                                placeholder="Address"
                                required
                        >
                        <label class="required" for="address">
                            <?= getPhrase('tpl_signup_address_input') ?>
                        </label>
                    </div>
                </div>

                <div class="col-md-6 offset-md-3">
                    <button type="submit" class="btn btn-danger">
                        <?= getPhrase('user_signup_signup_btn') ?>
                    </button>
                </div>

            </form>

            <?php
            if (isset($_SESSION['form_data'])) {
                unset($_SESSION['form_data']);
            }
            ?>

        </div>
    </div>
</div>