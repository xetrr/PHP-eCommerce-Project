<?php
session_start();

if (isset($_SESSION['Username'])) {

    $pageTitle = 'Dashboard';
    include 'init.php';
?>
    <!-- start dashboard page -->





    <div class="container home-stats text-center">
        <h1>Dashboard</h1>
        <div class="row">
            <div class="col-md-3">
                <div class="stat st-members">
                    Total Members
                    <span><a href="members.php"><?php echo countItems(
                                                    'user_id',
                                                    'users',
                                                ); ?></a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat st-pending">Pending Members
                    <span><a href="members.php?page=pending">
                            <?php echo checkItem('RegStatus', 'users', 0); ?>
                        </a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat st-items">Total Items
                    <span><a href="items.php"><?php echo countItems(
                                                    'item_id',
                                                    'items',
                                                ); ?></a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat st-comments">Total comments
                    <span>40</span>
                </div>
            </div>
        </div>
    </div>


    <div class="container latest">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-users"> Latest Registered Users</i>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        $theLatest = getLatest('*', 'users', 'user_id', 4);
                        foreach ($theLatest as $user) {
                            echo "<li class='list-group-item'>" . $user['Username'] . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-tag"> Latest Items</i>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        $theLatest = getLatest('*', 'items', 'item_id', 4);
                        foreach ($theLatest as $item) {
                            echo "<li class='list-group-item'>" . $item['name'] . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>






<?php include $tpl . 'footer.php';
} else {
    header('location: index.php');
    exit();
}
