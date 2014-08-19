<?php
class Resize{
	public $width;
	public $height;
	public $oldWidth;
	public $oldHeight;
	public $newWidth = 400;
	public $newHeight = 350;
	public $oldProportion;
	public $newProportion;
	public $info;
	public $new;
	public $canvas;

	public function newSize($image, $route, $name, $type){
		$this->info = getimagesize($image);
		$this->oldWidth = $this->info[0];
		$this->oldHeight = $this->info[1];

		switch ($type){
			case 'image/jpeg': case 'image/jpg':
				$this->new = imagecreatefromjpeg($image);
				break;
			case 'image/png':
				$this->new = imagecreatefrompng($image);
				break;
			case 'image/gif':
				$this->new = imagecreatefromgif($image);
				break;
		}

		$this->oldProportion = $this->oldWidth / $this->oldHeight;
		$this->newProportion = $this->newWidth / $this->newHeight;

		if ($this->oldProportion > $this->newProportion){
			$this->width = $this->newWidth;
			$this->height = $this->newHeight / $this->oldProportion;
		}
		else if($this->oldProportion < $this->newProportion){
			$this->width = $this->newHeight * $this->oldProportion;
			$this->height = $this->newHeight / $this->oldProportion;	
		}
		else{
			$this->width = $this->newWidth;
			$this->height = $this->newHeight;
		}

		$this->canvas = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($this->canvas, $this->new, 0, 0, 0, 0, $this->width, $this->height, $this->oldWidth, $this->oldHeight);
		imagejpeg($this->canvas, $route . $name, 100);
		imagedestroy($this->canvas);
	}
}
?>
