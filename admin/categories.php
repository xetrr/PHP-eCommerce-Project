<?php
// template page

//ob_start();

session_start();

$pageTitle = 'Categories';

if (isset($_SESSION['Username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    if ($do == 'Manage') {

        $sort = 'DESC';
        $sort_array = ['ASC', 'DESC'];
        if (isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {
            $sort = $_GET['sort'];
        }
        $stmt2 = $con->prepare("SELECT * FROM categories ORDER BY ordering $sort");
        $stmt2->execute();
        $cats = $stmt2->fetchAll();
        if (!empty($cats)) { ?>
            <h1 class="text-center">Manage Categories</h1>
            <div class="container category">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Manage Categories
                        <div class="ordering">
                            <a href="?do=Add" class="btn btn-sm btn-info">Add new category</a>
                            <a href="?sort=ASC" class="btn btn-sm btn-outline-secondary">ASC</a>
                            <a href="?sort=DESC" class="btn btn-sm btn-outline-secondary">DESC</a>
                        </div>
                    </div>
                    <div class="card-body cat">
                        <?php foreach ($cats as $cat): ?>
                            <div class="in-card card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3><?= $cat['name'] ?: 'unknown' ?></h3>

                                            <div class="full-card">
                                                <p class="mb-2"><?= $cat['description'] ?></p>

                                                <div class="btn-group hidden-buttons" role="group">
                                                    <a href="categories.php?do=Edit&catid=<?= $cat['catid'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="categories.php?do=delete&catid=<?= $cat['catid'] ?>" class="btn btn-sm btn-danger">Delete</a>
                                                </div>

                                                <div class="op-badges">
                                                    <?php if ($cat['visibility'] == 1): ?>
                                                        <span class="pg-hidden me-2">Hidden</span>
                                                    <?php endif; ?>
                                                    <?php if ($cat['allow_comments'] == 1): ?>
                                                        <span class="pg-comment me-2">Comments disabled</span>
                                                    <?php endif; ?>
                                                    <?php if ($cat['active_ads'] == 1): ?>
                                                        <span class="pg-ads">Ads disabled</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="container">

            </div>
        <?php } else {
            echo '<div class="container">';
            echo '<div class="nice-message">There\'s No Categories To Show</div>';
            echo '<a href="categories.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> New Category
						</a>';
            echo '</div>';
        }
        ?>

    <?php
    } elseif ($do == 'Add') { ?>
        <h1 class="text-center">Add New Category</h1>
        <div class="container-md mx-auto">
            <form action="?do=Insert" method="POST" class="form-horizontal ">
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="name" id="" class="form-control" autocomplete="off" required="required">
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2 password-field">
                    <label for="" class="col-sm-1 col-form-label ">Description</label>
                    <div class="eye-div col-sm-10  col-md-6">
                        <input type="text" name="desc" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label ">Order</label>
                    <div class="col-sm-10  col-md-6">
                        <input type="text" name="ordering" id="" class="form-control">
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label ">Visibility</label>
                    <div class="col-sm-10 col-md-6">
                        <div>
                            <input type="radio" name="visibility" id="vis-yes" value="0" checked>
                            <label for="vis-yes">Yes</label>
                        </div>
                        <div>
                            <input type="radio" name="visibility" id="vis-no" value="1">
                            <label for="vis-no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label ">Allow commenting</label>
                    <div class="col-sm-10 col-md-6">
                        <div>
                            <input type="radio" name="comment" id="com-yes" value="0" checked>
                            <label for="com-yes">Yes</label>
                        </div>
                        <div>
                            <input type="radio" name="comment" id="com-no" value="1">
                            <label for="com-no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label ">Ads</label>
                    <div class="col-sm-10 col-md-6">
                        <div>
                            <input type="radio" name="Ads" id="Ads-yes" value="0" checked>
                            <label for="Ads-yes">Yes</label>
                        </div>
                        <div>
                            <input type="radio" name="Ads" id="Ads-no" value="1">
                            <label for="Ads-no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center">
                    <div class="col-sm-offset-2 col-sm-10 col-md-6">
                        <input type="submit" value="Add Category" id="" class="btn btn-primary mt-2">
                    </div>
                </div>

            </form>
        </div>



        <?php } elseif ($do == 'Insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo "<h1 class='text-center'>New Category</h1>";
            echo "<div class='container'>";
            $name = $_POST['name'];
            $desc = $_POST['desc'];
            $order = $_POST['ordering'];
            $visibility = $_POST['visibility'];
            $comment = $_POST['comment'];
            $ads = $_POST['Ads'];

            $check = checkItem('name', 'categories', $name);
            echo $check;

            if ($check == 1) {
                $userMsg =
                    "<div class='alert alert-danger'>" .
                    'sorry this category already exists' .
                    '</div>';
                redirectHome($userMsg, 'back');
            } else {
                $stmt = $con->prepare("INSERT INTO 
            categories(name , description , ordering , visibility , allow_comments , active_ads)
            VALUES(:name , :desc ,  :order , :visibile , :comments, :ads)");
                $stmt->execute([
                    'name' => $name,
                    'desc' => $desc,
                    'order' => $order,
                    'visibile' => $visibility,
                    'comments' => $comment,
                    'ads' => $ads,
                ]);
                $usersmg =
                    $stmt->rowCount() . ' record inserted and you will be redirected to home';
                //redirectHome($usersmg, '');
            }
        } else {
            $errorMsg = 'sorry you are not allowed to see this content';
            redirectHome($errorMsg, 2);
            echo '</div>';
        }
    } elseif ($do == 'Edit') {
        $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : '0';
        $stmt = $con->prepare('SELECT * FROM categories WHERE catid = ?');
        $stmt->execute([$catid]);
        $cat = $stmt->fetch();
        $count = $stmt->rowCount();
        if ($stmt->rowCount() > 0) { ?>
            <h1 class="text-center">Edit Category</h1>
            <div class="container">
                <form action="?do=update" method="POST" class="form-horizontal">
                    <input type="hidden" name="catid" value="<?php echo $catid; ?>">
                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Category Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name" id="" class="form-control" value="<?php echo $cat['name']; ?>" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="desc" id="" class="form-control" value="<?php echo $cat['description']; ?>" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Order</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="ordering" id="" value="<?php echo $cat['ordering']; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label ">Visibility</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input type="radio" name="visibility" id="vis-yes" value="0" <?php if (
                                                                                                    !$cat['visibility']
                                                                                                ) {
                                                                                                    echo 'checked';
                                                                                                } ?>>
                                <label for="vis-yes">Yes</label>
                            </div>
                            <div>
                                <input type="radio" name="visibility" id="vis-no" value="1" <?php if (
                                                                                                $cat['visibility']
                                                                                            ) {
                                                                                                echo 'checked';
                                                                                            } ?>>
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label ">Comments</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input type="radio" name="comments" id="vis-yes" value="0" <?php if (
                                                                                                !$cat['allow_comments']
                                                                                            ) {
                                                                                                echo 'checked';
                                                                                            } ?>>
                                <label for="com-yes">Yes</label>
                            </div>
                            <div>
                                <input type="radio" name="comments" id="vis-no" value="1" <?php if (
                                                                                                $cat['allow_comments']
                                                                                            ) {
                                                                                                echo 'checked';
                                                                                            } ?>>
                                <label for="com-no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label ">Ads</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input type="radio" name="ads" id="ads-yes" value="0" <?php if (
                                                                                            !$cat['active_ads']
                                                                                        ) {
                                                                                            echo 'checked';
                                                                                        } ?>>
                                <label for="ads-yes">Yes</label>
                            </div>
                            <div>
                                <input type="radio" name="ads" id="ads-no" value="1" <?php if (
                                                                                            $cat['active_ads']
                                                                                        ) {
                                                                                            echo 'checked';
                                                                                        } ?>>
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                    </div>



                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10 col-md-6">
                            <input type="submit" value="update" id="" class="btn btn-primary btn-lg">
                        </div>
                    </div>

                </form>
            </div>

<?php } else {
            echo 'no Id found';
        }
    } elseif ($do == 'update') {
        echo "<h1 class='text-center'>update Category</h1>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $catid = $_POST['catid'];
            $catname = $_POST['name'];
            $desc = $_POST['desc'];
            $ordering = $_POST['ordering'];
            $visiblity = $_POST['visibility'];
            $comments = $_POST['comments'];
            $ads = $_POST['ads'];

            $stmt = $con->prepare(
                'UPDATE categories SET name = ?, description =? , ordering =? , visibility = ? , allow_comments=? , active_ads = ? WHERE catid =?',
            );

            $stmt->execute([$catname, $desc, $ordering, $visiblity, $comments, $ads, $catid]);
            $userMsg = $catname . ' Category updated';
            redirectHome($userMsg, 'categories.php');
        } else {
            echo 'sorry you are not allowed to see this content';
        }
    } elseif ($do == 'delete') {
        $catid = $_GET['catid'] && is_numeric($_GET['catid']) ? intval($_GET['catid']) : '0';
        $stmt = $con->prepare('DELETE FROM categories WHERE catid=?');
        $stmt->execute([$catid]);
        $userMsg = 'category deleted';
        redirectHome($userMsg, 'categories.php');
    }

    include $tpl . 'footer.php';
} else {
    header('location: index.php');
    exit();
}
//ob_end_flush();
