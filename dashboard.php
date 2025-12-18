<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$dj_alias = $_SESSION['dj_alias'] ?? 'DJ';
$display_name = $dj_alias !== 'DJ' ? $dj_alias : ($_SESSION['fname'] ?? 'Artist');

// My Mixes
$my_mix_stmt = $conn->prepare("SELECT title, description, soundcloud_link, uploaded_at, genre FROM mixes WHERE user_id = ? ORDER BY uploaded_at DESC");
$my_mix_stmt->bind_param("i", $user_id);
$my_mix_stmt->execute();
$my_mixes = $my_mix_stmt->get_result();

// Discover - newest mixes
$discover_stmt = $conn->query("SELECT m.title, m.description, m.soundcloud_link, m.uploaded_at, u.dj_alias, m.genre 
                              FROM mixes m JOIN users u ON m.user_id = u.id 
                              ORDER BY m.uploaded_at DESC LIMIT 12");

// Community Feed
$feed_stmt = $conn->query("SELECT m.title, m.description, m.soundcloud_link, m.uploaded_at, u.dj_alias, m.genre 
                          FROM mixes m JOIN users u ON m.user_id = u.id 
                          ORDER BY m.uploaded_at DESC LIMIT 20");

// Top DJs - ranked by number of mixes
$top_djs_stmt = $conn->query("SELECT u.id, u.dj_alias, COUNT(m.id) as mix_count 
                             FROM users u 
                             LEFT JOIN mixes m ON u.id = m.user_id 
                             GROUP BY u.id 
                             ORDER BY mix_count DESC LIMIT 10");

// All distinct genres for the dropdown
$genres_stmt = $conn->query("SELECT DISTINCT genre FROM mixes WHERE genre IS NOT NULL AND genre != '' ORDER BY genre");

// Genre filter
$selected_genre = $_GET['genre'] ?? '';
$genre_filter_sql = $selected_genre ? "WHERE m.genre = '" . $conn->real_escape_string($selected_genre) . "'" : '';
$genre_mixes_stmt = $conn->query("SELECT m.title, m.description, m.soundcloud_link, m.uploaded_at, u.dj_alias, m.genre 
                                  FROM mixes m JOIN users u ON m.user_id = u.id 
                                  $genre_filter_sql 
                                  ORDER BY m.uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FusionMix</title>
    <link rel="stylesheet" href="style.css">
    <style>
    /* Background Control */
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

    /* Top Nav */
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
        cursor: pointer;
    }
    .nav-link:hover, .nav-link.active {
        background: linear-gradient(90deg, var(--accent1), var(--accent2));
    }
    .logout-btn {
        padding: 6px 18px;
        background: linear-gradient(90deg, #ff4b2b, #ff416c);
        color: white;
        text-decoration: none;
        border-radius: 30px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* Main Content */
    .content-wrapper {
        margin-left: auto;
        margin-right: auto;
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
    }

    .welcome-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .welcome {
        font-size: 2.8rem;
        background: linear-gradient(90deg, var(--accent1), var(--accent2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .tabs {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .mix-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 30px;
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

    /* Top DJs */
    .dj-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
    }
    .dj-card {
        background: rgba(30, 30, 60, 0.7);
        padding: 25px;
        border-radius: 16px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .dj-rank {
        font-size: 2.2rem;
        opacity: 0.5;
        margin-bottom: 10px;
    }
    .dj-name {
        font-size: 1.4rem;
        font-weight: bold;
        color: var(--accent1);
    }

    /* Genre Filter */
    .genre-filter {
        text-align: center;
        margin-bottom: 40px;
    }
    .genre-select {
        padding: 12px 24px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 30px;
        color: white;
        font-size: 1.1rem;
        min-width: 200px;
    }

    /* Upload Modal - FIXED POSITION */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(8px);
    }
    .modal-content {
        background: rgba(15, 12, 41, 0.95);
        backdrop-filter: blur(15px);
        margin: 10% auto;
        padding: 40px;
        border-radius: 20px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        position: relative;
    }
    .close {
        position: absolute;
        top: 15px;
        right: 25px;
        font-size: 2rem;
        cursor: pointer;
        color: #aaa;
    }
    .close:hover {
        color: white;
    }
    .modal h2 {
        text-align: center;
        margin-bottom: 30px;
        color: var(--accent1);
    }
    .modal label {
        display: block;
        margin: 20px 0 8px;
        font-weight: bold;
    }
    .modal input,
    .modal select,
    .modal textarea {
        width: 100%;
        padding: 12px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: white;
    }
    /* Fix inactive tab buttons readability */
    .tabs .nav-link {
        background: rgba(255, 255, 255, 0.1); /* Subtle dark transparent base */
        color: white; /* White text always */
        border: 1px solid rgba(255, 255, 255, 0.15); /* Light border for definition */
    }

    .tabs .nav-link:hover {
        background: rgba(255, 255, 255, 0.2); /* Slightly lighter on hover */
    }

    .tabs .nav-link.active {
        background: linear-gradient(90deg, var(--accent1), var(--accent2)); /* Gradient only on active */
        color: white;
        border: none;
    }

    /* Same fix for top nav links if needed */
    .top-nav .nav-link {
        background: transparent;
        color: white;
    }

    .top-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .top-nav .nav-link.active {
        background: linear-gradient(90deg, var(--accent1), var(--accent2));
    }
    .modal {
    overflow-y: auto; /* Enable scroll on modal background */
    }

    .modal-content {
        max-height: 90vh; /* Max height with scroll */
        overflow-y: auto; /* Scroll inside modal if needed */
        margin: 5% auto; /* Reduced top margin for more space */
        padding: 40px 30px;
    }
    /* Fix dropdown readability */
    select, .modal select {
        background: rgba(30, 30, 60, 0.9) !important; /* Dark background */
        color: white !important; /* White text */
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    select option {
        background: #0f0c29; /* Dark option background */
        color: white;
    }

    .modal select {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 30px; /* Space for arrow */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }
    /* Search Bar - Centered */
    .search-bar {
            position: relative;
            width: 350px;
            max-width: 100%;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            color: white;
            font-size: 1rem;
            outline: none;
        }

        .search-bar input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-bar input:focus {
            border-color: var(--accent1);
            box-shadow: 0 0 15px rgba(255, 65, 108, 0.3);
        }

        .search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.7);
            pointer-events: none;
        }

        /* Flex layout for nav */
        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px; /* Space between sections */
        }

        .left-links, .right-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        /* Responsive - stack on small screens */
        @media (max-width: 1000px) {
            .top-nav {
                flex-wrap: wrap;
                padding: 10px 20px;
                height: auto;
            }
            .search-bar {
                order: 3;
                width: 100%;
                margin: 10px 0;
            }
            .left-links, .right-links {
                gap: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Background -->
    <div class="bg-wrapper">
        <img src="https://images.pexels.com/photos/1570651/pexels-photo-1570651.jpeg?auto=compress&cs=tinysrgb&w=1920" alt="Moody rave crowd" class="bg-image">
        <div class="bg-overlay"></div>
    </div>

    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="logo-nav">
            <div class="badge">🎧</div>
            <div>FusionMix</div>
        </div>

        <div class="nav-links left-links">
            <a href="#" class="nav-link" onclick="openTab('my-mixes'); return false;">My Mixes</a>
            <a href="#" class="nav-link" onclick="openTab('discover'); return false;">Discover</a>
            <a href="#" class="nav-link" onclick="openTab('top-djs'); return false;">Top DJs</a>
            <a href="#" class="nav-link" onclick="openTab('genres'); return false;">Genres</a>
        </div>

        <!-- Search Bar in the Center -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search mixes, DJs, genres..." onkeyup="performSearch()">
            <span class="search-icon">🔍</span>
        </div>

        <div class="nav-links right-links">
            <a href="#" class="nav-link" onclick="openTab('community'); return false;">Community Feed</a>
            <button class="nav-link" onclick="openUploadModal()">+ Upload Mix</button>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="welcome-header">
            <h1 class="welcome">Welcome back, <?= htmlspecialchars($display_name) ?> 🎧</h1>
            <p>Drop heat. Discover vibes. Own the decks.</p>
        </div>

        <div class="tabs">
            <button class="nav-link active" onclick="openTab('my-mixes')">My Mixes</button>
            <button class="nav-link" onclick="openTab('discover')">Discover</button>
            <button class="nav-link" onclick="openTab('top-djs')">Top DJs</button>
            <button class="nav-link" onclick="openTab('genres')">Genres</button>
            <button class="nav-link" onclick="openTab('community')">Community Feed</button>
            <button class="nav-link" onclick="openUploadModal()">+ Upload Mix</button>
        </div>

        <!-- My Mixes -->
        <div id="my-mixes" class="tab-content active">
            <h2 style="text-align:center;margin-bottom:30px;">Your Mixes</h2>
            <div class="mix-grid">
                <?php if ($my_mixes->num_rows > 0): ?>
                    <?php while ($mix = $my_mixes->fetch_assoc()): ?>
                        <div class="mix-card">
                            <div class="mix-title"><?= htmlspecialchars($mix['title']) ?></div>
                            <div class="mix-meta"><?= htmlspecialchars($mix['genre'] ?? 'Various') ?> • <?= date('M j, Y', strtotime($mix['uploaded_at'])) ?></div>
                            <?php if ($mix['description']): ?><p><?= nl2br(htmlspecialchars($mix['description'])) ?></p><?php endif; ?>
                            <?php if ($mix['soundcloud_link']): ?>
                                <div class="soundcloud-embed">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay"
                                        src="https://w.soundcloud.com/player/?url=<?= urlencode($mix['soundcloud_link']) ?>&color=%23ff416c">
                                    </iframe>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-mixes">
                        <p>You haven't uploaded any mixes yet.</p>
                        <p>Time to share your sound with the world! 🔥</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Discover -->
        <div id="discover" class="tab-content">
            <h2 style="text-align:center;margin-bottom:30px;">Fresh Drops – Discover New Heat</h2>
            <div class="mix-grid">
                <?php if ($discover_stmt->num_rows > 0): ?>
                    <?php while ($mix = $discover_stmt->fetch_assoc()): ?>
                        <div class="mix-card">
                            <div class="mix-title"><?= htmlspecialchars($mix['title']) ?></div>
                            <div class="mix-meta">by <?= htmlspecialchars($mix['dj_alias']) ?> • <?= htmlspecialchars($mix['genre'] ?? 'Various') ?></div>
                            <?php if ($mix['soundcloud_link']): ?>
                                <div class="soundcloud-embed">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay"
                                        src="https://w.soundcloud.com/player/?url=<?= urlencode($mix['soundcloud_link']) ?>&color=%23ff416c">
                                    </iframe>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-mixes">No fresh drops yet — be the first!</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top DJs -->
        <div id="top-djs" class="tab-content">
            <h2 style="text-align:center;margin-bottom:30px;">Top DJs on FusionMix</h2>
            <div class="dj-list">
                <?php $rank = 1; while ($dj = $top_djs_stmt->fetch_assoc()): ?>
                    <div class="dj-card">
                        <div class="dj-rank">#<?= $rank++ ?></div>
                        <div class="dj-name"><?= htmlspecialchars($dj['dj_alias'] ?? 'DJ') ?></div>
                        <p><?= $dj['mix_count'] ?> mix<?= $dj['mix_count'] == 1 ? '' : 'es' ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Genres -->
        <div id="genres" class="tab-content">
            <h2 style="text-align:center;margin-bottom:20px;">Browse by Genre</h2>
            <div class="genre-filter">
                <select class="genre-select" onchange="location = 'dashboard.php?genre=' + encodeURIComponent(this.value);">
                    <option value="">All Genres</option>
                    <?php while ($g = $genres_stmt->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($g['genre']) ?>" <?= $selected_genre === $g['genre'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['genre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mix-grid">
                <?php if ($genre_mixes_stmt->num_rows > 0): ?>
                    <?php while ($mix = $genre_mixes_stmt->fetch_assoc()): ?>
                        <div class="mix-card">
                            <div class="mix-title"><?= htmlspecialchars($mix['title']) ?></div>
                            <div class="mix-meta">by <?= htmlspecialchars($mix['dj_alias']) ?> • <?= date('M j, Y', strtotime($mix['uploaded_at'])) ?></div>
                            <?php if ($mix['soundcloud_link']): ?>
                                <div class="soundcloud-embed">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay"
                                        src="https://w.soundcloud.com/player/?url=<?= urlencode($mix['soundcloud_link']) ?>&color=%23ff416c">
                                    </iframe>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-mixes">
                        <p>No mixes in this genre yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Community Feed -->
        <div id="community" class="tab-content">
            <h2 style="text-align:center;margin-bottom:30px;">Community Feed</h2>
            <div class="mix-grid">
                <?php if ($feed_stmt->num_rows > 0): ?>
                    <?php while ($mix = $feed_stmt->fetch_assoc()): ?>
                        <div class="mix-card">
                            <div class="mix-title"><?= htmlspecialchars($mix['title']) ?></div>
                            <div class="mix-meta">by <?= htmlspecialchars($mix['dj_alias']) ?> • <?= htmlspecialchars($mix['genre'] ?? 'Various') ?> • <?= date('M j, Y', strtotime($mix['uploaded_at'])) ?></div>
                            <?php if ($mix['soundcloud_link']): ?>
                                <div class="soundcloud-embed">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay"
                                        src="https://w.soundcloud.com/player/?url=<?= urlencode($mix['soundcloud_link']) ?>&color=%23ff416c">
                                    </iframe>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-mixes">The community is waiting for your mix...</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Upload Modal -->
        <div id="uploadModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeUploadModal()">×</span>
                <h2>Upload New Mix</h2>
                <form action="upload_mix.php" method="post">
                    <h2 style="font-size:2rem;margin-bottom:30px;">What's your mix about?</h2>

                    <label>Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Deep House Sunset 2025">

                    <label>Genre *</label>
                    <select name="genre" required>
                        <option value="">Select genre</option>
                        <option>House</option>
                        <option>Techno</option>
                        <option>Drum & Bass</option>
                        <option>Hip Hop</option>
                        <option>Trap</option>
                        <option>Dubstep</option>
                        <option>Amapiano</option>
                        <option>Afrobeat</option>
                        <option>Chill</option>
                        <option>Other</option>
                    </select>

                    <label>Description (optional)</label>
                    <textarea name="description" rows="5" placeholder="What's the vibe of this mix? Tracklist? Story behind it?"></textarea>

                    <label>SoundCloud Link *</label>
                    <input type="url" name="soundcloud_link" required placeholder="https://soundcloud.com/yourname/your-mix">

                    <button type="submit" style="width:100%;padding:16px;margin-top:30px;background:linear-gradient(90deg,var(--accent1),var(--accent2));border:none;border-radius:50px;color:white;font-size:1.2rem;font-weight:bold;cursor:pointer;">Upload Mix</button>
                </form>
            </div>
        </div>

    <script>
        function openUploadModal() {
                document.getElementById('uploadModal').style.display = 'block';
            }
            function closeUploadModal() {
                document.getElementById('uploadModal').style.display = 'none';
            }
            // Close modal if click outside
            window.onclick = function(event) {
                const modal = document.getElementById('uploadModal');
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        function openTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            document.querySelectorAll('.nav-link').forEach(btn => {
                if (btn.getAttribute('onclick') === "openTab('" + tabName + "')") btn.classList.add('active');
            });
        }
        function performSearch() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.mix-card');

            cards.forEach(card => {
                const title = card.querySelector('.mix-title')?.textContent.toLowerCase() || '';
                const meta = card.querySelector('.mix-meta')?.textContent.toLowerCase() || '';
                const description = card.querySelector('p')?.textContent.toLowerCase() || '';

                const matches = title.includes(query) || meta.includes(query) || description.includes(query);

                card.style.display = matches || query === '' ? 'block' : 'none';
            });

            // Optional: highlight active tab as Community or Discover
            if (query !== '') {
                openTab('community'); // Show community feed when searching
            }
        }
    </script>
</body>
</html>

<?php
$my_mix_stmt->close();
mysqli_close($conn);
?>