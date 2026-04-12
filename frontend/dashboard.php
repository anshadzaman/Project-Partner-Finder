<?php
session_start();

// DB
$conn = new mysqli("localhost", "root", "", "project_finder");

if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$page = $_GET['page'] ?? 'main';

// ================= CREATOR DATA =================
if($role == 'creator'){

    $projects = $conn->query("SELECT * FROM projects WHERE creator_id='$user_id'");

    $applications = $conn->query("
        SELECT applications.*, users.full_name, projects.title 
        FROM applications
        JOIN users ON applications.user_id = users.id
        JOIN projects ON applications.project_id = projects.id
        WHERE projects.creator_id = '$user_id' AND applications.status='pending'
    ");
}

// ================= FINDER DATA =================
if($role == 'finder'){

    $search = $_GET['search'] ?? '';

    $projects = $conn->query("
        SELECT projects.*, users.full_name 
        FROM projects 
        JOIN users ON projects.creator_id = users.id
        WHERE status='open' AND title LIKE '%$search%'
    ");

    $myApps = $conn->query("
        SELECT applications.*, projects.title 
        FROM applications
        JOIN projects ON applications.project_id = projects.id
        WHERE applications.user_id = '$user_id'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="/project-partner-finder/assets/css/dashboard.css?v=5">
</head>

<body>

<div class="wrapper">

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">
    <h2>Partner Finder</h2>

    <?php if($role == 'creator'): ?>
        <a href="?page=main">🏠 Main Dashboard</a>
        <a href="?page=applications">📥 Pending Applications</a>
        <a href="?page=projects">📁 Ongoing Projects</a>
    <?php endif; ?>

    <?php if($role == 'finder'): ?>
        <a href="?page=main">🏠 Main Dashboard</a>
        <a href="?page=status">📊 Application Status</a>
    <?php endif; ?>
</div>

<!-- ================= MAIN ================= -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">

        <form action="../backend/switch_role.php" method="POST">
            <button class="btn switch">🔄 Switch Role</button>
        </form>

        <a href="../backend/logout.php">
            <button class="btn logout">Logout</button>
        </a>

    </div>

    <!-- CONTENT -->
    <div class="content">

        <h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>

        <!-- ================= CREATOR MAIN ================= -->
        <?php if($role == 'creator' && $page == 'main'): ?>

        <div class="card">
            <h3>➕ Create Project</h3>

            <form action="../backend/create_project.php" method="POST" class="form-grid">

                <input type="text" name="title" placeholder="Project Title" required>
                <input type="text" name="domain" placeholder="Domain" required>

                <input type="text" name="skills" placeholder="Skills" required>
                <input type="text" name="experience" placeholder="Experience" required>

                <input type="number" name="work_hours" placeholder="Work Hours" required>
                <input type="number" name="team_size" placeholder="Team Size" required>

                <input type="date" name="deadline" class="full" required>

                <textarea name="description" placeholder="Description" required></textarea>

                <button class="full">Create Project</button>

            </form>
        </div>

        <?php endif; ?>

        <!-- ================= CREATOR APPLICATIONS ================= -->
        <?php if($role == 'creator' && $page == 'applications'): ?>

        <div class="card">
            <h3>📥 Pending Applications</h3>

            <?php if($applications && $applications->num_rows > 0): ?>
                <?php while($a = $applications->fetch_assoc()): ?>

                    <div class="card">
                        <p><b><?php echo $a['full_name']; ?></b> applied for <b><?php echo $a['title']; ?></b></p>

                        <form action="../backend/update_application.php" method="POST">
    <input type="hidden" name="app_id" value="<?php echo $a['id']; ?>">
    <button name="action" value="accept">Accept</button>
    <button name="action" value="reject">Reject</button>
</form>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No pending requests</p>
            <?php endif; ?>

        </div>

        <?php endif; ?>

        <!-- ================= CREATOR PROJECTS ================= -->
        <?php if($role == 'creator' && $page == 'projects'): ?>

        <div class="card">
            <h3>📁 Your Projects</h3>

            <?php if($projects && $projects->num_rows > 0): ?>
                <?php while($p = $projects->fetch_assoc()): ?>

                    <div class="card">
                        <h4><?php echo $p['title']; ?></h4>
                        <p><?php echo $p['description']; ?></p>
                        <small><b>Skills:</b> <?php echo $p['required_skills']; ?></small>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects yet</p>
            <?php endif; ?>

        </div>

        <?php endif; ?>

        <!-- ================= FINDER MAIN ================= -->
        <?php if($role == 'finder' && $page == 'main'): ?>

        <div class="card">
            <h3>🔍 Search Projects</h3>

            <form method="GET">
                <input type="hidden" name="page" value="main">
                <input type="text" name="search" placeholder="Search..." style="padding:8px;">
                <button class="btn">Search</button>
            </form>
        </div>

        <div class="section">
            <h3>📁 Available Projects</h3>

            <?php if($projects && $projects->num_rows > 0): ?>
                <?php while($p = $projects->fetch_assoc()): ?>

                    <div class="project-card">

    <!-- HEADER -->
    <div class="project-header">
        <h3><?php echo $p['title']; ?></h3>
        <span class="domain"><?php echo $p['domain']; ?></span>
    </div>

    <!-- SHORT DESCRIPTION -->
    <p class="desc">
        <?php echo substr($p['description'], 0, 100); ?>...
    </p>

    <!-- INFO -->
    <div class="project-info">

        <p><strong>👤 Owner:</strong> <?php echo $p['full_name']; ?></p>

        <p><strong>👥 Team:</strong>
        <?php
            $count = $conn->query("
                SELECT COUNT(*) as total 
                FROM applications 
                WHERE project_id='".$p['id']."' AND status='accepted'
            ");
            $row = $count->fetch_assoc();
            echo $row['total'] . " / " . ($p['team_size'] ?? 0);
        ?>
        </p>

        <p><strong>🛠 Skills:</strong> <?php echo $p['required_skills']; ?></p>

        <p><strong>⏱ Work:</strong> <?php echo $p['work_hours'] ?? 'N/A'; ?> hrs/day</p>

        <p><strong>🎯 Experience:</strong> <?php echo $p['experience'] ?? 'N/A'; ?></p>

    </div>

    <!-- APPLY BUTTON LOGIC -->
    <form action="../backend/apply_project.php" method="POST">
        <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">

        <?php
            // check already applied
            $check = $conn->query("
                SELECT * FROM applications 
                WHERE user_id='$user_id' AND project_id='".$p['id']."'
            ");

            if($check->num_rows > 0){
                echo "<button class='apply-btn' disabled>Applied</button>";
            }
            elseif($row['total'] >= ($p['team_size'] ?? 0)){
                echo "<button class='apply-btn' disabled>Full</button>";
            }
            else{
                echo "<button class='apply-btn'>Apply</button>";
            }
        ?>
    </form>

</div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects found</p>
            <?php endif; ?>

        </div>

        <?php endif; ?>

        <!-- ================= FINDER STATUS ================= -->
        <?php if($role == 'finder' && $page == 'status'): ?>

        <div class="card">
            <h3>📊 Your Applications</h3>

            <?php if($myApps && $myApps->num_rows > 0): ?>
                <?php while($a = $myApps->fetch_assoc()): ?>

                    <div class="card">
                        <h4><?php echo $a['title']; ?></h4>

                        <p>Status: 
                            <b style="
                                color:
                                <?php 
                                if($a['status']=='accepted') echo 'green';
                                elseif($a['status']=='rejected') echo 'red';
                                else echo 'orange';
                                ?>
                            ">
                            <?php echo ucfirst($a['status']); ?>
                            </b>
                        </p>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No applications yet</p>
            <?php endif; ?>

        </div>

        <?php endif; ?>

    </div>

</div>

</div>

</body>
</html>