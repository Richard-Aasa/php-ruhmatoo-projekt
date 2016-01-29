<?php
  require_once 'includes/config.php';

  if ($_SESSION["is_admin"] != 1) {
    header("Location: index.php");
  }

  $e_title=$e_desc=$e_category=$e_content=$e_file="";
  if(isset($_POST["submit"])) {

    

    if(!isset($_POST["title"])) {
      $e_title = "Missing title!";
    }
    if(!isset($_POST["category"])) {
      $e_desc = "Missing category!";
    }
    if(!isset($_POST["desc"])) {
      $e_category = "Missing description!";
    }
    if(!isset($_POST["body"])) {
      $e_content = "Missing body!";
    }
  }
  $target_dir = "images/";
  $target_file = $target_dir . basename($_FILES["file"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

  // Check if image file is a actual image or fake image
  $check = getimagesize($_FILES["file"]["tmp_name"]);
  if($check !== false) {
      $uploadOk = 1;
  } else {
      $e_file = "File is not an image.";
      $uploadOk = 0;
  }

  // Check if file already exists
  if (file_exists($target_file)) {
      $e_file = "Sorry, file already exists.";
      $uploadOk = 0;
  }
  // Check file size
  if ($_FILES["file"]["size"] > 500000) {
      $e_file = "Sorry, your file is too large.";
      $uploadOk = 0;
  }
  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif" ) {
      $e_file = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      $uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
      $e_file = "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
  } else {
      if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
          echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
      } else {
          $e_file = "Sorry, there was an error uploading your file.";
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=no">
<title>Admin</title>
<link rel="stylesheet" href="css/md-css.min.css">
<link rel="stylesheet" href="css/md-icons.min.css">
<link rel="stylesheet" href="css/main.min.css">
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea' });</script>
</head>
<body material centered fluid>
  <div toolbar>
    Welcome <?= $_SESSION["user_email"]?>
    <a fg-black href="logout.php">Logout</a>
  </div>
  <div content>
    <!-- Postituse loomine-->
    <div card z-1 style="width:40%">
      <form  role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
        <span>Title</span><input type="text" name="title">
        <select name="category">
          <?php
            try {
              $result = $db->query("SELECT * FROM blog_tag_color");
              $html = " ";
              foreach ($result as $row) {
                $html .= '<option value="'.$row["postTag"];
                $html .= '">'.$row["postTag"].'</option>';
              }
              echo $html;
            } catch (PDOexception $e) {
              echo "Error is: " . $e-> etmessage();
            }
          ?>
        </select>
        <input type="file" name="file" accept="image/*">
        <h6>Description</h6>
        <textarea name="desc"></textarea>
        <h6>Content</h6>
        <textarea name="body"></textarea>
        <div align-right>
          <button type="submit" name="create" bg-blue-grey400 ripple-color="tealA400">Post</button>
        </div>
      </form>
    </div>

    <!-- Postituse muutmine/kustutamine -->
    <div card z-1 style="width:50%">
      <table>
        <caption>Edit/Delete post:</caption>
        <?php
          try {
            $result = $db->query("SELECT * FROM blog_posts
              LEFT JOIN blog_tag_color
              ON blog_posts.postTag=blog_tag_color.postTag");
            $html = " ";
            foreach ($result as $row) {
              $html .= '<tr '.$row["color"].'>';
                $html .= '<td style="padding: 2px">'.date('F/j/Y',strtotime($row["postDate"])).'</td>';
                $html .= '<td style="padding: 2px">'.$row["postTag"].'</td>';
                $html .= '<td style="padding: 2px">'.$row["postTitle"].'</td>';
                $html .= '<td style="padding: 2px"><a button fg-black href="post_delete.php?id='.$row["postID"].'">Delete</a>';
                $html .= '<a button fg-black href="post_edit.php?id='.$row["postID"].'">Edit</a></td>';
              $html .= '</tr>';
            }
            echo $html;
          } catch (PDOexception $e) {
            echo "Error is: " . $e-> etmessage();
          }
        ?>
      </table>
    </div>
  </div>
</body>
</html>
