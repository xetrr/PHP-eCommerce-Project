<?php
ob_start();
session_start();
$pageTitle = 'items';


if (isset($_SESSION['Username'])) {
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if ($do == 'Manage') {

        $stmt = $con->prepare("SELECT items.*,
                                categories.name AS category_name,
                                users.Username AS user_name
                                FROM items
                                INNER JOIN categories
                                ON catid = cat_id
                                INNER JOIN users
                                ON user_id = member_id
        ");
        $stmt->execute();
        $items = $stmt->fetchAll();
?>
        <h1 class="text-center">Manage Items</h1>
        <div class="container">
            <div class="table-responsive">
                <table class="table table-bordered main-table text-center">
                    <tr>
                        <td>#ID</td>
                        <td>Item Name</td>
                        <td>Description</td>
                        <td>Price</td>
                        <td>Made In</td>
                        <td>Adding Date</td>
                        <td>Category</td>
                        <td>Member</td>
                        <td>Control</td>
                    </tr>
                    <?php foreach ($items as $item) {
                        echo '<tr>';
                        echo '<td>' . $item['item_id'] . '</td>';
                        echo '<td>' . $item['name'] . '</td>';
                        echo '<td>' . $item['description'] . '</td>';
                        echo '<td>' . $item['price'] . '</td>';
                        echo '<td>' . $item['country_made'] . '</td>';
                        echo '<td>' . $item['add_date'] . '</td>';
                        echo '<td>' . $item['category_name'] . '</td>';
                        echo '<td>' . $item['user_name'] . '</td>';

                        echo "<td> <a href='items.php?do=Edit&item_id=" .
                            $item['item_id'] .
                            "' class='btn btn-success'>Edit</a>";
                        echo " <a href='items.php?do=delete&item_id=" .
                            $item['item_id'] .
                            "' class='btn btn-danger confirm'>Delete</a>";
                        if ($item['Approve'] == 0) {
                            echo " <a href='items.php?do=Approve&item_id=" .
                                $item['item_id'] .
                                "' class='btn btn-info'>Approve</a></td>";
                        }
                        echo '</tr>';
                    } ?>
                </table>
            </div>
            <a href="items.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add new item </a>
        </div>
    <?php
    } elseif ($do == 'Add') { ?>
        <h1 class="text-center">Add New Item</h1>
        <div class="container-md mx-auto">
            <form action="?do=insert" method="POST" class="form-horizontal ">
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Item Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="name" id="" class="form-control" required="required">
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Description</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="desc" id="" class="form-control"
                            required="required">
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Price</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="price" id="" class="form-control"
                            required="required">
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Made In</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="country" id="" class="form-control"
                            required="required">
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Status</label>
                    <div class="col-sm-10 col-md-6">
                        <select class="form-control" name="status" id="">
                            <option value="0">...</option>
                            <option value="1">New</option>
                            <option value="2">Like New</option>
                            <option value="3">Used</option>
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Member</label>
                    <div class="col-sm-10 col-md-6">
                        <select class="form-control" name="member" id="">
                            <option value="0">...</option>
                            <?php
                            $stmt = $con->prepare("SELECT * FROM users");
                            $stmt->execute();
                            $users = $stmt->fetchAll();
                            foreach ($users as $user) {
                                echo "<option value=" . $user['user_id'] . ">" . $user["Username"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">categories</label>
                    <div class="col-sm-10 col-md-6">
                        <select class="form-control" name="category" id="">
                            <option value="0">...</option>
                            <?php
                            $stmt2 = $con->prepare("SELECT * FROM categories");
                            $stmt2->execute();
                            $cats = $stmt2->fetchAll();
                            foreach ($cats as $cat) {
                                echo "<option value=" . $cat['catid'] . ">" . $cat["name"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-lg row g-1 align-items-center">
                    <div class="col-sm-offset-2 col-sm-10 col-md-6">
                        <input type="submit" value="Add Item" id="" class="btn btn-primary mt-2">
                    </div>
                </div>

            </form>
        </div>
        <?php
    } elseif ($do == 'insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            echo "<h1 class='text-center'>Insert Item</h1>";
            echo "<div class= 'container'>";
            // get variables from the form
            $name = $_POST['name'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];
            $country = $_POST['country'];
            $status = $_POST['status'];
            $member = $_POST['member'];
            $category = $_POST['category'];

            $check = checkItem('name', 'items', $name);

            $formErrors = array();
            if (empty($name)) {
                $formErrors[] = "name can\'t be Empty";
            };
            if (empty($desc)) {
                $formErrors[] = "description can\'t be Empty";
            };
            if (empty($price)) {
                $formErrors[] = "price can\'t be Empty";
            };
            if (empty($country)) {
                $formErrors[] = "country can\'t be Empty";
            };
            if ($status == 0) {
                $formErrors[] = "status can\'t be Empty";
            };
            if ($member == 0) {
                $formErrors[] = "You must select a valid member";
            };
            if ($category == 0) {
                $formErrors[] = "You must select a valid category";
            };
            foreach ($formErrors as $error) {
                echo '<div class = "alert alert-danger">' . $error . '</div>';
            }
            if (empty($formErrors)) {
                $stmt = $con->prepare("INSERT INTO
            items(name, description, price, add_date, country_made, status, member_id, cat_id)
            VALUES(:zname, :zdesc, :zprice, NOW(), :zcountry, :zstatus, :zmemberid, :zcatid)");
                $stmt->execute([
                    'zname' => $name,
                    'zdesc' => $desc,
                    'zprice' => $price,
                    'zcountry' => $country,
                    'zstatus' => $status,
                    'zmemberid' => $member,
                    'zcatid' => $category,
                ]);
                $usersmg =
                    $stmt->rowCount() . ' record inserted and you will be redirected to home';
                redirectHome($usersmg, 'http://localhost/eCommerce/admin/items.php');
            }
        } else {
            $errorMsg = 'Please login to view this page';
            redirectHome($errorMsg, "login.php", 2);
        }
    } elseif ($do == 'Edit') {
        $itemid = isset($_GET['item_id']) && is_numeric($_GET['item_id']) ? intval($_GET['item_id']) : '0';
        $stmt = $con->prepare('SELECT * FROM items WHERE item_id = ?');
        $stmt->execute([$itemid]);
        $item = $stmt->fetch();
        $count = $stmt->rowCount();
        if ($stmt->rowCount() > 0) { ?>
            <h1 class="text-center">Edit Items</h1>
            <div class="container">
                <form action="?do=update" method="POST" class="form-horizontal">
                    <input type="hidden" name="itemid" value="<?php echo $itemid; ?>">
                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Item Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name" id="" class="form-control" value="<?php echo $item['name']; ?>" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="desc" id="" class="form-control" value="<?php echo $item['description']; ?>" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Price</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="price" id="" value="<?php echo $item['price']; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label">Made In</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="country" id="" value="<?php echo $item['country_made']; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label">Status</label>
                        <div class="col-sm-10 col-md-6">
                            <select class="form-control" name="status" id="">
                                <option value="0">...</option>
                                <option value="1">New</option>
                                <option value="2">Like New</option>
                                <option value="3">Used</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label">Member</label>
                        <div class="col-sm-10 col-md-6">
                            <select class="form-control" name="member" id="">
                                <option value="0">...</option>
                                <?php
                                $stmt = $con->prepare("SELECT * FROM users");
                                $stmt->execute();
                                $users = $stmt->fetchAll();
                                foreach ($users as $user) {
                                    echo "<option value=" . $user['user_id'] . ">" . $user["Username"] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                        <label for="" class="col-sm-1 col-form-label">categories</label>
                        <div class="col-sm-10 col-md-6">
                            <select class="form-control" name="category" id="">
                                <option value="0">...</option>
                                <?php
                                $stmt2 = $con->prepare("SELECT * FROM categories");
                                $stmt2->execute();
                                $cats = $stmt2->fetchAll();
                                foreach ($cats as $cat) {
                                    echo "<option value=" . $cat['catid'] . ">" . $cat["name"] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10 col-md-6">
                            <input type="submit" value="update" id="" class="btn btn-primary btn-lg">
                        </div>
                    </div>
                </form>
                <?php
                $stmt = $con->prepare("SELECT comments.*,                                
                                users.Username AS user_name
                                FROM comments                               
                                INNER JOIN users
                                ON users.user_id = comments.member_id
                                WHERE comments.item_id = ?
        ");
                $stmt->execute([$itemid]);
                $comments = $stmt->fetchAll();
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered main-table text-center">
                        <tr>
                            <td>Member Name</td>
                            <td>Comment</td>
                            <td>Date</td>
                            <td>control</td>
                        </tr>
                        <?php foreach ($comments as $comment) {
                            echo '<tr>';
                            echo '<td>' . $comment['user_name'] . '</td>';
                            echo '<td>' . $comment['comment'] . '</td>';
                            echo '<td>' . $comment['date'] . '</td>';
                            echo "<td> <a href='comments.php?do=Edit&c_id=" .
                                $comment['c_id'] .
                                "' class='btn btn-success'>Edit</a>";
                            echo " <a href='comments.php?do=delete&c_id=" .
                                $comment['c_id'] .
                                "' class='btn btn-danger confirm'>Delete</a>";
                            if ($comment['status'] == 0) {
                                echo " <a href='comments.php?do=Approve&c_id=" .
                                    $comment['c_id'] .
                                    "' class='btn btn-info'>Approve</a></td>";
                            }
                            echo '</tr>';
                        } ?>
                    </table>
                </div>
    <?php } else {
            echo 'no Id found';
        }
    } elseif ($do == 'update') {
        echo "<h1 class='text-center'>Update Items</h1>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $itemid = isset($_POST['itemid']) ? intval($_POST['itemid']) : 0;
            $name = trim($_POST['name']);
            $desc = trim($_POST['desc']);
            $price = trim($_POST['price']);
            $country = trim($_POST['country']);
            $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
            $member = isset($_POST['member']) ? intval($_POST['member']) : 0;
            $category = isset($_POST['category']) ? intval($_POST['category']) : 0;

            $formErrors = array();

            if (empty($name)) {
                $formErrors[] = "Name can't be empty";
            }
            if (empty($desc)) {
                $formErrors[] = "Description can't be empty";
            }
            if (empty($price)) {
                $formErrors[] = "Price can't be empty";
            }
            if (empty($country)) {
                $formErrors[] = "Country can't be empty";
            }
            if ($status == 0) {
                $formErrors[] = "Status can't be empty";
            }
            if ($member == 0) {
                $formErrors[] = "You must select a valid member";
            }
            if ($category == 0) {
                $formErrors[] = "You must select a valid category";
            }

            // Display all errors
            foreach ($formErrors as $error) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
            }

            if (empty($formErrors)) {
                $stmt = $con->prepare(
                    "UPDATE items
                    SET name = ?, description = ?, price = ?, country_made = ?, status = ?, member_id = ?, cat_id = ?
                    WHERE item_id = ?"
                );
                $stmt->execute([
                    $name,
                    $desc,
                    $price,
                    $country,
                    $status,
                    $member,
                    $category,
                    $itemid
                ]);
                $msg = $stmt->rowCount() . ' record updated and you will be redirected to home';
                redirectHome($msg, 'items.php');
            }
        } else {
            echo 'Sorry, you are not allowed to see this content';
        }
    } elseif ($do == 'delete') {
        $item_id = $_GET["item_id"] && is_numeric($_GET["item_id"]) ? intval($_GET["item_id"]) : "0";
        $stmt3 = $con->prepare("DELETE FROM items WHERE item_id = ?");
        $stmt3->execute([$item_id]);
        $userMsg = "Item Delected";
        redirectHome($userMsg, "items.php");
    } elseif ($do == 'Approve') {
        $item_id = $_GET["item_id"] && is_numeric($_GET["item_id"]) ? intval($_GET["item_id"]) : "0";

        $stmt4 = $con->prepare("UPDATE items SET Approve = 1 WHERE item_id = ? ");
        $stmt4->execute([$item_id]);
        $userMsg = "Item Approved";
        redirectHome($userMsg, "items.php");
    }

    include $tpl . 'footer.php';
} else {
    header('location: index.php');
    exit();
}
ob_end_flush();
