<?php
include("connection.php");

// Public community feed - latest 20 mixes
$feed_stmt = $conn->query("SELECT m.title, m.description, m.soundcloud_link, m.uploaded_at, u.dj_alias 
                          FROM mixes m 
                          JOIN users u ON m.user_id = u.id 
                          ORDER BY m.uploaded_at DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Feed - FusionMix</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Background Control - same as dashboard */
        .bg-wrapper {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -2;
            overflow: hidden;
        }
        .bg-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(4px);
        }
        .bg-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(15, 12, 41, 0.88), rgba(48, 43, 99, 0.82));
            z-index: 1;
        }

        /* Smaller Top Nav - Guest Version */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(15, 12, 41, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 10px 30px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
        }
        .logo-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
        }
        .logo-nav .badge { font-size: 1.8rem; }
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 6px 16px;
            border-radius: 30px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
        }
        .signup-btn {
            padding: 6px 18px;
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Foundation Container */
        .content-wrapper {
            position: relative;
            z-index: 2;
            max-width: 1300px;
            margin: 90px auto 50px;
            padding: 40px;
            background: rgba(15, 12, 41, 0.93);
            backdrop-filter: blur(18px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
            text-align: center;
        }

        .page-title {
            font-size: 2.8rem;
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }
        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 40px;
        }

        .mix-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        .mix-card {
            background: rgba(20, 20, 50, 0.8);
            backdrop-filter: blur(8px);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .mix-title {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: var(--accent1);
        }
        .mix-meta {
            font-size: 0.95rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        .no-mixes {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            font-size: 1.4rem;
            opacity: 0.7;
        }
        .soundcloud-embed {
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
        }
        .cta-section {
            margin: 50px 0;
        }
        .cta-btn {
            padding: 14px 32px;
            margin: 0 12px;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .cta-primary {
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            color: white;
        }
        .cta-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

    <!-- Background -->
    <div class="bg-wrapper">
        <img src="https://images.pexels.com/photos/1570651/pexels-photo-1570651.jpeg?auto=compress&cs=tinysrgb&w=1920" alt="Moody rave crowd" class="bg-image">
        <div class="bg-overlay"></div>
    </div>

    <!-- Top Nav - Public -->
    <nav class="top-nav">
        <div class="logo-nav">
            <div class="badge">🎧</div>
            <div>FusionMix</div>
        </div>
        <div class="nav-links">
            <a href="public_feed.php" class="nav-link active">Community Feed</a>
            <a href="index.php" class="nav-link">Sign Up</a>
            <a href="login.php" class="nav-link">Log In</a>
        </div>
    </nav>

    <!-- Public Feed Foundation -->
    <div class="content-wrapper">
        <h1 class="page-title">Community Feed</h1>
        <p class="page-subtitle">Discover fresh mixes from DJs worldwide</p>

        <div class="cta-section">
            <a href="index.php" class="cta-btn cta-primary">Join & Upload Your Mix</a>
            <a href="login.php" class="cta-btn cta-secondary">Log In</a>
        </div>

        <div class="mix-grid">
            <?php if ($feed_stmt->num_rows > 0): ?>
                <?php while ($mix = $feed_stmt->fetch_assoc()): ?>
                    <div class="mix-card">
                        <div class="mix-title"><?= htmlspecialchars($mix['title']) ?></div>
                        <div class="mix-meta">by <?= htmlspecialchars($mix['dj_alias'] ?? 'Anonymous DJ') ?> • <?= date('M j, Y', strtotime($mix['uploaded_at'])) ?></div>
                        <?php if ($mix['description']): ?>
                            <p><?= nl2br(htmlspecialchars($mix['description'])) ?></p>
                        <?php endif; ?>
                        <?php if ($mix['soundcloud_link']): ?>
                            <div class="soundcloud-embed">
                                <iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay"
                                    src="https://w.soundcloud.com/player/?url=<?= urlencode($mix['soundcloud_link']) ?>&color=%23ff416c&auto_play=false">
                                </iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-mixes">
                    <p>The community is quiet right now...</p>
                    <p>Be the first to sign up and drop a mix to start the wave! 🎉</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>