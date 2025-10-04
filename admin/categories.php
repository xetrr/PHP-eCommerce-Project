<?php
// template page 

//ob_start();

session_start();

$pageTitle = 'Categories';

if (isset($_SESSION["Username"])) {
    include 'init.php';
    $do = isset($_GET["do"]) ? $_GET["do"] : 'Manage';

    if ($do == "Manage") {
        echo "welcome to manage"; ?>
        <div class="container">
            <a href="categories.php?do=Add">go to add page</a>
        </div>
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



<?php
    } elseif ($do == 'Insert') {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            echo "<h1 class='text-center'>New Category</h1>";
            echo "<div class='container'>";
            $name       = $_POST["name"];
            $desc       = $_POST["desc"];
            $order      = $_POST["ordering"];
            $visibility = $_POST["visibility"];
            $comment    = $_POST["comment"];
            $ads        = $_POST["Ads"];

            $check = checkItem("name", "categories", $name);
            echo $check;

            if ($check == 1) {
                $userMsg = "<div class='alert alert-danger'>" . "sorry this category already exists" . "</div>";
                redirectHome($userMsg, 'back');
            } else {
                $stmt = $con->prepare("INSERT INTO 
            categories(name , description , ordering , visibility , allow_comments , active_ads)
            VALUES(:name , :desc ,  :order , :visibile , :comments, :ads)");
                $stmt->execute(array(
                    'name' => $name,
                    'desc' => $desc,
                    'order' => $order,
                    'visibile' => $visibility,
                    'comments' => $comment,
                    'ads' => $ads,
                ));
                $usersmg =  $stmt->rowCount() . " record inserted and you will be redirected to home";
                //redirectHome($usersmg, '');
            }
        } else {
            $errorMsg =  "sorry you are not allowed to see this content";
            redirectHome($errorMsg, 2);
            echo "</div>";
        }
    } elseif ($do == 'Edit') {
    } elseif ($do == 'Update') {
    } elseif ($do == 'Delete') {
    }

    include $tpl . 'footer.php';
} else {
    header("location: index.php");
    exit();
}
//ob_end_flush();
