<?php
session_start();
$pageTitle = 'members';

if (isset($_SESSION['Username'])) {
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if ($do == 'Manage') {

        $query = '';
        if (isset($_GET['page']) && $_GET['page'] == 'pending') {
            $query = "AND RegStatus = '0'";
        }
        $value = 'mahmoud';
        checkItem('Username', 'users', $value);
        $stmt = $con->prepare("SELECT * from users WHERE GroupID != 1 $query");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        ?>
        <h1 class="text-center">Manage Members</h1>
        <div class="container">
            <div class="table-responsive">
                <table class="table table-bordered main-table text-center">
                    <tr>
                        <td>#ID</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Full Name</td>
                        <td>Registerd Date</td>
                        <td>Control</td>
                    </tr>
                    <?php foreach ($rows as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['user_id'] . '</td>';
                        echo '<td>' . $row['Username'] . '</td>';
                        echo '<td>' . $row['Email'] . '</td>';
                        echo '<td>' . $row['FullName'] . '</td>';
                        echo '<td>' . $row['Date'] . '</td>';

                        echo "<td> <a href='members.php?do=Edit&userid=" .
                            $row['user_id'] .
                            "' class='btn btn-success'>Edit</a>";
                        echo " <a href='members.php?do=delete&userid=" .
                            $row['user_id'] .
                            "' class='btn btn-danger confirm'>Delete</a>";
                        if ($row['RegStatus'] == 0) {
                            echo " <a href='members.php?do=Approve&userid=" .
                                $row['user_id'] .
                                "' class='btn btn-info'>Approve</a></td>";
                        }
                        echo '</tr>';
                    } ?>
                </table>
            </div>
            <a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add new memeber </a>
        </div>
    <?php
    } elseif ($do == 'Add') { ?>

        <h1 class="text-center">Add New Member</h1>
        <div class="container-md mx-auto">
            <form action="?do=insert" method="POST" class="form-horizontal ">
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label">Username</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="username" id="" class="form-control" autocomplete="off" required="required">
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2 password-field">
                    <label for="" class="col-sm-1 col-form-label ">Password</label>
                    <div class="eye-div col-sm-10  col-md-6">
                        <input type="password" name="password" class="password form-control" autocomplete="new-password" required="required">
                        <i class="show-pass fa fa-eye-slash fa-2x"></i>
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label ">Email</label>
                    <div class="col-sm-10  col-md-6">
                        <input type="email" name="email" id="" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label for="" class="col-sm-1 col-form-label ">Full name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="fullName" id="" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group form-group-lg row g-1 align-items-center">
                    <div class="col-sm-offset-2 col-sm-10 col-md-6">
                        <input type="submit" value="Add Member" id="" class="btn btn-primary mt-2">
                    </div>
                </div>

            </form>
        </div>

    <?php } elseif ($do == 'insert') {
        echo "<h1 class='text-center'>Insert Member</h1>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $_POST['username'];
            $email = $_POST['email'];
            $name = $_POST['fullName'];
            $pass = sha1($_POST['password']);

            $check = checkItem('Username', 'users', $user);

            if ($check == 1) {
                $userMsg =
                    "<div class='alert alert-danger'>" .
                    'sorry this user already exists' .
                    '</div>';
                redirectHome($userMsg, 'back');
            } else {
                $stmt = $con->prepare("INSERT INTO 
            users(Username , Password , Email , FullName,RegStatus , Date)
            VALUES(:user , :password ,  :email , :name ,1, NOW())");
                $stmt->execute([
                    'user' => $user,
                    'password' => $pass,
                    'email' => $email,
                    'name' => $name,
                ]);
                $usersmg =
                    $stmt->rowCount() . ' record inserted and you will be redirected to home';
                redirectHome($usersmg, '');
            }
        } else {
            $errorMsg = 'sorry you are not allowed to see this content';
            redirectHome($errorMsg, 2);
        }
    } elseif ($do == 'Edit') { ?>
        <?php
        $userid =
            isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : '0';
        $stmt = $con->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userid]);
        $row = $stmt->fetch();
        $count = $stmt->rowCount();
        if ($stmt->rowCount() > 0) { ?>

            <h1 class="text-center">Edit Member</h1>
            <div class="container">
                <form action="?do=update" method="POST" class="form-horizontal">
                    <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Username</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="username" id="" class="form-control" value="<?php echo $row[
                                'Username'
                            ]; ?>" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Password</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="hidden" name="oldpassword" id="" value="<?php echo $row[
                                'Password'
                            ]; ?>">
                            <input type="password" name="newpassword" id="" class="form-control" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Email</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="email" name="email" id="" value="<?php echo $row[
                                'Email'
                            ]; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Full name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="fullName" id="" value="<?php echo $row[
                                'FullName'
                            ]; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10 col-md-6">
                            <input type="submit" value="save" id="" class="btn btn-primary btn-lg">
                        </div>
                    </div>

                </form>
            </div>

<?php } else {echo 'no Id found';}
        } elseif ($do == 'update') {
        echo "<h1 class='text-center'>update Member</h1>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['userid'];
            $user = $_POST['username'];
            $email = $_POST['email'];
            $name = $_POST['fullName'];
            $pass = '';
            if (empty($_POST['newpassword'])) {
                $pass = $_POST['oldpassword'];
            } else {
                $pass = sha1($_POST['newpassword']);
            }
            $stmt = $con->prepare(
                'UPDATE users SET Username = ?, Email =? , FullName =? , Password = ? WHERE user_id =?',
            );

            $stmt->execute([$user, $email, $name, $pass, $id]);
            echo $stmt->rowCount() . 'record updated';
        } else {
            echo 'sorry you are not allowed to see this content';
        }
    } elseif ($do == 'delete') {
        $userid =
            isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : '0';

        // $stmt  = $con->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
        // $stmt->execute(array($userid));
        // $count  = $stmt->rowCount();

        // instead we will use the checkitem function
        $check = checkItem('user_id', 'users', $userid);

        if ($check > 0) {
            $stmt = $con->prepare('DELETE from users WHERE user_id =:zuser');
            $stmt->bindParam(':zuser', $userid);
            $stmt->execute();
            $userMsg =
                "<div class='alert alert-success'>" . $stmt->rowCount() . 'record deleted </div>';
            redirectHome($userMsg, 'back');
        } else {
            echo 'false ID';
        }
    } elseif ($do == 'Approve') {
        $userid =
            isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : '0';

        // $stmt  = $con->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
        // $stmt->execute(array($userid));
        // $count  = $stmt->rowCount();

        // instead we will use the checkitem function
        $check = checkItem('user_id', 'users', $userid);

        if ($check > 0) {
            $stmt = $con->prepare('UPDATE users SET RegStatus = 1 WHERE user_id = ?');
            $stmt->execute([$userid]);
            $userMsg =
                "<div class='alert alert-success'>" . $stmt->rowCount() . 'User Approved </div>';
            redirectHome($userMsg, 'back');
        } else {
            echo 'false ID';
        }
    }
    include $tpl . 'footer.php';
} else {
    header('location: index.php');
    exit();
}
?>
