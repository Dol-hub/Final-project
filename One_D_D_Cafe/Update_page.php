<?php  
include('header.php');
include('dbconn.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($connect, "SELECT * FROM menu WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
}

if (isset($_POST['update_menu']) && isset($_GET['id_new'])) {
    $idnew = $_GET['id_new'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $comment = $_POST['comment'];

    $new_image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    if ($new_image) {
        move_uploaded_file($image_tmp, "uploads/$new_image");
    } else {
        $new_image = $row['image'];
    }

    $query = "UPDATE `menu` SET 
              `name` = '$name', 
              `category` = '$category',
              `price` = '$price',
              `image` = '$new_image',
              `comment` = '$comment'
              WHERE `id` = $idnew";

    $result = mysqli_query($connect, $query);

    if ($result) {
        header('location:index.php?update_msg=อัปเดตเมนูเรียบร้อยแล้ว');
        exit();
    } else {
        die("Query Failed: " . mysqli_error($connect));
    }
}
?>

<form action="update_page.php?id_new=<?php echo $id ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>ชื่อเมนูอาหาร</label>
        <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>">
    </div>

    <div class="form-group">
        <label>หมวดหมู่</label>
        <select name="category" class="form-control" required>
            <?php
            $categories = ['กาแฟ', 'ชา', 'อิตาเลี่ยนโซดา', 'นม', 'เมนูปั่น', 'เมนูพิเศษ', 'เมนูปังเย็น'];
            foreach ($categories as $cat) {
                $selected = ($row['category'] == $cat) ? 'selected' : '';
                echo "<option value=\"$cat\" $selected>$cat</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>ราคา</label>
        <input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>">
    </div>

    <div class="form-group">
        <label>รูปภาพปัจจุบัน:</label><br>
        <?php if ($row['image']) { ?>
            <img src="uploads/<?php echo $row['image']; ?>" width="120"><br>
        <?php } ?>
        <label>อัปโหลดรูปใหม่:</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="form-group">
        <label>ลิงก์แบบฟอร์ม</label>
        <input type="text" name="comment" class="form-control" value="<?php echo $row['comment']; ?>">
    </div>

    <input type="submit" class="btn btn-success" name="update_menu" value="อัปเดตเมนู">
</form>

<?php include('footer.php'); ?>
