<?php require_once __DIR__ . '/../config/init.php'; ?>
<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - डिजिटल ग्राम पंचायत</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Marathi:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #00695c; /* Deep Teal */
            --primary-light: #439688;
            --secondary-color: #ffa000; /* Amber */
            --dark-color: #003d33;
            --accent-color: #00bcd4;
            --bg-color: #f4f7f6;
            --white: #ffffff;
            --text-dark: #263238;
            --text-muted: #546e7a;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', 'Noto Sans Marathi', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .navbar {
            background-color: var(--white);
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 8px 0;
            min-height: 80px;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .navbar-brand {
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,105,92,0.3);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-muted) !important;
            margin: 0 10px;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(0, 105, 92, 0.95), rgba(0, 188, 212, 0.8)), url('https://images.unsplash.com/photo-1590059392347-1906a2f8964d?q=80&w=2070&auto=format&fit=crop') no-repeat center center/cover;
            color: white;
            padding: 120px 0;
            text-align: center;
            border-radius: 0 0 50px 50px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            background: var(--white);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .feature-icon {
            font-size: 2.8rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .badge {
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 600;
        }

        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }

        footer {
            background-color: #1a252f;
            color: #fff;
            padding: 70px 0 30px;
            border-radius: 50px 50px 0 0;
            margin-top: 50px;
        }
        footer a:hover {
            color: var(--white) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo APP_URL; ?>">
            <img src="<?php echo APP_URL; ?>/assets/img/rectangular logo.png" alt="GramSetu Logo">
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if(isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-primary" href="<?php echo isAdmin() ? APP_URL.'/admin/dashboard.php' : APP_URL.'/user/dashboard.php'; ?>">
                        डॅशबोर्ड
                    </a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>">मुख्यपृष्ठ</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/user/notices.php">सूचना</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/user/schemes.php">योजना</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/user/events.php">कार्यक्रम</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/emergency.php">आणीबाणी</a>
                </li>
                
                <?php if(isLoggedIn()): ?>
                    <!-- Notification Bell -->
                    <?php 
                    $user_id = $_SESSION['user_id'];
                    $last_seen = null;
                    
                    if (isAdmin()) {
                        $last_seen = $pdo->query("SELECT last_notif_seen FROM admins WHERE id = $user_id")->fetchColumn();
                        $notif_count = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending' AND (created_at > '$last_seen' OR '$last_seen' IS NULL)")->fetchColumn();
                        $latest_items = $pdo->query("SELECT complaints.*, users.full_name FROM complaints JOIN users ON complaints.user_id = users.id WHERE status = 'pending' ORDER BY created_at DESC LIMIT 3")->fetchAll();
                        $notif_title = "नवीन तक्रारी";
                        $view_all_link = APP_URL . "/admin/complaints.php";
                    } else {
                        $last_seen = $pdo->query("SELECT last_notif_seen FROM users WHERE id = $user_id")->fetchColumn();
                        $notif_count = $pdo->query("SELECT COUNT(*) FROM notices WHERE (created_at > '$last_seen' OR '$last_seen' IS NULL)")->fetchColumn();
                        $latest_items = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 3")->fetchAll();
                        $notif_title = "नवीन सूचना";
                        $view_all_link = APP_URL . "/user/notices.php";
                    }
                    ?>
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-bell fs-5 <?php echo isAdmin() ? 'text-warning' : ''; ?>"></i>
                            <?php if($notif_count > 0): ?>
                                <span class="position-absolute top-2 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    <?php echo $notif_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3 p-0" style="width: 300px; border-radius: 12px; overflow: hidden;">
                            <div class="<?php echo isAdmin() ? 'bg-dark' : 'bg-primary'; ?> text-white p-3 text-center">
                                <h6 class="mb-0 small"><?php echo $notif_title; ?></h6>
                            </div>
                            <div class="p-2">
                                <?php if(empty($latest_items)): ?>
                                    <div class="p-3 text-center text-muted small">कोणतेही नवीन अपडेट नाही.</div>
                                <h6 class="mb-0 small">नोटिफिकेशन्स</h6>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item d-flex align-items-center ms-lg-3">
                        <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                            <span class="small fw-bold me-3"><?php echo $_SESSION['full_name']; ?></span>
                            <a href="<?php echo APP_URL; ?>/logout.php" class="text-danger small fw-bold text-decoration-none border-start ps-3">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/login.php">लॉगिन</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-3 px-4 shadow-sm" href="<?php echo APP_URL; ?>/register.php">नोंदणी करा</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>
    <div class="container mt-3">
        <?php 
        $flash = getFlash();
        if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
