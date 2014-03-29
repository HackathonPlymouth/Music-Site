<?
require_once('includes/core.php');
define('AUTHORIZATION_ENDPOINT','https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize');
define('CLIENT_ID','ee405bc7316bae78c36e38d522fd290d');
define('CLIENT_SECRET','850bab885775b5b7');
define('CALLBACK_URL','http://www.edmundgentle.com/snippets/music/login/');
define('ACCESS_TOKEN_ENDPOINT','https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/tokenservice');
define('PROFILE_ENDPOINT','https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/userinfo');
function run_curl($url, $method = 'GET', $postvals = null){
    $ch = curl_init($url);
    if ($method == 'GET'){
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSLVERSION => 3
        );
        curl_setopt_array($ch, $options);
    } else {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_VERBOSE => 1,
            CURLOPT_POSTFIELDS => $postvals,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSLVERSION => 3
        );
        curl_setopt_array($ch, $options);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}
if(!isset($_GET['code']) or !isset($_GET['state']) or !isset($_SESSION['login_state'])) {
	//redirect with params to authorization endpoint
	$scopes='openid email';
	$state=generate_string(10);
	$_SESSION['login_state']=$state;
	$auth_url = sprintf("%s?client_id=%s&response_type=code&scope=%s&redirect_uri=%s&nonce=%s&state=%s",AUTHORIZATION_ENDPOINT,CLIENT_ID,$scopes,urlencode(CALLBACK_URL),time() . rand(),$state);
	header("Location: $auth_url");
	exit();
}else{
	//auth code returned
	if($_SESSION['login_state']==$_GET['state']) {
		//exchange token for access token to access token endpoint
		$code = $_GET['code'];
		$postvals = sprintf("client_id=%s&client_secret=%s&grant_type=authorization_code&code=%s",CLIENT_ID,CLIENT_SECRET,$code,urlencode(CALLBACK_URL));
		//returns json that contains access_token, refresh_token and id_token
		$token = json_decode(run_curl(ACCESS_TOKEN_ENDPOINT, "POST", $postvals),true);
		if(isset($token['access_token'])) {
			$_SESSION['access_token']=$token['access_token'];
			//pass access_token to retrieve profile to profile endpoint
			$profile_url = sprintf("%s?schema=openid&access_token=%s",PROFILE_ENDPOINT,$token['access_token']);
			$profile = json_decode(run_curl($profile_url),true);
			//returns user profiles
			if(isset($profile['user_id'])) {
				$result=mysql_query("SELECT artist_id FROM artist WHERE paypal_id='".mysql_real_escape_string($profile['user_id'])."'");
				if(mysql_num_rows($result)==1) {
					list($artist_id) = mysql_fetch_array($result, MYSQL_NUM);
					$_SESSION['user_id']=$artist_id;
				}else{
					do {
						$artist_id = generate_string(10);
					}while(mysql_num_rows(mysql_query("SELECT * FROM artist WHERE artist_id='$artist_id'")));
					mysql_query("INSERT INTO artist (artist_id, paypal_id, name) VALUES ('$artist_id','".mysql_real_escape_string($profile['user_id'])."','')");
					$_SESSION['user_id']=$artist_id;
				}
			}
		}
		header("Location: ".BASE_URI."dashboard/");
		exit();
	}else{
		echo'State doesn\'t match';
	}
}
?>