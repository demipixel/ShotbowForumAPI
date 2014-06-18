<?



class Queue {

	private $client;
	
	function Queue($client,$file) {
		$this->client = $client;
		if ($client->file != null) throw new Exception('You cannot set two queues to one client.');
		$client->file = $file;
	}
	
	function toQueue($data) {
		$f = fopen($this->client->file,'a');
		fwrite($f,serialize($data) . "\n");
		fclose($f);
	}
	
	function postProfile($id,$message) {
		$this->toQueue(Array(0,$id,$message));
	}
	
	function postThread($id,$message) {
		$this->toQueue(Array(1,$id,$message));
	}
	
	function newThread($title,$message,$section) {
		$this-toQueue(Array(2,$title,$message,$section));
	}
	
	function newConvo($title,$userArray,$message) {
		$this->toQueue(Array(3,$title,$userArray,$message));
	}
	
	function postConvo($id,$message) {
		$this->toQueue(Array(7,$id,$message));
	}
	
	function editProfilePost($id,$message) {
		$this->toQueue(Array(4,$id,$message));
	}
	
	function editThreadPost($id,$message) {
		$this->toQueue(Array(5,$id,$message));
	}
	
	function deleteThreadPost($postId) {
		$this->toQueue(6,$postId);
	}
	
}


?>