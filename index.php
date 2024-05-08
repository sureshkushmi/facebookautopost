<?php
// Function to post data to Facebook
function postToFacebook($postData, $pageId) {
    $url = "https://graph.facebook.com/$pageId/feed";

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

// Function to upload image from local file to Facebook
function uploadImageFromURLToFacebook($imagePath, $facebookAccessToken) {
    $url = "https://graph.facebook.com/me/photos";

    $postData = array(
        "access_token" => $facebookAccessToken,
        "source" => new CURLFile($imagePath)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo "Error in the line: " . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

// Database connection details
$host = 'localhost';
$dbname = 'ypnepal_database';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from the database
    $stmt = $pdo->query('SELECT name, description, add1, cityId, phone1, phone2, email, image FROM listings where id=50819');
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Access tokens and page ID
    $facebookAccessToken = "";
    $pageId = "288361071033119"; // Replace with your Facebook Page ID

// Loop through the fetched data and post each entry to Facebook
foreach ($locations as $location) {
    // Construct the message with all the data in the desired order
    $message = "Name: " . $location['name'] . "\n";

    if (!empty($location['image'])) {
        // Upload image to Facebook
        $imageUploadResult = uploadImageFromURLToFacebook("https://ypnepal.com/uploads/listing/".$location['image']."", $facebookAccessToken);
        $imageData = json_decode($imageUploadResult, true);

        // Check if image upload was successful
        if (isset($imageData['id'])) {
            $imageId = $imageData['id'];

            // Add the image ID to the message
            //$message .= "Image: [fb:image:$imageId]\n";
        } else {
            echo "Failed to upload image to Facebook. Error: " . $imageUploadResult . "<br>";
        }
    }

    $message .= "Description: " . $location['description'] . "\n";
    $message .= "Address: " . $location['add1'] . "\n";
    $message .= "Phone: " . $location['phone1'] . "\n";

    // Create an array with the message
    $postData = array(
        "message" => $message,
        "access_token" => $facebookAccessToken
    );

    // Post to Facebook
    $postResultFacebook = postToFacebook($postData, $pageId);

    // Handle response
    if ($postResultFacebook) {
        echo "Message posted to Facebook successfully.<br>";
    } else {
        echo "Failed to post message to Facebook.<br>";
    }
}

} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    die();
}



