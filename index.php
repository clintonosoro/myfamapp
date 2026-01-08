<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MyFamApp - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #F0F4F8;
      color: #2D3748;
      scroll-behavior: smooth;
    }

    .navbar {
      background: rgba(90, 103, 216, 0.95);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.6rem;
    }

    .nav-link {
      color: #fff !important;
      margin-left: 1rem;
      font-weight: 500;
    }

    .nav-link:hover {
      text-decoration: underline;
    }

    /* Hero Section */
    .hero-section {
      background: url('index.png') center center/cover no-repeat;
      height: 90vh;
      position: relative;
      border-radius: 0 0 2rem 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
    }

    .hero-overlay {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(6px);
      padding: 3rem 2rem;
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .hero-title {
      font-size: 3rem;
      font-weight: 700;
    }

    .hero-subtitle {
      font-size: 1.25rem;
      margin-top: 0.5rem;
    }

    /* Features */
    .feature-card {
      background: white;
      border: none;
      border-radius: 18px;
      padding: 1.5rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
      position: relative;
    }

    .feature-card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .feature-icon {
      font-size: 2.5rem;
      color: #5A67D8;
      margin-bottom: 0.5rem;
      transition: transform 0.3s;
    }

    .feature-card:hover .feature-icon {
      transform: rotate(5deg) scale(1.2);
    }

    .feature-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.3rem;
      color: #5A67D8;
    }

    .feature-text {
      font-size: 0.95rem;
      color: #4A5568;
    }

    /* Footer */
    .footer {
      background: #2D3748;
      color: #fff;
      text-align: center;
      padding: 1.2rem;
      margin-top: 4rem;
      border-top: 6px solid #9F7AEA;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">MyFamApp</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section with Background Image -->
  <section class="hero-section">
    <div class="hero-overlay">
      <h1 class="hero-title">Welcome to MyFamApp 👨‍👩‍👧‍👦</h1>
      <p class="hero-subtitle">Bringing your family closer through connection, memories, and love.</p>
    </div>
  </section>

  <!-- Features -->
  <div class="container mt-5">
    <div class="row g-4 text-center">
      <div class="col-sm-6 col-lg-3">
        <div class="feature-card h-100">
          <div class="feature-icon">📅</div>
          <div class="feature-title">Calendar</div>
          <div class="feature-text">Plan events, birthdays, and family gatherings with ease.</div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-card h-100">
          <div class="feature-icon">📝</div>
          <div class="feature-title">To-Do</div>
          <div class="feature-text">Manage shopping lists, chores, and family responsibilities.</div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-card h-100">
          <div class="feature-icon">📷</div>
          <div class="feature-title">Gallery</div>
          <div class="feature-text">Share memories and photos with the entire family.</div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-card h-100">
          <div class="feature-icon">📢</div>
          <div class="feature-title">Announcements</div>
          <div class="feature-text">Post family updates, reminders, or special news.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; <?= date('Y') ?> MyFamApp. Built with ❤️ to bring families closer.</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
