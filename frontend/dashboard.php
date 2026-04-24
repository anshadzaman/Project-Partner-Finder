<?php
session_start();

// DB
$conn = new mysqli("localhost", "root", "", "project_finder");

if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$page    = $_GET['page'] ?? 'main';

/* ================= CREATOR DATA ================= */
if($role == 'creator'){

    $projects = $conn->query("
        SELECT * FROM projects
        WHERE creator_id='$user_id'
    ");

    $applications = $conn->query("
        SELECT applications.*, users.full_name, projects.title
        FROM applications
        JOIN users ON applications.user_id = users.id
        JOIN projects ON applications.project_id = projects.id
        WHERE projects.creator_id='$user_id'
        AND applications.status='pending'
    ");
}

/* ================= FINDER DATA ================= */
if($role == 'finder'){

    $search = $_GET['search'] ?? '';

    $projects = $conn->query("
        SELECT projects.*, users.full_name
        FROM projects
        JOIN users ON projects.creator_id = users.id
        WHERE status='open'
        AND title LIKE '%$search%'
    ");

    $myApps = $conn->query("
        SELECT applications.*, projects.title
        FROM applications
        JOIN projects ON applications.project_id = projects.id
        WHERE applications.user_id='$user_id'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <link rel="stylesheet"
    href="/project-partner-finder/assets/css/dashboard.css?v=9">

    <?php if($page == 'applications'): ?>
    <link rel="stylesheet"
    href="/project-partner-finder/assets/css/pending.css?v=2">
    <?php endif; ?>

</head>

<body>

<div class="wrapper">

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">

    <h2>Partner Finder</h2>

    <?php if($role == 'creator'): ?>

        <a href="?page=main">Main Dashboard</a>
        <a href="?page=applications">Pending Applications</a>
        <a href="?page=projects">Ongoing Projects</a>

    <?php endif; ?>

    <?php if($role == 'finder'): ?>

        <a href="?page=main">Main Dashboard</a>
        <a href="?page=status">Application Status</a>

    <?php endif; ?>

</div>

<!-- ================= MAIN ================= -->
<div class="main">

<!-- ================= TOPBAR ================= -->
<div class="topbar">

    <div class="top-left">

        <h3 class="welcome">
            Welcome, <?php echo $_SESSION['name']; ?>
        </h3>

        <?php if($role == 'finder' && $page == 'main'): ?>
        <form method="GET" class="top-search">

            <input type="hidden" name="page" value="main">

            <input type="text"
            name="search"
            placeholder="Search Projects">

            <button class="btn">Search</button>

        </form>
        <?php endif; ?>

    </div>

    <div class="top-actions">

        <form action="../backend/switch_role.php" method="POST">
            <button class="btn switch">Switch Role</button>
        </form>

        <a href="../backend/logout.php">
            <button class="btn logout">Logout</button>
        </a>

    </div>

</div>

<!-- ================= CONTENT ================= -->
<div class="content">

<!-- ===================================================
   CREATOR MAIN
=================================================== -->
<?php if($role == 'creator' && $page == 'main'): ?>

<div class="card">

    <h2>Create Project</h2>

    <form action="../backend/create_project.php"
    method="POST"
    class="form-grid">

        <input type="text"
        name="title"
        placeholder="Project Title"
        required>

        <input type="text"
        name="domain"
        placeholder="Project Domain"
        required>

        <input type="text"
        name="skills"
        placeholder="Required Skills"
        required>

        <input type="text"
        name="experience"
        placeholder="Experience Level"
        required>

        <input type="number"
        name="work_hours"
        placeholder="Work Hours / Day"
        required>

        <input type="number"
        name="team_size"
        placeholder="Team Size"
        required>

        <input type="date"
        name="deadline"
        class="full"
        required>

        <textarea
        name="description"
        placeholder="Short Description"
        required></textarea>

        <button class="btn switch full">
            Create Project
        </button>

    </form>

</div>

<?php endif; ?>

<!-- ===================================================
   CREATOR APPLICATIONS
=================================================== -->
<?php if($role == 'creator' && $page == 'applications'): ?>

<div class="pending-header">
    <h2>Pending Applications</h2>
    <p>Review applicants and build your team</p>
</div>

<div class="pending-wrapper">

<?php if($applications && $applications->num_rows > 0): ?>
<?php while($a = $applications->fetch_assoc()): ?>

<div class="app-card">

    <div class="app-top">

        <div style="display:flex;gap:15px;align-items:center;">

            <div class="user-badge">
                <?php echo strtoupper(substr($a['full_name'],0,1)); ?>
            </div>

            <div class="app-info">
                <h3><?php echo $a['full_name']; ?></h3>
                <p>Applied for <strong><?php echo $a['title']; ?></strong></p>
            </div>

        </div>

    </div>

    <div class="app-meta">

        <div class="meta-box">
            <span>Status</span>
            <strong>Pending</strong>
        </div>

        <div class="meta-box">
            <span>Project</span>
            <strong><?php echo $a['title']; ?></strong>
        </div>

        <div class="meta-box">
            <span>Applicant</span>
            <strong><?php echo $a['full_name']; ?></strong>
        </div>

    </div>

    <form action="../backend/update_application.php" method="POST">

        <input type="hidden"
        name="app_id"
        value="<?php echo $a['id']; ?>">

        <div class="action-btns">

            <button name="action"
            value="accept"
            class="accept-btn">
                Accept
            </button>

            <button name="action"
            value="reject"
            class="reject-btn">
                Reject
            </button>

        </div>

    </form>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="empty-app">
    <h3>No Pending Applications</h3>
    <p>New requests will appear here.</p>
</div>

<?php endif; ?>

</div>

<?php endif; ?>

<!-- ===================================================
   CREATOR PROJECTS
=================================================== -->
<?php if($role == 'creator' && $page == 'projects'): ?>

<div class="section-header">
    <h2>Ongoing Projects</h2>
    <p>Manage active projects and progress</p>
</div>

<div class="project-grid">

<?php if($projects && $projects->num_rows > 0): ?>
<?php while($p = $projects->fetch_assoc()): ?>

<?php
$count = $conn->query("
SELECT COUNT(*) as total
FROM applications
WHERE project_id='".$p['id']."'
AND status='accepted'
");

$row = $count->fetch_assoc();

$joined   = $row['total'];
$total    = $p['team_size'];
$progress = $p['progress'] ?? 0;
?>

<div class="pro-card">

    <div class="pro-top">

        <div>
            <h3><?php echo $p['title']; ?></h3>
            <span class="tag"><?php echo $p['domain']; ?></span>
        </div>

        <span class="status-pill">
            <?php echo $p['project_status'] ?? 'Not Started'; ?>
        </span>

    </div>

    <p class="pro-desc">
        <?php echo substr($p['description'],0,90); ?>...
    </p>

    <div class="mini-stats">

        <div>Team: <?php echo $joined; ?>/<?php echo $total; ?></div>

        <div>Deadline: <?php echo $p['deadline']; ?></div>

        <div>Experience: <?php echo $p['experience']; ?></div>

        <div>Work: <?php echo $p['work_hours']; ?> hrs/day</div>

    </div>

    <div class="progress-label">
        <span>Progress</span>
        <span><?php echo $progress; ?>%</span>
    </div>

    <div class="bar">
        <div class="fill"
        style="width:<?php echo $progress; ?>%"></div>
    </div>

    <form action="../backend/update_project_status.php"
    method="POST"
    class="action-row">

        <input type="hidden"
        name="project_id"
        value="<?php echo $p['id']; ?>">

        <select name="status">
            <option>Not Started</option>
            <option>Planning</option>
            <option>In Progress</option>
            <option>Testing</option>
            <option>Completed</option>
        </select>

        <input type="number"
        name="progress"
        min="0"
        max="100"
        value="<?php echo $progress; ?>">

        <button class="save-btn">
            Update
        </button>

    </form>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="empty-box">
    <h3>No Projects Yet</h3>
</div>

<?php endif; ?>

</div>

<?php endif; ?>

<!-- ===================================================
   FINDER MAIN
=================================================== -->
<?php if($role == 'finder' && $page == 'main'): ?>

<div class="section-header">
    <h2>Available Projects</h2>
    <p>Explore open projects and apply</p>
</div>

<?php if($projects && $projects->num_rows > 0): ?>
<?php while($p = $projects->fetch_assoc()): ?>

<div class="project-card">

    <div class="project-header">

        <h3><?php echo $p['title']; ?></h3>

        <span class="domain">
            <?php echo $p['domain']; ?>
        </span>

    </div>

    <p class="desc">
        <?php echo substr($p['description'],0,100); ?>...
    </p>

    <div class="project-info">

        <p><strong>Owner:</strong>
        <?php echo $p['full_name']; ?></p>

        <p><strong>Skills:</strong>
        <?php echo $p['required_skills']; ?></p>

        <p><strong>Work:</strong>
        <?php echo $p['work_hours']; ?> hrs/day</p>

        <p><strong>Experience:</strong>
        <?php echo $p['experience']; ?></p>

    </div>

    <form action="../backend/apply_project.php"
    method="POST">

        <input type="hidden"
        name="project_id"
        value="<?php echo $p['id']; ?>">

        <button class="apply-btn">
            Apply
        </button>

    </form>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="empty-box">
    <h3>No Projects Found</h3>
</div>

<?php endif; ?>

<?php endif; ?>

<!-- ===================================================
   FINDER STATUS
=================================================== -->
<?php if($role == 'finder' && $page == 'status'): ?>

<div class="card">

<h2>Your Applications</h2>

<?php if($myApps && $myApps->num_rows > 0): ?>
<?php while($a = $myApps->fetch_assoc()): ?>

<div class="card">

    <h3><?php echo $a['title']; ?></h3>

    <p>Status:
        <strong><?php echo ucfirst($a['status']); ?></strong>
    </p>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No Applications Yet</p>

<?php endif; ?>

</div>

<?php endif; ?>

</div>
</div>
</div>

</body>
</html>