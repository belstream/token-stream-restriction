<?php

// === Configuration section ===
// You must set your API key here.
define('API_KEY', ''); // <-- Insert your API key here

// The API endpoint to request a stream token.
define('API_URL', 'https://console.belstream.com/api/token');

// The base URL of the stream (without the token).
define('STREAM_URL', 'https://radio-demo.broadcast.belstream.net/radio-demo-128.mp3');

// The stream type: use "stream" for Icecast or "hls" for HLS streams.
define('MODE', 'stream');

// The stream identifier (e.g., mountpoint name or .m3u8 file name).
define('MOUNTNAME', 'radio-demo-128.mp3');

// How long the token should be valid (in seconds). 3600 = 1 hour.
define('VALIDITY', 3600);

/**
 * Requests a token for a specific stream using Belstream's API.
 *
 * @param string   $mode       Either "stream" or "hls".
 * @param string   $mountName  The stream's mountpoint or file name.
 * @param int|null $window     Optional validity duration (in seconds).
 * @return array               An associative array with the API response.
 */
function getStreamToken($mode, $mountName, $window = null)
{
    // Build the URL for the token request.
    $url = API_URL . '/' . $mode . '/' . urlencode($mountName) . '?apiKey=' . API_KEY;

    // If a time window is provided, include it as JSON in the POST body.
    $data = $window !== null ? json_encode(['window' => $window]) : null;

    // Initialize a cURL session for the HTTP request.
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POST           => $data !== null,  // Use POST only if data is sent.
        CURLOPT_POSTFIELDS     => $data,
    ]);

    // Execute the request and capture the response.
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch); // Always close the cURL session.

    // If something went wrong, return an error.
    if ($httpCode !== 200) {
        return ['status' => 'KO', 'error' => "HTTP $httpCode"];
    }

    // Convert the JSON response to an associative array and return it.
    return json_decode($response, true);
}

// === Main logic ===

// Request a token from the API.
$result = getStreamToken(MODE, MOUNTNAME, VALIDITY);

// If the response is valid, extract the token.
$token = $result['data'] ?? null;

// If token is missing, show an error instead of trying to play.
if (!$token) {
    echo '<p style="color: red;">Error: Unable to retrieve token.</p>';
    if (isset($result['error'])) {
        echo '<p><strong>Details:</strong> ' . htmlspecialchars($result['error']) . '</p>';
    }
    exit;
}

// Display the audio player using the secured URL.
echo '<audio controls>';
echo '<source src="' . STREAM_URL . '?' . $token . '">';
echo 'Your browser does not support the audio element.';
echo '</audio>';
