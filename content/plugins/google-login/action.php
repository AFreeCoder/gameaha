<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );

if(is_login()){
	return;
}

function verify_google_token($token) {
	$client_id = get_option('google_client_id'); // Replace with your Google Client ID

	// Google's token verification endpoint
	$url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $token;

	// Initialize cURL session
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Execute cURL session and get the response
	$response = curl_exec($ch);
	curl_close($ch);

	// Decode the JSON response
	$data = json_decode($response, true);

	// Check if the token is valid and issued for our client ID
	if (isset($data['aud']) && $data['aud'] == $client_id) {
		return $data;
	} else {
		return false;
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Read the raw POST data from the request body
	$rawData = file_get_contents('php://input');
	// Decode the JSON data into a PHP associative array
	$input = json_decode($rawData, true);

	if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
		echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
		exit;
	}

	if (isset($input['token'])) {
		$token = $input['token'];
		$data = verify_google_token($token);
		if ($data) {
			// Token is valid, process the login
			$_SESSION['google-login-data'] = [];
			$_SESSION['google-login-data']['username'] = $data['name'];
			$_SESSION['google-login-data']['sub'] = $data['sub'];
			$_SESSION['google-login-data']['email'] = $data['email'];
			echo json_encode(['status' => 'success', 'data' => $data]);
		} else {
			// Token is invalid
			echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
		}
	} else {
		echo json_encode(['status' => 'error', 'message' => 'No token provided']);
	}
}
?>