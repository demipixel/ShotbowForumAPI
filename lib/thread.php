<?

$threadURL = "http://shotbow.net/forum/threads/";

// There MUST be a better way to do this. I might re-write it later...
$strSplitPost = Array();
for ($i=0;$i<5;$i++) { // 9 for all variables of threadView
	$strSplitPost[$i] = Array();
}
/*
MAIN:
0 = Overall, 1 = date, 2 = author, 
3 = authorId, 4 = message

SECONDARY: 0 = Before, 1 = After

*/

$strSplitPost[0][0] = "<li id=\"post-";
$strSplitPost[0][1] = "</li>";
$strSplitPost[1][0] = "data-time=\"";
$strSplitPost[1][1] = "\"";
$strSplitPost[2][0] = "data-author=\"";
$strSplitPost[2][1] = "\"";
$strSplitPost[3][0] = "members/";
$strSplitPost[3][1] = "/";
$strSplitPost[4][0] = "<blockquote class=\"messageText ugc baseHtml\">";
$strSplitPost[4][1] = "</article>";

class Thread {
	
	
	public $posts = Array();
	public $id = 0;
	public $url = "";
	private $scanned = false;
	
	function Thread($id) {
		global $threadURL;
		$this->id = $id;
		$this->url = $threadURL . $id . "/";
	}
	
	function scan() {
		if ($scanned == true) return;
		$scanned = true;
		
		$pageNum = 0;
		do {
			$tmpPage = new Page($this->url,$pageNum);
			$tmpPage->scan();
			$this->posts = array_merge($this->posts,$tmpPage->getAllPosts());
			
			$pageNum++;
		} while ($tmpPage->hasNext()); 
		
	}
	
	function getAllPosts() {
		$this->scan();
		return $this->posts;
	}
	
	function getPost($i) {
		$this->scan();
		if ($this->posts[$i]) return $this->posts[$i];
		else return null;
	}
	
}

class Page {
	
	private $nextButton = false;
	private $url = "";
	private $scanned = false;
	private $posts = Array();
	
	function Page($url,$page) {
		$this->url = $url . "page-" . ($page+1);
	}
	
	function scan() {
		
		global $strSplitPost;
	
		if ($scanned == true) return;
		$scanned = true;
		$contents = file_get_contents($this->url);
		
		$postStart = explode($strSplitPost[0][0], $contents); // Gets array, split at start of post HTML
		$allPosts = Array();
		array_shift($postStart); // Removes preceding HTML, nothing to do with posts
		
		foreach ($postStart as $index => $postHTML) {
			$postEnd = explode($strSplitPost[0][1], $postStart[$index]); // Scrape off ending
			array_push($allPosts,$postEnd[0]); // Add to $allPosts only the part we didn't scrape
		}
		
		foreach ($allPosts as $ind => $postHTML) {
			$cont = Array();
			
			foreach ($strSplitPost as $index => $strType) {
				if ($index == 0) continue; // Don't need "Overall"
				$strStart = explode($strType[0], $postHTML); // Get everything after string, there should only be one anyway
				if ($strStart[1] == null) { array_push($cont,null); continue; } // Was used for something else, decided to keep to prevent errors
				$strEnd = explode($strType[1], $strStart[1]); // Scrape off ending
				$strTotal = $strEnd[0]; // Finish product
				$cont[$index] =  $strTotal;
			}
			
			trim($cont[4]);
			$cont[4] = substr($cont[4], 0, -13); // 13 is the length of string "blockquote"
			$cont[4] = str_replace("<br />","<br>",$cont[4]);
			// </blockquote> can't be used since it occurs in quotes...
			
			$postObj = new Post($cont[1],$cont[2],$cont[3],$cont[4]);
			array_push($this->posts,$postObj);
		}
		
		if (strstr($contents,"Next &gt")) $this->nextButton = true; // &gt is the > symbol
	}
	
	function getAllPosts() {
		return $this->posts;
	}
	
	function hasNext() {
		return $this->nextButton;
	}
	
}

class Post {
	
	public $date = 0;
	public $author = "";
	public $authorId = 0;
	public $message = "";
	
	function Post($d,$a,$aI,$m) {
		$this->date = $d;
		$this->author = $a;
		$this->authorId = $aI;
		$this->message = $m;
	}
	
}


?>