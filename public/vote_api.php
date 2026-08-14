<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
jsonResponse(['success' => false, 'message' => 'Coming soon. Check back later.'], 503);
