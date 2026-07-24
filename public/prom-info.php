<?php
// Get database for prom settings
require_once '../includes/config.php';

$db = getDB();
$settings = getSetting($db, ['prom_name', 'prom_date', 'prom_venue', 'prom_theme']);

$promName = $settings['prom_name'] ?? 'Golden Night 2026';
$promDate = $settings['prom_date'] ?? 'June 28, 2026';
$promVenue = $settings['prom_venue'] ?? 'Imperial Ballroom, City Center';
$promTheme = $settings['prom_theme'] ?? 'An Evening of Elegance & Celebration';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prom Information - <?php echo htmlspecialchars($promName); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .info-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .info-header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 30px;
        }
        
        .info-header h1 {
            font-size: 2.5em;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        
        .info-header .theme {
            font-size: 1.3em;
            color: #666;
            font-style: italic;
        }
        
        .info-section {
            margin-bottom: 40px;
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            border-left: 5px solid #D4AF37;
        }
        
        .info-section h2 {
            color: #1a1a1a;
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
            line-height: 1.6;
            color: #333;
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
            color: #1a1a1a;
            min-width: 150px;
        }
        
        .info-detail-value {
            color: #555;
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
            color: #333;
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
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
        <!-- Header -->
        <div class="info-header">
            <h1><?php echo htmlspecialchars($promName); ?></h1>
            <p class="theme"><?php echo htmlspecialchars($promTheme); ?></p>
        </div>

        <!-- Event Details -->
        <div class="info-section">
            <h2><span class="icon">📅</span> Event Details</h2>
            <div class="info-detail">
                <span class="info-detail-label">Date:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promDate); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Venue:</span>
                <span class="info-detail-value"><?php echo htmlspecialchars($promVenue); ?></span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Event Type:</span>
                <span class="info-detail-value">Formal Dinner & Dance</span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Dress Code:</span>
                <span class="info-detail-value">Formal Attire (Black Tie Recommended)</span>
            </div>
            <div class="info-detail">
                <span class="info-detail-label">Ticket Price:</span>
                <span class="info-detail-value"><span class="highlight">GHS 30,000</span></span>
            </div>
        </div>

        <!-- What to Expect -->
        <div class="info-section">
            <h2><span class="icon">✨</span> What to Expect</h2>
            <p>Join us for an unforgettable evening celebrating our final year together! This year's prom promises to be a spectacular event filled with:</p>
            <ul style="margin: 15px 0; padding-left: 30px; color: #555; line-height: 1.8;">
                <li><strong>Elegant Dining:</strong> A 3-course dinner featuring both local and international cuisine</li>
                <li><strong>Live Entertainment:</strong> Professional DJ, live band performances, and special guest artists</li>
                <li><strong>Crowning Ceremony:</strong> Coronation of Prom King and Queen as voted by attendees</li>
                <li><strong>Photo Opportunities:</strong> Professional photography and dedicated photo booth areas</li>
                <li><strong>Memorable Moments:</strong> First dance, recognition of class superlatives, and special awards</li>
                <li><strong>Dancing & Celebration:</strong> Dance floor open all night with hits from past and present</li>
            </ul>
        </div>

        <!-- Schedule / Timeline -->
        <div class="info-section">
            <h2><span class="icon">⏰</span> Event Schedule</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <span class="time">6:00 PM</span>
                    <span class="activity">Doors Open - Welcome Reception & Cocktails</span>
                </div>
                <div class="timeline-item">
                    <span class="time">6:30 PM</span>
                    <span class="activity">Guests Seated - Dinner Service Begins</span>
                </div>
                <div class="timeline-item">
                    <span class="time">7:45 PM</span>
                    <span class="activity">Opening Remarks & Toast</span>
                </div>
                <div class="timeline-item">
                    <span class="time">8:00 PM</span>
                    <span class="activity">Live Entertainment - Band Performance</span>
                </div>
                <div class="timeline-item">
                    <span class="time">8:45 PM</span>
                    <span class="activity">Dance Floor Opens</span>
                </div>
                <div class="timeline-item">
                    <span class="time">9:30 PM</span>
                    <span class="activity">Class Superlatives & Special Awards</span>
                </div>
                <div class="timeline-item">
                    <span class="time">10:00 PM</span>
                    <span class="activity">Prom King & Queen Coronation Ceremony</span>
                </div>
                <div class="timeline-item">
                    <span class="time">10:15 PM</span>
                    <span class="activity">First Dance & Continued Celebration</span>
                </div>
                <div class="timeline-item">
                    <span class="time">2:00 AM</span>
                    <span class="activity">Event Concludes</span>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="info-section">
            <h2><span class="icon">🎟️</span> How to Participate</h2>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #1a1a1a; margin-bottom: 10px;">1. Purchase Your Ticket</h3>
                <p>Visit our <a href="buy-ticket.php" style="color: #D4AF37; text-decoration: none; font-weight: 600;">ticket portal</a> to purchase your prom ticket. Payment can be made via MTN MoMo. Limited tickets available!</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #1a1a1a; margin-bottom: 10px;">2. Run for King or Queen</h3>
                <p>Interested in being crowned Prom Royalty? Submit your <a href="audition.php" style="color: #D4AF37; text-decoration: none; font-weight: 600;">audition application</a> as a candidate for Prom King or Queen. Voting opens shortly after applications close!</p>
            </div>
            
            <div>
                <h3 style="color: #1a1a1a; margin-bottom: 10px;">3. Vote for Your Favorites</h3>
                <p>Have a ticket? Cast your vote for who you think should be crowned Prom King and Queen. <a href="vote.php" style="color: #D4AF37; text-decoration: none; font-weight: 600;">Visit the voting portal</a> and show your support!</p>
            </div>
        </div>

        <!-- Important Information -->
        <div class="info-section">
            <h2><span class="icon">📋</span> Important Information</h2>
            
            <div class="info-box">
                <strong>✓ Eligibility Requirements:</strong><br>
                All participants must be current or recent alumni of the institution. Proof of enrollment may be required at the door.
            </div>
            
            <div class="info-box">
                <strong>✓ Dress Code Policy:</strong><br>
                Formal attire is required. Gentlemen: Black/Dark suit or tuxedo with tie. Ladies: Formal gown or elegant dressy outfit. Accessorize tastefully. No casual wear, athletic wear, or jeans permitted.
            </div>
            
            <div class="info-box">
                <strong>✓ Plus-One Policy:</strong><br>
                Each ticket admits one (1) person. Plus-ones must be guests from the school community. Advance notice required at ticket purchase.
            </div>
            
            <div class="info-box">
                <strong>✓ Arrival & Parking:</strong><br>
                Doors open at 6:00 PM. Free complimentary parking is available onsite. Please arrive by 7:00 PM to be seated for dinner service.
            </div>
            
            <div class="info-box">
                <strong>✓ Photography & Videography:</strong><br>
                Professional photographers will be present throughout the evening. You may request photos from the photographer. Please inform us in advance if you do not wish to be photographed.
            </div>
        </div>

        <!-- Frequently Asked Questions -->
        <div class="info-section">
            <h2><span class="icon">❓</span> Frequently Asked Questions</h2>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>Can I buy my ticket on the day of the event?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    Tickets are sold exclusively online until the date of the event. Limited walk-in tickets may be available at the door, but online purchase is strongly recommended to guarantee your spot.
                </div>
            </div>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>What payment methods are accepted?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    We accept MTN MoMo payments for maximum convenience. Once you complete payment, you'll receive your ticket ID, QR code, and seat assignment instantly.
                </div>
            </div>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>Is there a refund policy?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    Once purchased, tickets are non-refundable. However, you may transfer your ticket to another eligible person by contacting the event organizers at least 48 hours before the event.
                </div>
            </div>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>How do I apply to be a Prom King/Queen candidate?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    Simply visit our <a href="audition.php" style="color: #D4AF37;">audition page</a>, fill out the application form, and submit your photo and bio. Applications are reviewed and approved within 24-48 hours. Approved candidates then become eligible for voting.
                </div>
            </div>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>Can I vote multiple times?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    No, each ticket ID can only be used to cast one vote. Your vote is recorded and verified to prevent duplicate voting. You can change your vote before voting closes.
                </div>
            </div>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>What if I have dietary restrictions?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    We'll accommodate common dietary needs (vegetarian, vegan, allergies). Please specify your dietary requirements when purchasing your ticket or contact the event team immediately.
                </div>
            </div>
            
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="question">
                    <span>Is there an age limit for attending?</span>
                    <span>▼</span>
                </div>
                <div class="answer">
                    Prom is reserved for eligible class members and their approved guests. All attendees must be 16 years or older. This is a sober, family-friendly event.
                </div>
            </div>
        </div>

        <!-- Call to Action Buttons -->
        <div class="button-group">
            <a href="buy-ticket.php">🎟️ Get Your Ticket Now</a>
            <a href="audition.php">✨ Apply as Candidate</a>
            <a href="vote.php">🗳️ Vote Now</a>
            <a href="../" class="secondary">← Back to Home</a>
        </div>
    </div>

    <footer style="background: #1a1a1a; color: #D4AF37; text-align: center; padding: 20px; margin-top: 50px;">
        <p><?php echo htmlspecialchars($promName); ?> 2026 | All Rights Reserved | <a href="#" style="color: #D4AF37;">Contact Us</a></p>
    </footer>

</body>
</html>
