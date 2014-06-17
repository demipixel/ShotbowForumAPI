<?

class Tracker {
	
	private $file = "";
	
	function Tracker($file) {
		$this->file = $file;
		if (!file_exists($file)) $this->saveDat(0);
	}
	
	function saveDat($time) {
		file_put_contents($this->file,$time);
	}
	
	function getDat() {
		return file_get_contents($this->file);
	}
	
	function getLatestThread($threadArray) { // From a thread array
		$time = 0;
		foreach ($threadArray as $thread) {
			if ($thread->getLatestTime() > $time) $time = $thread->time;
		}
		return $time;
	}
	
	function saveLatestThread($threadArray) { // Usually you want to use this since it's a shortcut
		$this->saveDat($this->getLatest($threadArray));
	}
	
	// No getLatestForum() since that already exists in the forum object
	function saveLatestForum($forum) { // From a forum
		$this->saveDat($forum->getLatest());
	}
	
	
}


?>