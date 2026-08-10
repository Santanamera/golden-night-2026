<?php
// Get database for prom settings
require_once '../includes/config.php';

$db = getDB();
$settings = getSetting(['prom_name', 'prom_date', 'prom_time', 'prom_venue', 'prom_venue_address', 'prom_venue_description', 'prom_venue_phone'], '');

$promName = $settings['prom_name'] ?? 'Golden Night 2026';
$promDate = $settings['prom_date'] ?? 'June 28, 2026';
$promTime = $settings['prom_time'] ?? '4:00 PM - 10:00 PM';
$promVenue = $settings['prom_venue'] ?? 'RAKKA Hotel';
$promVenueAddress = $settings['prom_venue_address'] ?? 'KN 4 Ave, Kigali, Rwanda';
$promVenueDescription = $settings['prom_venue_description'] ?? 'RAKKA Hotel is the confirmed prom venue for Golden Night 2026.';
$promVenuePhone = $settings['prom_venue_phone'] ?? '+250 780153944';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prom Information - <?php echo htmlspecialchars($promName); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(180deg, #0a0a0a 0%, #161616 100%);
            color: #f5ecd8;
        }

        .navbar {
            background: rgba(10, 10, 10, 0.96);
            border-bottom: 1px solid rgba(212, 175, 55, 0.22);
        }

        .navbar .nav-brand,
        .navbar .nav-menu a {
            color: #f5ecd8;
        }

        .info-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        .hero-banner {
            background: linear-gradient(135deg, #111111 0%, #1f1f1f 50%, #0a0a0a 100%);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 18px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
        }

        .hero-banner h1 {
            font-size: 2.5em;
            color: #D4AF37;
            margin-bottom: 12px;
        }

        .hero-banner p {
            color: #f0e8cc;
            font-size: 1.05em;
            line-height: 1.8;
            margin: 0;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-badges span {
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #f0d060;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.85em;
        }
        
        .info-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
        }
        
        .info-header h1 {
            font-size: 2.5em;
            color: #ffffff;
            margin-bottom: 10px;
        }
        
        .info-header .theme {
            font-size: 1.3em;
            color: #cccccc;
            font-style: italic;
        }
        
        .info-section {
            margin-bottom: 40px;
            background: #111;
            padding: 30px;
            border-radius: 14px;
            border-left: 5px solid #D4AF37;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
        }
        
        .info-section h2 {
            color: #ffffff;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-section h2 .icon {
            font-size: 1.3em;
        }
        
        .info-section p {
            margin: 10px 0;
            line-height: 1.7;
            color: #e8ddbf;
        }
        
        .info-detail {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-detail:last-child {
            border-bottom: none;
        }
        
        .info-detail-label {
            font-weight: 600;
            color: #f0d060;
            min-width: 150px;
        }
        
        .info-detail-value {
            color: #e8ddbf;
            text-align: right;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            margin-bottom: 25px;
            padding-left: 20px;
            position: relative;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -15px;
            top: 5px;
            width: 12px;
            height: 12px;
            background: #D4AF37;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #D4AF37;
        }
        
        .timeline-item .time {
            font-weight: 600;
            color: #D4AF37;
            font-size: 0.95em;
        }
        
        .timeline-item .activity {
            color: #e8ddbf;
            margin-top: 5px;
        }
        
        .faq-item {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #eee;
        }
        
        .faq-item .question {
            font-weight: 600;
            color: #1a1a1a;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-item .question:hover {
            color: #D4AF37;
        }
        
        .faq-item .answer {
            margin-top: 10px;
            color: #555;
            line-height: 1.6;
            display: none;
        }
        
        .faq-item.active .answer {
            display: block;
        }
        
        .info-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            color: #856404;
        }
        
        .info-box strong {
            color: #1a1a1a;
        }
        
        .highlight {
            color: #D4AF37;
            font-weight: 600;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .feature-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin: 20px 0 8px;
        }

        .feature-pill {
            background: #171717;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            color: #e8ddbf;
            font-weight: 600;
        }

        .feature-pill strong {
            color: #D4AF37;
            display: block;
            margin-bottom: 4px;
        }

        .activity-card {
            background: #1c1c1c;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .activity-card h3 {
            color: #f0d060;
            margin-bottom: 8px;
            font-size: 1.05em;
        }

        .activity-card p {
            margin: 0;
            color: #e8ddbf;
            line-height: 1.6;
        }
        
        .button-group {
            text-align: center;
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .button-group a {
            display: inline-block;
            padding: 12px 30px;
            background: #D4AF37;
            color: #111;
            text-decoration: none;
            border-radius: 999px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .button-group a:hover {
            background: #b8931a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }
        
        .button-group a.secondary {
            background: #6c757d;
        }
        
        .button-group a.secondary:hover {
            background: #5a6268;
        }
        
        @media (max-width: 768px) {
            .info-header h1 {
                font-size: 1.8em;
            }
            
            .info-detail {
                flex-direction: column;
            }
            
            .info-detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- DEPLOY-VERIFY-2026-07-24 -->
    <nav class="navbar">
        <div class="nav-brand">Golden Night 2026</div>
        <ul class="nav-menu">
            <li><a href="../">Home</a></li>
            <li><a href="prom-info.php" class="active">Event Info</a></li>
            <li><a href="buy-ticket.php">Get Ticket</a></li>
            <li><a href="audition.php">Auditions</a></li>
            <li><a href="vote.php">Vote</a></li>
        </ul>
    </nav>

    <div class="info-container">
        <div class="hero-banner">
            <h1><?php echo htmlspecialchars($promName); ?></h1>
            <p>Prom night details for the graduating class at RAKKA Hotel, Kigali.</p>
            <div class="hero-badges">
                <span>Formal Prom Night</span>
                <span>Live Music</span>
                <span>Prom King & Queen</span>
                <span>Photo Moments</span>
            </div>
            <div class="button-group" style="justify-content:flex-start; margin-top:20px; gap:12px;">
                <a href="buy-ticket.php">Register Now</a>
                <a href="audition.php" class="secondary">Apply for Royalty</a>
            </div>
        </div>

        <div class="info-header">
            <h1><?php echo htmlspecialchars($promName); ?></h1>
            <p class="theme">Prom information, schedule, and activities.</p>
        </div>

        <!-- Event Details -->
        <div class="info-section">
            <h2><span class="icon">📅</span> Event Details</h2>
            <div class="info-detail">
                <span class="info-detail-label">Date:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promDate); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Time:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promTime); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Venue:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promVenue); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Location:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promVenueAddress); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Contact:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promVenuePhone); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Ticket Price:</span>
                <span class="info-detail-value"><span class="highlight">Single Rwf 20,000 / Couple Rwf 35,000</span></span>
            </div>
        </div>

        <div class="info-section">
            <h2><span class="icon">🎬</span> Event Activities</h2>
            <div class="activity-grid">
                <div class="activity-card">
                    <h3>📸 Red Carpet</h3>
                    <p>Formal entrance with dedicated photo moments for attendees.</p>
                </div>
                <div class="activity-card">
                    <h3>💃 Dance Floor</h3>
                    <p>Music and dancing for the full evening program.</p>
                </div>
                <div class="activity-card">
                    <h3>🎤 Live Music</h3>
                    <p>DJ and student performances on the main stage.</p>
                </div>
                <div class="activity-card">
                    <h3>🏆 Crowning Ceremony</h3>
                    <p>Prom King & Queen announcements and class awards.</p>
                </div>
            </div>
        </div>

        <div class="info-section" style="padding-bottom:24px;">
            <h2><span class="icon">🏨</span> Venue</h2>
            <p style="margin-bottom:22px; color:#e8ddbf; line-height:1.8;">
                <?php echo htmlspecialchars($promVenueDescription); ?>
            </p>
            <div style="display:grid; gap:14px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
                <div style="background: #111; border:1px solid rgba(212,175,55,0.12); border-radius: 14px; padding:18px;">
                    <strong style="color:#D4AF37; display:block; margin-bottom:10px;">Venue Highlights</strong>
                    <ul style="margin:0; padding-left: 22px; color:#d9cfa7; line-height:1.8;">
                        <li>Large ballroom with premium lighting</li>
                        <li>Comfortable seating and dance floor</li>
                        <li>Professional sound system</li>
                        <li>Photo-ready decoration</li>
                        <li>Easy access from Kigali city center</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2><span class="icon">🎉</span> Prom Activities</h2>
            <div class="activity-grid">
                <div class="activity-card">
                    <h3>🎥 Red Carpet Entrance</h3>
                    <p>Pictures, media personalities, and a formal arrival experience.</p>
                </div>
                <div class="activity-card">
                    <h3>💃 Dance Sessions</h3>
                    <p>Multiple dance sessions with exciting twists all night long.</p>
                </div>
                <div class="activity-card">
                    <h3>🍽️ Food & Beverages</h3>
                    <p>Enjoy a selection of food and drinks throughout the event.</p>
                </div>
                <div class="activity-card">
                    <h3>🎤 Surprise Artist</h3>
                    <p>A special surprise artist performance to make the night unforgettable.</p>
                </div>
                <div class="activity-card">
                    <h3>🕹️ Games</h3>
                    <p>Secret Message, card games, and multiple interactive experiences.</p>
                </div>
                <div class="activity-card">
                    <h3>🏆 Awards Giveaway</h3>
                    <p>Multiple awards and prizes will be handed out during the evening.</p>
                </div>
                <div class="activity-card">
                    <h3>🔥 Dance & Rap Battles</h3>
                    <p>Competition rounds for dance and rap talent on stage.</p>
                </div>
                <div class="activity-card">
                    <h3>👑 Prom King & Queen</h3>
                    <p>Announcement, crowning, and special rewards for the winners.</p>
                </div>
                <div class="activity-card">
                    <h3>✨ More Experiences</h3>
                    <p>And so much more… every part of the night is full of surprises.</p>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2><span class="icon">📋</span> Quick Notes</h2>
            <p>Please register your ticket on the <a href="buy-ticket.php" style="color: #D4AF37;">ticket page</a> and complete payment before the event.</p>
            <p>If you want to be a candidate for Prom King or Queen, submit your application on the <a href="audition.php" style="color: #D4AF37;">audition page</a>.</p>
            <p>Use your valid ticket ID to vote on the <a href="vote.php" style="color: #D4AF37;">voting page</a>.</p>
        </div>

        <div class="button-group">
            <a href="buy-ticket.php">🎟️ Buy Ticket</a>
            <a href="audition.php" class="secondary">✨ Apply as Candidate</a>
            <a href="vote.php" class="secondary">🗳️ Vote Now</a>
        </div>
    </div>

    <footer style="background: #111; color: #D4AF37; text-align: center; padding: 22px; margin-top: 40px; border-top: 1px solid rgba(212,175,55,0.14);">
        <p><?php echo htmlspecialchars($promName); ?> 2026 | RAKKA Hotel • Kigali | All Rights Reserved</p>
    </footer>

</body>
</html>