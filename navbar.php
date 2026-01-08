<?php 
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  /* Optional: Ensure mobile dropdown is readable */
  .navbar-nav .nav-link {
    color: white !important;
  }
  @media (max-width: 991.98px) {
    .navbar-collapse {
      background-color: #343a40; /* dark bg on small screens */
      padding: 1rem;
      border-radius: 5px;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">MyFamApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="post_status.php">Post a Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="status_feed.php">Status Feed</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="members.php">Family Members</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>

      <?php if (isset($_SESSION['user'])): ?>
        <span class="navbar-text text-light">
          Welcome, <?= htmlspecialchars($_SESSION['user']['full_name']) ?> 
          (<?= isset($_SESSION['user']['role']) ? htmlspecialchars($_SESSION['user']['role']) : 'Member' ?>)
        </span>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Bootstrap Bundle JS (with Popper for toggle menu) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
