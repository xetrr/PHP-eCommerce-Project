<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="categories.php">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="members.php">Members</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="items.php">Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="settings.php">settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="comments.php">Comments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="logout.php">logout</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Profile
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="members.php?do=Edit&userid=<?php echo $_SESSION[
                            'ID'
                        ]; ?>">Edit profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>