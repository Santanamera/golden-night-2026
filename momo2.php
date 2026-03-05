<?php
$sub = 'd71acf6855ad4c1391ab52e041f6e783';
$base = 'https://sandbox.momodeveloper.mtn.com';

// Generate UUID
$uid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff),
    mt_rand(0,0x0fff)|0x4000,mt_rand(0,0x3fff)|0x8000,
    mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff));

echo "<pre style='background:#111;color:#D4AF37;padding:20px;font-size:16px;'>";
echo "Subscription Key: $sub\n";
echo "API User UUID: $uid\n\n";

// Step 1: Create API User
$ch = curl_init($base.'/v1_0/apiuser');
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode(['providerCallbackHost'=>'goldennight2026.kesug.com']),
    CURLOPT_HTTPHEADER=>['X-Reference-Id: '.$uid,'Ocp-Apim-Subscription-Key: '.$sub,'Content-Type: application/json'],
    CURLOPT_TIMEOUT=>20,CURLOPT_SSL_VERIFYPEER=>false,
]);
$r1 = curl_exec($ch);
$c1 = curl_getinfo($ch,CURLINFO_HTTP_CODE);
$e1 = curl_error($ch);
curl_close($ch);

echo "Step 1 HTTP: $c1\n";
if($e1) echo "cURL error: $e1\n";
echo "Response: $r1\n\n";

if($c1 !== 201){ echo "FAILED at step 1. Stopping."; exit; }

// Step 2: Create API Key
$ch2 = curl_init($base.'/v1_0/apiuser/'.$uid.'/apikey');
curl_setopt_array($ch2,[
    CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>'',
    CURLOPT_HTTPHEADER=>['Ocp-Apim-Subscription-Key: '.$sub],
    CURLOPT_TIMEOUT=>20,CURLOPT_SSL_VERIFYPEER=>false,
]);
$r2 = curl_exec($ch2);
$c2 = curl_getinfo($ch2,CURLINFO_HTTP_CODE);
$e2 = curl_error($ch2);
curl_close($ch2);

echo "Step 2 HTTP: $c2\n";
if($e2) echo "cURL error: $e2\n";
echo "Response: $r2\n\n";

$key = json_decode($r2,true)['apiKey'] ?? 'NOT FOUND';

echo "=== COPY THESE INTO momo_request.php ===\n\n";
echo "define('MOMO_API_USER', '$uid');\n";
echo "define('MOMO_API_KEY',  '$key');\n";
echo "</pre>";
?>
