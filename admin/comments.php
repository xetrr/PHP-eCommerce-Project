<!-- -------------------------- Manage comments page -------------------------- */
/* ------------------ you can Edit | Approve | Delete ----------------- */-->

<?php
ob_start();
session_start();
$pageTitle = 'comments';


if (isset($_SESSION['Username'])) {
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if ($do == 'Manage') {

        $stmt = $con->prepare("SELECT comments.*,
                                items.name AS item_name,
                                users.Username AS user_name
                                FROM comments
                                INNER JOIN items
                                ON comments.item_id = items.item_id
                                INNER JOIN users
                                ON users.user_id = comments.member_id
        ");
        $stmt->execute();
        $comments = $stmt->fetchAll();
?>
        <h1 class="text-center">Manage Comments</h1>
        <div class="container">
            <div class="table-responsive">
                <table class="table table-bordered main-table text-center">
                    <tr>
                        <td>ID</td>
                        <td>Member Name</td>
                        <td>Item Name</td>
                        <td>Comment</td>
                        <td>Date</td>
                        <td>control</td>
                    </tr>
                    <?php foreach ($comments as $comment) {
                        echo '<tr>';
                        echo '<td>' . $comment['c_id'] . '</td>';
                        echo '<td>' . $comment['user_name'] . '</td>';
                        echo '<td>' . $comment['item_name'] . '</td>';
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
        </div>
        <?php
    } elseif ($do == 'Edit') {
        $commentID = isset($_GET['c_id']) && is_numeric($_GET['c_id']) ? intval($_GET['c_id']) : '0';
        $stmt = $con->prepare('SELECT * FROM comments WHERE c_id = ?');
        $stmt->execute([$commentID]);
        $comment = $stmt->fetch();
        $count = $stmt->rowCount();
        if ($stmt->rowCount() > 0) { ?>
            <h1 class="text-center">Edit Items</h1>
            <div class="container">
                <form action="?do=update" method="POST" class="form-horizontal">
                    <input type="hidden" name="commentID" value="<?php echo $commentID; ?>">

                    <div class="form-group form-group-lg">
                        <label for="" class="col-sm-2 control-label ">Comment</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" name="newComment" rows="3"><?php echo $comment["comment"] ?></textarea>
                    </div>
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10 col-md-6">
                            <input type="submit" value="update" id="" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div><?php
                } else {
                    echo 'no Id found';
                }
            } elseif ($do == "update") {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $commentID = isset($_POST["commentID"]) && is_numeric($_POST["commentID"])  ? intval($_POST["commentID"]) : "0";
                    $newComment = trim($_POST["newComment"]);
                    $stmt = $con->prepare("UPDATE comments SET comment = ? WHERE c_id = ?");
                    $stmt->execute([$newComment, $commentID]);
                    $stmtMsg = "comment Edited";
                    redirectHome($stmtMsg, 'comments.php');
                } else {
                    "no comment with this ID";
                }
            } elseif ($do == 'delete') {
                $commentID = $_GET["c_id"] && is_numeric($_GET["c_id"]) ? intval($_GET["c_id"]) : "0";
                $stmt3 = $con->prepare("DELETE FROM comments WHERE c_id = ?");
                $stmt3->execute([$commentID]);
                $userMsg = "comment Delected";
                redirectHome($userMsg, "comments.php");
            } elseif ($do == 'Approve') {
                $commentID = $_GET["c_id"] && is_numeric($_GET["c_id"]) ? intval($_GET["c_id"]) : "0";
                $stmt4 = $con->prepare("UPDATE comments SET Approve = 1 WHERE c_id = ? ");
                $stmt4->execute([$commentID]);
                $userMsg = "comment Approved";
                redirectHome($userMsg, "comments.php");
            }
            include $tpl . 'footer.php';
        } else {
            header('location: index.php');
            exit();
        }
        ob_end_flush();
