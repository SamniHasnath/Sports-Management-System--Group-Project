<?php
include 'Dashboard/db.php';  // Your DB connection
// Query to get counts and names for all sports (one efficient query)
$sql = "SELECT s.sport_id, s.name AS sport_name, COUNT(ssr.id) AS student_count
        FROM sports s
        LEFT JOIN student_sport_registration ssr ON s.sport_id = ssr.sport_id
        LEFT JOIN users u ON ssr.user_id = u.id
        WHERE u.role = 'student' AND ssr.status = 'Approved'
        GROUP BY s.sport_id, s.name";

$result = $conn->query($sql);
$counts = [];  // Array keyed by sport_id
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $counts[$row['sport_id']] = [
            'name' => $row['sport_name'],
            'count' => $row['student_count']
        ];
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sports Club - Sabaragamuwa University</title>
  <link rel="icon" type="image/x-icon" href="images/Favicon.png">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Reset + base */
    * { margin: 0; padding: 0; box-sizing: border-box;}

    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #ffffff; color: #222;}

    /* Navbar */
    .navbar { background-color: rgba(62, 105, 145, 0.95);padding: 10px 0;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);transition: background-color 0.3s ease, padding 0.2s ease;}
    .navbar-scrolled { background-color: rgba(62, 105, 145, 0.95) !important; padding: 6px 0;}
    .navbar-brand {display: flex;align-items: center;color: #fff !important;font-weight: 600;}
    .logo-circle {width: 53px;height: 53px;background: #fff;border-radius: 50%;display: flex;align-items: center;justify-content: center;margin-right: 12px;}
    .logo-circle img {width: 42px;height: 42px;object-fit: contain;}
    .brand-text {display: flex;flex-direction: column;line-height: 1.1;}
    .brand-title {font-size: 17px;font-weight: 700;color: #fff;}
    .brand-subtitle {font-size: 14px;color: rgba(255, 255, 255, 0.95);font-weight: 400;}
    .navbar-nav .nav-link {color: rgba(255, 255, 255, 0.95) !important;margin: 0 10px;font-weight: 500;font-size: 15px;}
    .navbar-nav .nav-link.btn-register,
    .navbar-nav .nav-link.btn-login {
      margin-left: 8px;
    }
    .btn-register,
    .btn-login {
      padding: 8px 22px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
      transition: all .2s;
    }
    .btn-register {background: #dc3545; border: none; color: #fff;}
    .btn-register:hover {background: #e21126ff;color: #fff;}
    .navbar-nav .nav-link.btn-login {background: #fff !important;border: none !important;color: #1e3a55 !important;}
    .navbar-nav .nav-link.btn-login:hover {background: #ffffffe4 !important;color: #1e3a55 !important;}

    /* Carousel */
    .carousel-item {height: 600px;}
    .carousel-item img {width: 100%;height: 100%;object-fit: cover;display: block;}
    .carousel-caption {padding: 18px;border-radius: 10px;max-width: 720px;margin: 0 auto;background: none;}
    .carousel-caption h2 {font-size: 2.8rem;font-weight: 700;color: #fff;text-shadow: 2px 2px 6px rgba(0,0,0,0.5);}
    .carousel-caption p {font-size: 1.2rem;color: #f0f0f0;margin: 12px 0 20px;text-shadow: 1px 1px 4px rgba(0,0,0,0.4);}

    /* Sections */
    section {padding: 60px 0;}
    section h1 {text-align: center;color: #1e3a55;margin-bottom: 14px;font-weight: 700;font-size: 2.4rem;}
    section p.lead {text-align: center;color: #666;margin-bottom: 30px;font-size: 1.05rem;}

    /* About */
    .about-content {max-width: 920px;margin: 0 auto;text-align: center;color: #444;}
    .about-stats {display: flex;justify-content: space-around;gap: 20px;margin-top: 32px;flex-wrap: wrap;}
    .stat-box {text-align: center;padding: 16px;min-width: 160px;}
    .stat-number {font-size: 2.6rem;font-weight: 700;color: #1e3a55;}
    .stat-label {margin-top: 8px;color: #666;font-size: 0.98rem;}
    /* Image inside card */
    .about-card img {width: 100%;border-radius: 10px;transition: transform 0.4s ease;}
    /* Hover effect */
    .about-card:hover {transform: translateY(-10px) scale(1.05);box-shadow: 0 15px 30px rgba(0,0,0,0.15);}
    .about-card:hover img {transform: scale(1.12);}
    /* Title under image */
    .about-card .card-title {font-size: 1.1rem;font-weight: 600;color: #222;margin-top: 8px;}


   /* Categories */ 
   #categories {background: #f8f9fa;} 
   #categories .container {width: 90%;max-width: 1200px;margin: 0 auto;padding: 40px 0;text-align: center;} 
   #categories h1 {font-size: 2.5rem;margin-bottom: 10px;} 
   #categories p.lead {font-size: 1.1rem;color: #666;margin-bottom: 40px;} 
   #categories .row {display: grid;grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));gap: 30px;} 
   #categories .card {background: #fff;border-radius: 12px;box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);overflow: hidden;transition: transform 0.3s ease, box-shadow 0.3s ease;} 
   #categories .card:hover {border: 2px solid #1e5eff;border-radius: 8px;transition: border 0.2s ease;transform: translateY(-8px);box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);} 
   #categories .card img {width: 100%;height: 180px;object-fit: left;border-radius: 8px;transition: border 0.2s ease, transform 0.3s ease;border: 3px solid transparent;cursor: pointer;} 
   #categories .card img:active, .card img:hover {transform: scale(1.1);} 
   #categories .card-content {padding: 20px;text-align: left;} 
   #categories .card-content h3 {font-size: 1.3rem;margin-bottom: 8px;} 
   #categories .about-content p {font-size: 0.95rem;color: #555;margin-bottom: 10px;} 
   #categories .participants {font-weight: 600;color: #444;} 
   #categories .participants span {color: #1e5eff;} 
   #categories .btn {display: inline-block;width: 100%;padding: 10px 0;background: #1e5eff;color: #fff;border: none;border-radius: 8px;cursor: pointer;font-size: 1rem;transition: background 0.3s ease;} 
   #categories .btn:hover {background: #0d47a1;} 
   #categories .card.hidden {display: none;} 
   #categories .view-more-container {text-align: center;margin: 40px 0 20px;} 
   #categories .view-more-btn {background-color: transparent;color: #0b52d7;border: 2px solid #0b52d7;padding: 15px 40px;border-radius: 8px;cursor: pointer;font-size: 1rem;font-weight: 600;transition: all 0.3s ease;} 
   #categories .view-more-btn:hover {background-color: #0b52d7; color: white;} 
   #categories .view-more-btn.hidden {display: none;}


    /* Events */
    #events {background: #ffffff;}
    .event-card {border-radius: 15px;transition: transform 0.5s ease, box-shadow 0.5s ease; overflow: hidden;}
    .event-card:hover {transform: translateY(-5px) scale(1.03); box-shadow: 0 10px 25px rgba(13, 110, 253, 0.25);}
    .event-card img {height: 220px;object-fit: cover;transition: transform 0.5s ease; }
    .event-card:hover img {transform: scale(1.06);}
    .event-info p {font-size: 0.9rem;margin: 0;}
    #events .btn-primary {background-color: #0d95fd;border: none;font-weight: 600;border-radius: 10px;transition: background 0.4s ease;}
    #events .btn-primary:hover {background-color: #0b52d7;}
    .subtitle {color: #6c757d;font-size: 1.1rem;}

    /* Help/Contact */
    #help {background: #f8f9fa;}
    .help-card {background: #fff;border-radius: 10px;padding: 26px;text-align: center;transition: all .2s;height: 100%;}
    .help-card:hover {box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);}
    .help-card {position: relative;transition: transform 0.3s ease, box-shadow 0.3s ease;}
    .help-card:hover {transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15) !important;}
    .help-icon {font-size: 2.6rem;color: #1e3a55;margin-bottom: 14px;}
    .contact-info {background: rgba(244, 245, 246, 1);color: black;padding: 28px;border-radius: 10px;margin-top: 22px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);}
    .map-container {margin-top: 25px;border-radius: 8px;overflow: hidden;box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);border: 3px solid rgba(255, 255, 255, 0.2);}
    .map-container iframe {width: 100%;height: 300px;display: block;border: 0;}
    .map-caption {background: rgba(255, 255, 255, 0.1);color: #fff;padding: 10px 15px;font-size: 0.9rem;text-align: center;border-top: 1px solid rgba(255, 255, 255, 0.2);}
    .map-caption a {color: #fff;text-decoration: underline;font-weight: 500;}
    .map-caption a:hover {color: #f0f0f0;text-decoration: none;}

    /* Footer */
    footer {background: #1e3a55;color: #fff;padding: 26px 0;text-align: center;}

    /* Responsive tweaks */
    @media (max-width: 768px) {
      .carousel-item {height: 320px;}
      section h1 {font-size: 1.9rem;}
      .stat-number {font-size: 2rem;}
      .navbar-nav .nav-link {margin: 6px 0;}
      .btn-register,
      .btn-login {
        margin: 6px 0;
      }
      .map-image {height: 250px;}
    }
    
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="#home" aria-label="Sabaragamuwa University Sports Club">
        <div class="logo-circle" aria-hidden="true">
          <!-- replace with real favicon/logo path -->
          <img src="images/Favicon.png" alt="SUSL Logo" onerror="this.onerror=null;this.src='https://via.placeholder.com/34';">
        </div>
        <div class="brand-text">
          <span class="brand-title">Sports Club</span>
          <span class="brand-subtitle">Sabaragamuwa University of Sri Lanka</span>
        </div>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#categories">Sports</a></li>
          <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
          <li class="nav-item"><a class="nav-link" href="#help">Help</a></li>
          <li class="nav-item"><a class="nav-link btn-register" href="#register" onclick="gotoRegister()">Register</a></li>
          <li class="nav-item"><a class="nav-link btn-login" href="#login" onclick="gotoLogin()">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Carousel -->
  <div id="home" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#home" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#home" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#home" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <div class="carousel-inner">
      <div class="carousel-item active" data-bs-interval="1000">
        <img src="images3/imageee2.png" alt="Sports action" onerror="this.onerror=null;this.src='https://via.placeholder.com/1400x600';">
        <div class="carousel-caption d-none d-md-block">
          <h2>Welcome to SUSL Sports Club</h2>
          <p>Excellence in Sports and Athletics</p>
          <a class="btn btn-primary btn-lg" href="#categories">Get Started</a>
        </div>
      </div>

      <div class="carousel-item" data-bs-interval="2000">
        <img src="images3/oriimage.png" alt="Team sports" onerror="this.onerror=null;this.src='https://via.placeholder.com/1400x600';">
        <div class="carousel-caption d-none d-md-block">
          <h2>Join Our Teams</h2>
          <p>Compete at the highest level</p>
          <a class="btn btn-warning btn-lg" href="#" onclick="gotoBookings()">Bookings</a>
        </div>
      </div>

      <div class="carousel-item" data-bs-interval="2000">
        <img src="images3/download.png" alt="Training facilities" onerror="this.onerror=null;this.src='https://via.placeholder.com/1400x600';">
        <div class="carousel-caption d-none d-md-block">
          <h2>Professional Training</h2>
          <p>World-class facilities and coaching</p>
          <a class="btn btn-success btn-lg" href="#help">Learn More</a>
        </div>
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#home" data-bs-slide="prev" aria-label="Previous slide">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#home" data-bs-slide="next" aria-label="Next slide">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>

  <!-- About -->
<section id="about" class="py-5">
  <div class="container mt-4">
    <h1 class="text-center mb-4">About Us</h1>

    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <p class="lead">
          The Sports Club of Sabaragamuwa University promotes fitness, sportsmanship and competitive excellence among students.
        </p>
        <p class="text-muted" style="line-height:1.8;">
          The SUSL Sports Management System is a smart digital platform that brings athletes, coaches, and sports administrators together. 
          It simplifies sport registrations, training updates, events, and communication—making university sports more organized, accessible, and engaging.
        </p>
      </div>
    </div>

    <!-- Stats -->
    <div class="about-stats d-flex justify-content-center gap-5 mt-5 text-center">
      <div>
        <h2 class="stat-number" data-target="15">0</h2>
        <p class="text-muted">Sports Categories</p>
      </div>
      <div>
        <h2 class="stat-number" data-target="500">0</h2>
        <p class="text-muted">Active Athletes</p>
      </div>
      <div>
        <h2 class="stat-number" data-target="150">0</h2>
        <p class="text-muted">Championships Won</p>
      </div>
    </div>

    <!-- IMAGE CARDS -->
    <div class="row g-4 mt-4 justify-content-center">
      <div class="col-md-4 col-sm-6">
        <div class="about-card card border-0 shadow-sm">
          <img src="About/about1.jpg" class="card-img-top" alt="Training">
          <div class="card-body text-center">
            <h5 class="card-title"></h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="about-card card border-0 shadow-sm">
          <img src="About/about2.jpg" class="card-img-top" alt="Facilities">
          <div class="card-body text-center">
            <h5 class="card-title"></h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="about-card card border-0 shadow-sm">
          <img src="About/about3.jpg" class="card-img-top" alt="Coaches">
          <div class="card-body text-center">
            <h5 class="card-title"></h5>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

  <!-- Categories -->
  <section id="categories">
    <div class="container">
      <h1 class="mb-3">Sports Categories</h1>
      <p class="lead mb-4">Discover and register for your favourite sports programs</p>

      <div class="row g-1">
        <div class="card">
          <img src="Sports/images/Cricket.jpg" alt="cricket" width="550px">
          <div class="card-content">
            <h3>Cricket</h3>
            <p>Join competitive cricket leagues and professional training</p>
            <p class="participants">Participants: <span><?php echo isset($counts[1]) ? $counts[1]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Cricket')">View detail</button>
          </div>
        </div>

        <div class="card">
          <img src="Sports/images/baseball.jpg" alt="Baseball" width="550px">
          <div class="card-content">
            <h3>Baseball</h3>
            <p>Professional baseball coaching and team formations</p>
            <p class="participants">Participants: <span><?php echo isset($counts[2]) ? $counts[2]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Baseball')">View Details</button>
          </div>
        </div>

        <div class="card">
          <img src="Sports/images/netball.webp" alt="Netball" width="550px">
          <div class="card-content">
            <h3>Netball</h3>
            <p>Competitive Elle netball programs and tournaments</p>
            <p class="participants">Participants: <span><?php echo isset($counts[3]) ? $counts[3]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Netball')">View Details</button>
          </div>
        </div>

        <div class="card">
          <img src="Sports/images/basketball.jpeg" alt="Basketball" width="550px">
          <div class="card-content">
            <h3>Basketball</h3>
            <p>Basketball leagues with state-of-the-art facilities</p>
            <p class="participants">Participants: <span><?php echo isset($counts[4]) ? $counts[4]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Basketball')">View Details</button>
          </div>
        </div>


        <div class="card hidden">
          <img src="Sports/images/Football.webp" alt="Football" width="550px">
          <div class="card-content">
            <h3>Football</h3>
            <p>Competitive football leagues and training programs</p>
            <p class="participants">Participants: <span><?php echo isset($counts[5]) ? $counts[5]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Football')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/tennis.jpg" alt="Tennis" width="550px">
          <div class="card-content">
            <h3>Tennis</h3>
            <p>Professional tennis coaching and tournaments</p>
            <p class="participants">Participants: <span><?php echo isset($counts[6]) ? $counts[6]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Tennis')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/swimming.jpeg" alt="Swimming" width="550px">
          <div class="card-content">
            <h3>Swimming</h3>
            <p>Competitive swimming programs and training</p>
            <p class="participants">Participants: <span><?php echo isset($counts[7]) ? $counts[7]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Swimming')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/Table-tennis.jpg" alt="Table Tennis" width="550px">
          <div class="card-content">
            <h3>Table Tennis</h3>
            <p>Fast-paced table tennis training and competitions</p>
            <p class="participants">Participants: <span><?php echo isset($counts[8]) ? $counts[8]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('TableTennis')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/athletics.jpg" alt="Athletics" width="550px">
          <div class="card-content">
            <h3>Athletics</h3>
            <p>Track and field events to boost your endurance</p>
            <p class="participants">Participants: <span><?php echo isset($counts[9]) ? $counts[9]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Athletics')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/hockey.jpeg" alt="Hockey" width="550px">
          <div class="card-content">
            <h3>Hockey</h3>
            <p>Competitive hockey teams and inter-university matches</p>
            <p class="participants">Participants: <span><?php echo isset($counts[10]) ? $counts[10]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Hockey')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/elle.jpeg" alt="Elle" width="550px">
          <div class="card-content">
            <h3>Elle</h3>
            <p>Competitive Elle netball programs and tournaments</p>
            <p class="participants">Participants: <span><?php echo isset($counts[11]) ? $counts[11]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Elle')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/karate.jpeg" alt="Karate" width="550px">
          <div class="card-content">
            <h3>Karate</h3>
            <p>Professional martial arts and self-defense programs</p>
            <p class="participants">Participants: <span><?php echo isset($counts[12]) ? $counts[12]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Karate')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/rugby.jpeg" alt="Rugby" width="550px">
          <div class="card-content">
            <h3>Rugby</h3>
            <p>Join the thrill of competitive rugby leagues</p>
            <p class="participants">Participants: <span><?php echo isset($counts[13]) ? $counts[13]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Rugby')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/volleyball.jpeg" alt="Volleyball" width="550px">
          <div class="card-content">
            <h3>Volleyball</h3>
            <p>Indoor and beach volleyball tournaments</p>
            <p class="participants">Participants: <span><?php echo isset($counts[14]) ? $counts[14]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Volleyball')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/weightlifting.jpeg" alt="Weight Lifting" width="550px">
          <div class="card-content">
            <h3>Weight Lifting</h3>
            <p>Strength training and professional competitions</p>
            <p class="participants">Participants: <span><?php echo isset($counts[15]) ? $counts[15]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('WeightLifting')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/wrestling.jpeg" alt="Wrestling" width="550px">
          <div class="card-content">
            <h3>Wrestling</h3>
            <p>Professional wrestling programs and tournaments</p>
            <p class="participants">Participants: <span><?php echo isset($counts[16]) ? $counts[16]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Wrestling')">View Details</button>
          </div>
        </div>

        <div class="card hidden">
          <img src="Sports/images/badminton.webp" alt="Badminton" width="550px">
          <div class="card-content">
            <h3>Badminton</h3>
            <p>Exciting badminton training sessions and inter-college tournaments</p>
            <p class="participants">Participants: <span><?php echo isset($counts[17]) ? $counts[17]['count'] : 0; ?></span></p>
            <button class="btn" onclick="gotoPage('Badminton')">View Details</button>
          </div>
        </div>
      </div>
      <div class="view-more-container">
        <button class="view-more-btn" onclick="toggleCards()">View More Sports</button>
        <button class="view-more-btn hidden" onclick="toggleCards()">Show Less</button>
      </div>
    </div>
  </section>

  <!-- Events -->
  <section id="events">
    <div class="container">
      <div class="text-center my-5">
        <h1 class="fw-bold mb-3">Sport Events</h1>
        <p class="subtitle">Join exciting competitions and represent your team</p>
      </div>

      <div class="row g-4" id="events-container">
        <!-- Dynamic cards will appear here -->
      </div>
    </div>
  </section>

  <!-- Help -->
  <section id="help">
    <div class="container">
      <h1>How Can We Help?</h1>
      <p class="lead">Find answers to common questions and get the support you need</p>

      <div class="row g-4 mb-4 justify-content-center two-cards-container">
        <div class="col-md-5">
          <div class="help-card" onclick="gotoRegister()">
            <div class="help-icon" aria-hidden="true">📝</div>
            <h4 class="help-title">Registration</h4>
            <p class="help-text">New to our sports club? Learn how to register and become a member. We'll guide you step-by-step.</p>
          </div>
        </div>

        <div class="col-md-5">
          <div class="help-card" onclick="gotoBookings()">
            <div class="help-icon" aria-hidden="true">📅</div>
            <h4 class="help-title">Facility Booking</h4>
            <p class="help-text">Book courts, fields, or equipment. Our online system makes reservations easy and available 24/7.</p>
          </div>
        </div>
      </div>

      <div class="contact-info" role="region" aria-label="Contact Information">
        <h4><b>Contact Information</b></h4>
        <div class="row">
          <div class="col-md-4">
            <p><strong>📧 Email:</strong></p>
            <p>sportsclub@susl.ac.lk</p>
          </div>
          <div class="col-md-4">
            <p><strong>📞 Phone:</strong></p>
            <p>+94 45 222 7000</p>
          </div>
          <div class="col-md-4">
            <p><strong>📍 Location:</strong></p>
            <p>Sabaragamuwa University of Sri Lanka,<br>P.O. Box 02, Belihuloya, 70140, Sri Lanka</p>
          </div>
        </div>
        <p style="margin-top:18px;"><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 4:00 PM</p>

        <!-- Google Maps Section -->
        <div class="map-container">
          <iframe
            src="https://maps.google.com/maps?q=sabaragamuwa%20university&t=&z=13&ie=UTF8&iwloc=&output=embed"
            width="100%"
            height="300"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>
  </section>


  <!-- Footer -->
  <footer>
    <div class="container">
      <h5>Sports Club - Sabaragamuwa University</h5>
      <p>Building champions, one athlete at a time</p>
      <p>&copy; 2025 Sabaragamuwa University of Sri Lanka. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Page JS -->
  <script>
    // Smooth scroll for internal nav links (only for same page anchors)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const targetSelector = this.getAttribute('href');
        // allow external hashes like "#!" or similar to be ignored
        if (!targetSelector || targetSelector === '#') return;
        const target = document.querySelector(targetSelector);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          // close responsive navbar after click (mobile)
          const bsCollapse = document.querySelector('.navbar-collapse');
          if (bsCollapse && bsCollapse.classList.contains('show')) {
            const collapseInstance = bootstrap.Collapse.getInstance(bsCollapse);
            if (collapseInstance) collapseInstance.hide();
          }
        }
      });
    });

    // Navbar scrolled class: use scrollY for consistent behavior
    const navbar = document.querySelector('.navbar');

    function updateNavbarScrolled() {
      if (window.scrollY > 10) navbar.classList.add('navbar-scrolled');
      else navbar.classList.remove('navbar-scrolled');
    }
    window.addEventListener('scroll', updateNavbarScrolled, {
      passive: true
    });
    document.addEventListener('DOMContentLoaded', updateNavbarScrolled);

    // Improve carousel accessibility: pause on focus, resume on blur
    const carouselEl = document.querySelector('#home.carousel');
    if (carouselEl) {
      carouselEl.addEventListener('focusin', () => {
        const bs = bootstrap.Carousel.getInstance(carouselEl) || new bootstrap.Carousel(carouselEl);
        bs.pause();
      });
      carouselEl.addEventListener('focusout', () => {
        const bs = bootstrap.Carousel.getInstance(carouselEl) || new bootstrap.Carousel(carouselEl);
        bs.cycle();
      });
    }

    // Event data
    const events = [{
        id: "Slug",
        title: "Slug Competition",
        description: "Annual slug race competition between university teams",
        image: "Events/images/image 01.jpeg"
      },
      {
        id: "InterUniversity",
        title: "Inter University Games",
        description: "Multi-sport championship with universities nationwide",
        image: "Events/images/image 02.jpeg"
      },
      {
        id: "InterFaculty",
        title: "Inter Faculty Sports Meet",
        description: "Annual sports meet between university faculties",
        image: "Events/images/image 03.jpeg"
      }
    ];

    const container = document.getElementById("events-container");

    // Generate cards
    events.forEach(event => {
      const cardHTML = `
        <div class="col-md-4">
          <div class="card shadow event-card h-100">
            <img src="${event.image}" class="card-img-top" alt="${event.title}">
            <div class="card-body">
              <h5 class="card-title text-dark fw-bold">${event.title}</h5>
              <p class="card-text">${event.description}</p>
              <a href="#" class="btn btn-primary w-100" onclick="gotoPageEvent('${event.id}')">View Event Details</a>
            </div>
          </div>
        </div>
      `;
      container.insertAdjacentHTML("beforeend", cardHTML);
    });

  let isExpanded = false;
  const initialCards = 4; // Shows Cricket, Baseball, Netball, Basketball

  function toggleCards() {
    const cards = document.querySelectorAll('#categories .card');
    const buttons = document.querySelectorAll('#categories .view-more-btn');

    if (!isExpanded) {
      // Show all cards
      cards.forEach(card => {
       card.classList.remove('hidden');
      });
      buttons[0].classList.add('hidden'); // Hide "View More Sports"
      buttons[1].classList.remove('hidden'); // Show "Show Less"
      isExpanded = true;
    } else {
      // Hide cards after initial count (keep first 4 visible)
      cards.forEach((card, index) => {
        if (index >= initialCards) {
          card.classList.add('hidden');
        }
      });
      buttons[0].classList.remove('hidden'); // Show "View More Sports"
      buttons[1].classList.add('hidden'); // Hide "Show Less"
      isExpanded = false;

      // Scroll to top of section smoothly
      document.querySelector('#categories').scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  }
}

    function gotoBookings() {
      window.location.href = "Dashboard/Login.php";
    }

    function gotoRegister() {
      window.location.href = "Dashboard/Register.php";
    }

    function gotoLogin() {
      window.location.href = "Dashboard/Login.php";
    }

    // Pages in the Sports categories
    function gotoPage(sport) {
      window.location.href = `Sports/${sport}.php`;
    }

    // Pages in the Sports events
    function gotoPageEvent(eventId) {
      window.location.href = `Events/${eventId}.php`;
    }

    // About page countup
    const counters = document.querySelectorAll('.stat-number');

    const startCounting = (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const counter = entry.target;
          const target = +counter.getAttribute('data-target');

          // LOWER speed = faster animation (try 10, 5, 3)
          const speed = 55;

          let count = 0;
          const increment = Math.ceil(target / speed);

          const update = () => {
            if (count < target) {
              count += increment;
              counter.textContent = count + "+";
              requestAnimationFrame(update); // smoother than setTimeout
            } else {
              counter.textContent = target + "+";
            }
          };

          update();
          observer.unobserve(counter);
        }
      });
    };

    const observer = new IntersectionObserver(startCounting, {
      threshold: 0.5
    });
    counters.forEach(counter => observer.observe(counter));
  </script>
</body>

</html>