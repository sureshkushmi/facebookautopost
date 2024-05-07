<?php
$host = 'localhost';
$dbname = 'ypnepal_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT * FROM listings LIMIT 10'); // Query modified to fetch all necessary fields
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    die();
}

$js_locations = json_encode($locations); // Convert PHP array to JSON format
?>

<?php

// Function to post data to Facebook
function postToFacebook($message, $pageId, $accessToken) {
    $url = "https://graph.facebook.com/$pageId/feed";
    
    $postData = array(
        "message" => $message,
        "access_token" => $accessToken
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    // Check for errors
    if($response === false) {
        echo "Error: " . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

// Access tokens and page ID
$facebookAccessToken = "EAAVjleDNSAEBO4NDt0CdXiL6NfZApWzweXnLEMsjZA3uD3atRdMq7dvhEnALdi92mg6JYLBqWR4LYNVAKaijoAblNJUsbuXzWkRnTGylnSdZBL5iZAYJTbWXPFWpRz4xEGpRo2oae9IFuGWoag42wlVs8iV36OjyuSAVd8zXZA7mVXaCF8W64gC5ie4lmiOvSwZAD4czgACxk0B56KpgavzJBT";
$pageId = "288361071033119"; // Replace with your Facebook Page ID

// Message to post
$message = "Hello, this is a test message!";

// Post to Facebook
$postResultFacebook = postToFacebook($message, $pageId, $facebookAccessToken);

// Handle response
if ($postResultFacebook) {
    echo "Message posted to Facebook successfully.";
} else {
    echo "Failed to post message to Facebook.";
}
?>
