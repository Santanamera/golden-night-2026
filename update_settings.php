<?php
require_once 'includes/config.php';
$db = getDB();
$stmt = $db->prepare("INSERT INTO prom_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
$stmt->execute(['prom_venue_phone', '+250 780153944']);
echo "updated\n";
