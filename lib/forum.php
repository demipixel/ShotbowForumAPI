<?

// There MUST be a better way to do this. I might re-write it later...
$strSplitForum = Array();
for ($i=0;$i<10;$i++) { // 9 for all variables of threadView
	$strSplitForum[$i] = Array();
}
/*
MAIN:
0 = dateDiff, 1 = author, 2 = title, 3 = replies,
4 = views, 5 = replyAuthor, 6 = replyDate,
7 = stickied, 8 = locked, 9 = ID, 
10 = Author ID, 11 = Reply Author ID, 
12 = Type, 13 = Overall thread

SECONDARY: 0 = Before, 1 = After

*/

$strSplitForum[0][0] = "data-diff=\""; // Data-Diff since there is no display for "data-time"
$strSplitForum[0][1] = "\""; 
$strSplitForum[1][0] = "title=\"Thread starter\">";
$strSplitForum[1][1] = "<";
$strSplitForum[2][0] = "/preview\">";
$strSplitForum[2][1] = "<";
$strSplitForum[3][0] = "Replies:</dt> <dd>";
$strSplitForum[3][1] = "<";
$strSplitForum[4][0] = "Views:</dt> <dd>";
$strSplitForum[4][1] = "<";
$strSplitForum[5][0] = "class=\"username\">";
$strSplitForum[5][1] = "<";
$strSplitForum[6][0] = "data-time=\"";
$strSplitForum[6][1] = "\"";
$strSplitForum[7][0] = ""; // Not used since determined by strstr()
$strSplitForum[7][1] = ""; //
$strSplitForum[8][0] = ""; //
$strSplitForum[8][1] = ""; //
$strSplitForum[9][0] = "thread-";
$strSplitForum[9][1] = "\"";
$strSplitForum[10][0] = "members/"; // Warning, based on function taking first choice of options. Others of this string appear
$strSplitForum[10][1] = "/";
$strSplitForum[11][0] = "<dt><a href=\"";
$strSplitForum[11][1] = "/";
$strSplitForum[12][0] = "Show only threads prefixed by '";
$strSplitForum[12][1] = "'";
$strSplitForum[13][0] = "<li id=\"";
$strSplitForum[13][1] = "</li>";

class Forum {
	
	private $url = ""; // Stores page it's getting data from. Use a new forum object for new pages
	private $threadList = Array(); // Array of threadView objects
	private $scanned = false;
	
	function forum($url) {
		$this->url = $url;
	}
	
	function scan() { // Scans page for threads, using getAllThreads() will do this automatically
		global $strSplitForum;
    	if ($this->scanned == true) return;
		$this->scanned = true;
		$contents = file_get_contents($this->url);
		$threadStart = explode($strSplitForum[13][0], $contents); // Gets array, split at start of thread HTML
		$allThreads = Array();
		array_shift($threadStart); // Removes preceding HTML, nothing to do with threads
		array_pop($threadStart); // Removes last one
		foreach ($threadStart as $index => $threadHTML) {
			$threadEnd = explode($strSplitForum[13][1], $threadStart[$index]); // Scrape off ending
			array_push($allThreads,$threadEnd[0]); // Add to $allThreads only the part we didn't scrape
		}
		
		foreach ($allThreads as $ind => $threadHTML) {
			$cont = Array();
			if (strstr($threadHTML,"Redirect")) continue;
			
			foreach ($strSplitForum as $index => $strType) {
				if ($index == 13 || $index == 7 || $index == 8) continue; // Don't include "Overall"
				$strStart = explode($strType[0], $threadHTML); // Get everything after string, there should only be one anyway
				if ($strStart[1] == null) { array_push($cont,null); continue; } // Was used for something else, decided to keep to prevent errors
				$strEnd = explode($strType[1], $strStart[1]); // Scrape off ending
				$strTotal = $strEnd[0]; // Finish product
				$cont[$index] =  $strTotal;
			}
			
			if (strstr($threadHTML,"Sticky")) $cont[7] = true;
			else $cont[7] = false;
			if (strstr($threadHTML,"Locked")) $cont[8] = true;
			else $cont[8] = false;
			
			$threadObj = new ThreadView($cont[0],$cont[1],$cont[2],$cont[3],$cont[4],$cont[5],$cont[6]
			,$cont[7],$cont[8],$cont[9],$cont[10],$cont[11],$cont[12]);
			
			array_push($this->threadList, $threadObj);
		}
	}
	
	function getAllThreads() { // Returns all threads. WILL RETURN FALSE IF NO THREADS
		$this->scan(); // Scan page if not already
		if ($this->threadList != null) return $this->threadList;
		else return false;
	}
	
	function getThread($i) { // Returns thread at index. WILL RETURN FALSE IF NO SUCH INDEX
		$this->scan(); // Scan page if not already. You really shouldn't get an index without scanning the page first anyway...
		if ($threadList[$i]) return $threadList[$i];
		else return null;
	}
	

}

class ThreadView { // Not named "thread" as it many be confused with an actual thread content
	
	public $date;
	public $author;
	public $title;
	public $replies;
	public $views;
	public $replyAuthor; // Simply the last post author and date as shown on the form
	public $replyDate;
	public $authorId = 0;
	public $replyAuthorId = 0;
	public $stickied = false;
	public $locked = false;
	public $id = 0;
	public $type; // CAN BE NULL if no type set
	
	
	function threadView($dateDiff,$author,$title,$replies,$views,$replyAuthor,$replyDate,$stickied,$locked,$id,$authorId,$replyAuthorId,$type) {
		$this->date = (int)$replyDate - (int)$dateDiff;
		$this->author = $author;
		$this->title = $title;
		$this->replies = (int)str_replace(",","",$replies);
		$this->views = (int)str_replace(",","",$views);
		$this->replyAuthor = $replyAuthor;
		$this->replyDate = (int)$replyDate;
		$this->stickied = $stickied;
		$this->locked = $locked;
		$this->id = $id;
		$this->authorId = $authorId;
		$this->replyAuthorId = $replyAuthorId;
		$this->type = $type;
	}
}

?>