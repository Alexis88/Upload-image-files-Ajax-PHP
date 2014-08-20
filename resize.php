<?php
class Resize{
	public $width;
	public $height;
	public $oldWidth;
	public $oldHeight;
	public $newWidth = 500;
	public $newHeight = 400;
	public $oldProportion;
	public $newProportion;
	public $info;
	public $new;
	public $canvas;
	public $types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
	public $mimeType;
	public $name;
	public $size;

	public function newSize($image, $route, $name){
		$this->info = getimagesize($image);
		$this->oldWidth = $this->info[0];
		$this->oldHeight = $this->info[1];
		$this->mimeType = $this->info['mime'];

		switch ($this->mimeType){
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

	public function upload($resource, $route, $oldName, $error, $upload_max_filesize){
		$this->mimeType = getimagesize($resource)['mime'];
		$this->name = preg_replace('/^(\.|\s)|(\.|\s)$/', '', str_shuffle($oldName . rand(1, 999999))) . '.' . substr($this->mimeType, strpos($this->mimeType, '/') + 1);
		$this->size = filesize($resource) / 1048576;
		if ($error == UPLOAD_ERR_OK && in_array($this->mimeType, $this->types) && $this->size <= $upload_max_filesize){
			if (move_uploaded_file($resource, $route . $this->name)){
				return ['name' => $this->name];
			}
			return false;
		}
	}
}
?>
