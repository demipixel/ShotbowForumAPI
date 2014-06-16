<?


$ckfileLoc = "../tmp";
$ckfile = tempnam ($ckfileLoc, "CURLCOOKIE");
$ch;

$strSplitClient = Array();
$strSplitClient[0] = "_xfToken\" value=\"";
$strSplitClient[1] = "\"";

class Client {
	
	private $file = "";
	private $currentQueue = "";
	
	function Client($username,$password) {
		$this->login($username,$password);
	}
	
	function login($username,$password) {
		$ckfile = tempnam ($ckfileLoc, "CURLCOOKIE");
		$url = 'http://shotbow.net/forum/login/login';
		$data = array(
		'login' => $username,
		'password' => $password,
		'cookie_check' => '1',
		'redirect' => '/forum/portal/',
		'remember' => 'on');
		
		global $ckfile, $ch;
		
		
		
		$output = sendPost($url,$data); // Login from data above
		echo $output;
		if (strstr($output,"Incorrect password")) throw new Exception("Incorrect password for " . $username);
		if (strstr($output,"The requested user")) throw new Exception("User " . $username . " was not found.");
		$this->token = $this->getToken(); // Get token to use for rest of session
	}
	
	function readQueue() {
		if ($this->file == null) throw new Exception("Cannot readQueue() without a file");
		getLatest();
	}
	
	function getLatest() { 
		$f = file($this->file);
		$cmdArray = array_splice($f,0,1);
		file_put_contents($this->file,implode("",$f));
		$cmdSer = $cmdArray[0];
		$this->currentQueue = $cmdSer;
		$cmd = unserialize($cmdSer);
		switch ($cmd[0]) {
			case 0: $this->postProfile($cmd[1],$cmd[2]); break;
			case 1: $this->postThread($cmd[1],$cmd[2]); break;
			case 2: $this->newThread($cmd[1],$cmd[2],$cmd[3]); break;
			case 3: $this->newConvo($cmd[1],$cmd[2]); break;
			case 4: $this->editProfilePost($cmd[1],$cmd[2]); break;
			case 5: $this->editThreadPost($cmd[1],$cmd[2]); break;
		}
		
	}
	
	function restoreLatest() {
		$f = file($this->file);
		$f = Array($this->currentQueue) + $f;
		file_put_contents($this->file,implode("",$f));
		$this->currentQueue = "";
	}
	
	function postProfile($id,$message) {
		$url = "http://shotbow.net/forum/members/" . $id . "/post";
		$data = Array(
		'message' => $message,
		'_xfToken' => $this->token);
		$o = sendPost($url,$data);
		return $this->security($o)
	}
	
	function postThread($id,$message) {
		$url = "http://shotbow.net/forum/threads/" . $id . "/add-reply";
		$data = Array(
		'message' => $message,
		'_xfToken' => $this->token);
		$o = sendPost($url,$data);
		return $this->security($o);
	}
	
	function newThread($title,$message,$section) {
		$url = "http://shotbow.net/forum/forums/" . $section . "/add-thread";
		$data = Array(
		'title' => $title,
		'message' => $message,
		'_xfToken' => $this->token);
		$o = sendPost($url,$data);
		return $this->security($o);
	}
	
	function newConvo($title,$users,$message) {
		$url = "http://shotbow.net/forum/conversations/insert"
		$data = Array(
		'recipients' => $users,
		'title' => $title,
		'message' => $message,
		'_xfToken' => $this->token);
		$o = sendPost($url,$data);
		return $this->security($o);
	}
	
	function editProfilePost($id,$message) {
		$url = "http://shotbow.net/forum/profile-posts/" . $id . "/save";
		$data = Array(
		'message' => $message,
		'_xfToken' => $this->token);
		$o = sendPost($url,$data);
		return $this->security($o);
	}
	
	function editThreadPost($id,$message) {
		$url = "http://shotbow.net/forum/posts/" . $id . "/save";
		$data = Array(
		'message' => $message,
		'_xfToken' => $this->token);
		$o = sendPost($url,$data);
		return $this->security($o)
	}
	
	function security($o) {
		if (strstr($o,"seconds before performing this action.") {
			restoreLatest();
			return false;
		}
		return true;
	}
	
	function getToken() {
		global $strSplitClient;
		$contents = getHTML("http://shotbow.net/forum/conversations/add");
		$startToken = explode($strSplitClient[0],$contents);
		$endToken = explode($strSplitClient[1],$startToken[1]);
		$token = $endToken[0];
		return $token;
	}
	
	function logout() {
		global $ckfile;
		unlink($ckfile);
	}
	
}

function sendPost($url,$data) { // Post as in POST Method
	
	global $ckfile, $ch;
	
	if (strstr($url,"login")) {
		$ch = curl_init ("http://shotbow.net/forum/portal");
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		$output = curl_exec ($ch);
		curl_close($ch);
		$ch = curl_init ($url);
	}
	
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_POST, 4);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($data)));
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");

	
	$output = curl_exec ($ch);
	return $output;
}

function getHTML($url) {
	global $ckfile, $ch;
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	
	$output = curl_exec ($ch);
	return $output;
}


?>