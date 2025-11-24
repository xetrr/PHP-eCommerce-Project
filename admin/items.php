<?php
ob_start();
session_start();
$pageTitle = 'items';


if (isset($_SESSION['Username'])) {
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if ($do == 'Manage') {
        
      
        $stmt = $con->prepare("SELECT * from items");
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

                        echo "<td> <a href='items.php?do=Edit&itemid=" .
                            $item['item_id'] .
                            "' class='btn btn-success'>Edit</a>";
                        echo " <a href='items.php?do=delete&itemid=" .
                            $item['item_id'] .
                            "' class='btn btn-danger confirm'>Delete</a>";                        
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
                redirectHome($usersmg, '');
            }
        } else {
            $errorMsg = 'Please login to view this page';
            redirectHome($errorMsg, "login.php", 2);
        }
    } elseif ($do == 'Edit') {
    } elseif ($do == 'update') {
    } elseif ($do == 'delete') {
    } elseif ($do == 'Approve') {
    }
    include $tpl . 'footer.php';
} else {
    header('location: index.php');
    exit();
}
ob_end_flush();
