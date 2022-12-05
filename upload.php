<?php
$infoMessage = "";
// id=1. if the form submited
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    // id=2. if else. if the file input is not empty
    if (!empty($_FILES['image']['name']))
    {

        $file_type = $_FILES['image']['type'];
        $allowed = array(
            "image/jpg",
            "image/png",
            "image/jpeg",
            "image/gif"
        );
        // id=3. if file extenstion is allowed
        if (in_array($file_type, $allowed))
        {

            $thumb_width = '100';
            $thumb_height = '100';
            $original_width = '600';
            $file_name = trim($_POST["image_name"]);
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = $file_name . '.' . $file_ext;

            // id=4. if else. if the not exist
			// if one of two TRUE continue 
            //if (!file_exists("uploads/thumbs/" . $fileName) || !file_exists("uploads/original/" . $fileName)){
			
			// if both are FALSE 
			if (!file_exists("uploads/thumbs/" . $fileName) && !file_exists("uploads/original/" . $fileName)){

                //upload image path
                $upload_image = 'uploads/' . basename($fileName);

                //upload image
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_image))
                {
                    $thumbnail = 'uploads/thumbs/' . $fileName;
                    $original = 'uploads/original/' . $fileName;
                    list($width, $height) = getimagesize($upload_image);

                    // this function preserves the height ratio of the image 10 is 10% of 100.
                    $percent = $height / $width;
                    $percent = number_format($percent * 100);
                    $original_height = ($percent / 100) * $original_width;

                    $thumb_create = imagecreatetruecolor($thumb_width, $thumb_height);
                    $original_create = imagecreatetruecolor($original_width, $original_height);

                    switch ($file_ext)
                    {
                        case 'jpg':
                            $source = imagecreatefromjpeg($upload_image);
                        break;
                        case 'jpeg':
                            $source = imagecreatefromjpeg($upload_image);
                        break;

                        case 'png':
                            $source = imagecreatefrompng($upload_image);
                        break;
                        case 'gif':
                            $source = imagecreatefromgif($upload_image);
                        break;
                        default:
                            $source = imagecreatefromjpeg($upload_image);
                    }

                    imagecopyresized($thumb_create, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
                    imagecopyresized($original_create, $source, 0, 0, 0, 0, $original_width, $original_height, $width, $height);

                    switch ($file_ext)
                    {
                        case 'jpg' || 'jpeg':
                            imagejpeg($thumb_create, $thumbnail);
                            imagejpeg($original_create, $original);
                        break;
                        case 'png':
                            imagepng($thumb_create, $thumbnail);
                            imagepng($original_create, $original);
                        break;

                        case 'gif':
                            imagegif($thumb_create, $thumbnail);
                            imagegif($original_create, $original);
                        break;
                        default:
                            imagejpeg($thumb_create, $thumbnail);
                            imagejpeg($original_create, $original);
                    }

                }
                else
                {
                    return false;
                }
                // if the images loaded remove temporary storage
                unlink($upload_image);
                // reset the form if the the submit is successful
                echo "<script>if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}</script>";

                // refresh the page to display images
                echo "<script>location.reload(); return false;</script>";

            }
            else
            { // id=4. if else. else if the exist
                $infoMessage = '<p style="color:red;">The file exist!</p>';
            }

        }
        else
        { // id=3. if file extenstion is not allowed
            $infoMessage = '<p style="color:red;">Only jpg, png, gif, files are allowed.</p>';
        }

    }
    else
    { // id=2. if else. if the file input is not empty end
        $infoMessage = '<p style="color:red;">Select an image please!</p>';

    } // id=2. if else. if the file input is not empty end
    
} // id=1. if the form submited ends

?>
<p><?php echo $infoMessage; ?></p>

<form id="myform" method="post" enctype="multipart/form-data" >
<p>Select an Image: <br> <input type="file" name="image" required></p>
<p>Image name: <br><input type="text" name="image_name" required></p>
<p><input type="submit" name="submit" value="Upload the image"></p>
</form>


 
 <div style='border:1px solid silver; margin:1px; padding:20px; overload:hidden;'> 
<h2>Thumbnails</h2>
<hr/>
<?php
function thumb_nails(){
$dir = "./uploads/thumbs/";
if (is_dir($dir))
{
    $files = scandir($dir);
    unset($files[array_search('.', $files) ]);
    unset($files[array_search('..', $files) ]);
    foreach ($files as $key => $val)
    {

        echo "<img style='padding:5px;' src=\"" . rtrim($dir, '\/') . "/" . $val . "\" />";
    }
}
else
{
    echo "(" . $dir . ") does not exist or is not a valid directory";
}
}
thumb_nails();
?>
</div>


<div style='border:1px solid silver; margin:1px; padding:20px; overload:hidden;'>
<h2>Original size and with details</h2>
 
<?php
function original_size(){
   $dir="./uploads/original/";
   if(is_dir($dir)){
   $files = scandir($dir);
   unset($files[array_search('.',$files)]);
   unset($files[array_search('..',$files)]);
    
   foreach($files as $key=>$val){
      echo "<hr/>";
      echo "Name: ".preg_replace('/\\.[^.\\s]{3,4}$/', '', $val)."<br/>";	  
	  $image_type = pathinfo($val, PATHINFO_EXTENSION);
      echo "Type: ". $image_type."<br/>";	  
      $img_size_array = getimagesize(rtrim($dir,'\/')."/".$val);
      echo "byte: ". filesize(rtrim($dir,'\/')."/".$val). "<br>";
      echo "width: ".$img_size_array[0]."px<br>";
      echo "height: ".$img_size_array[1]."px<br>";  
      echo "<br/>";
	  echo "<img src=\"".rtrim($dir,'\/')."/".$val."\"/><br/>";    
      
   }
   }else{
      echo "(".$dir.") does not exist or is not a valid directory";
   }
}
original_size();
?>
</div>