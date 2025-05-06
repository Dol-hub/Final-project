<?php
include 'dbconn.php';

if (isset($_POST['add_menu'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $comment = $_POST['comment'];

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        move_uploaded_file($image_tmp, "uploads/" . $image);
    }

    $query = "INSERT INTO `menu` (`name`, `category`, `price`, `image`, `comment`) 
              VALUES ('$name', '$category', '$price', '$image', '$comment')";

    $result = mysqli_query($connect, $query);

    if ($result) {
        header('location:index.php?insert_msg=เพิ่มเมนูเรียบร้อยแล้ว');
        exit();
    } else {
        die("Query Failed: " . mysqli_error($connect));
    }
}
?>
