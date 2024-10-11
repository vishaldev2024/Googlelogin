<?php
session_start();
$google_oauth_client_id = '1076689055174-9t3jns5vf60vgtt2eb57do83j73cbkf8.apps.googleusercontent.com';
$google_oauth_client_secret = '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';
$google_oauth_redirect_uri = 'https://staging.bdsd.technology/vishal/googleLogin/google-oauth.php';
$google_oauth_version = 'v3';
if (isset($_GET['code']) && !empty($_GET['code'])) {
    $params = [
        'code' => $_GET['code'],
        'client_id' => $google_oauth_client_id,
        'client_secret' => $google_oauth_client_secret,
        'redirect_uri' => $google_oauth_redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close(handle: $ch);
    $response = json_decode($response, true);



    // echo "here is the response";
    // echo "<pre>";
    // print_r( $response );
    // echo "</pre>";
    // die;

    /* $response = Array
    (
       "access_token" =>" ya29.a0AcM612xRGCrSD3I7rVUezKmC1LqnVOyRVOOZS0G8gbM7GoQUuFmsP5rXz-TvBpbncc6GWAUVsgqPUvcOKWe-GPpOuYGeD0vo4JpPT0RErDTSV4Xx46XMcU6-aYZajOFKglRspQC0d5_J1IJF8j5LxGAM-FsNKRc8NSkPHIvpaCgYKAUESARESFQHGX2Mi_Zr1Mt4Pc59PB9U0oUtQag0175",
        "expires_in"=> "3599",
        "refresh_token" => "1//0gJLtRhs5i3usCgYIARAAGBASNwF-L9IrUEu4RirFopCkEJiHX17nQIkzZix1f1rB5lYD3gaI4MNJFBJIE1ZiwcQ0LqJawqVQie8",
        "scope" => "https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile openid",
        "token_type" => "Bearer",
       "id_token" => "eyJhbGciOiJSUzI1NiIsImtpZCI6ImIyNjIwZDVlN2YxMzJiNTJhZmU4ODc1Y2RmMzc3NmMwNjQyNDlkMDQiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXpwIjoiMTA3NjY4OTA1NTE3NC05dDNqbnM1dmY2MHZndHQyZWI1N2RvODNqNzNjYmtmOC5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsImF1ZCI6IjEwNzY2ODkwNTUxNzQtOXQzam5zNXZmNjB2Z3R0MmViNTdkbzgzajczY2JrZjguYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTQ1NDI0NDgyMDAyNjQxMzg3ODkiLCJlbWFpbCI6InZpc2hhbG1hc3RoZWFkQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJhdF9oYXNoIjoiZWhEcEZMQ0JZS1ZBUjlmc1dHbDRsUSIsImlhdCI6MTcyNjc0NTM3OCwiZXhwIjoxNzI2NzQ4OTc4fQ.GSo_kT-rR3e1O43hR3-LpueApfv6Yb6kaH0KpM5K3tSAEhJWebSa-zmBSML26r9zuyWy8WHpas_NTug1dF4XZAmvqgU-R1Wx0vvPOmzX1bFZnf7abTcSRe1etcG9o2nJXjxCY9yUp1O9F02CyNX562KndxNy1ZsaFFr6Y9mNKvyi8VK0Tgfs9u-EoiJtG-ZZApY3FDkBkdLaTHLg5T4ijLHJ-Fkg5YvOlQVLPFT7oaUITVHCN5HRSqyeLYTX0e2EW1gh6uF0E79NfqErcLDH46eBnVbg0_2jnKo3xlgVanYnwaTtvFWy64xcJUZ3tWZ-DWf5h4FAE-COib5EDfT2Jw"
    ); */

    
    if (isset($response['access_token']) && !empty($response['access_token'])) {
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/' . $google_oauth_version . '/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($response, true);
       
        if (isset($profile['email'])) {
            $google_name_parts = [];
            $google_name_parts[] = isset($profile['given_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['given_name']) : '';
            $google_name_parts[] = isset($profile['family_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['family_name']) : '';
            // Authenticate the user
            session_regenerate_id();
            $_SESSION['google_loggedin'] = TRUE;
            $_SESSION['google_email'] = $profile['email'];
            $_SESSION['google_name'] = implode(' ', $google_name_parts);
            $_SESSION['google_picture'] = isset($profile['picture']) ? $profile['picture'] : '';
            // Redirect to profile page
            header('Location: profile.php');
            exit;
        } else {
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        exit('Invalid access token! Please try again later!');
    }
} else {
    // Define params and redirect to Google Authentication page
    $params = [
        'response_type' => 'code',
        'client_id' => $google_oauth_client_id,
        'redirect_uri' => $google_oauth_redirect_uri,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit;
}
?>