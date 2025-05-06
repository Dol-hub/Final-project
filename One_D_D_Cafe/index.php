<?php  
include('header.php');
include('dbconn.php');
session_start();

// ตรวจสอบหมวดหมู่ที่เลือก
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'ทั้งหมด';
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$categories = ['ทั้งหมด', 'กาแฟ', 'ชา', 'อิตาเลี่ยนโซดา', 'นม', 'เมนูปั่น', 'เมนูพิเศษ', 'เมนูปังเย็น'];

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!-- โลโก้และชื่อร้าน -->
<div class="text-center my-4">
    <img src="logo.jpg" alt="One D D Cafe Logo" width="200">
    <h1 id="main_title">☕ One D D Cafe 🍞</h1>
</div>

<div class="box1">
    <h2>เมนูทั้งหมด</h2>
    <!-- แสดงปุ่ม "เพิ่มเมนู" เฉพาะสำหรับ admin -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">เพิ่มเมนูอาหาร</button>
    <?php endif; ?>
</div>

<!-- ปุ่มกรองหมวดหมู่ -->
<div class="mb-4 text-center">
  <?php foreach ($categories as $cat): ?>
    <a href="index.php<?= $cat !== 'ทั้งหมด' ? '?category=' . urlencode($cat) : '' ?>" 
       class="btn btn<?= $selectedCategory === $cat ? ' btn-dark' : ' btn-outline-dark' ?> m-1">
       <?= $cat ?>
    </a>
  <?php endforeach; ?>
</div>

<!-- แบบฟอร์มค้นหา -->
<div class="mb-4 text-center">
  <form method="GET" action="index.php" class="d-inline-flex">
    <?php if ($selectedCategory !== 'ทั้งหมด'): ?>
      <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
    <?php endif; ?>
    <input type="text" name="search" class="form-control me-2" placeholder="ค้นหาเมนู..." value="<?= htmlspecialchars($searchKeyword) ?>">
    <button type="submit" class="btn btn-outline-success">ค้นหา</button>
  </form>
</div>

<!-- ปุ่มออกจากระบบ -->
<form action="logout.php" method="post" class="text-center mt-4">
    <button type="submit" class="btn btn-danger">ออกจากระบบ</button>
</form>

<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th>ชื่อเมนู</th>
            <th>หมวดหมู่</th>
            <th>ราคา (บาท)</th>
            <th>รูปภาพ</th>
            <th>ลิงก์แบบฟอร์ม</th>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <th>แก้ไข</th>
                <th>ลบ</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php  
        if ($selectedCategory !== 'ทั้งหมด' && $searchKeyword !== '') {
            $stmt = $connect->prepare("SELECT * FROM menu WHERE category = ? AND name LIKE ?");
            $likeKeyword = "%{$searchKeyword}%";
            $stmt->bind_param("ss", $selectedCategory, $likeKeyword);
            $stmt->execute();
            $result = $stmt->get_result();
        } elseif ($selectedCategory !== 'ทั้งหมด') {
            $stmt = $connect->prepare("SELECT * FROM menu WHERE category = ?");
            $stmt->bind_param("s", $selectedCategory);
            $stmt->execute();
            $result = $stmt->get_result();
        } elseif ($searchKeyword !== '') {
            $stmt = $connect->prepare("SELECT * FROM menu WHERE name LIKE ?");
            $likeKeyword = "%{$searchKeyword}%";
            $stmt->bind_param("s", $likeKeyword);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = mysqli_query($connect, "SELECT * FROM menu");
        }

        while($row = mysqli_fetch_assoc($result)){
    ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= $row['image'] ? "<img src='uploads/{$row['image']}' width='100'>" : "ไม่มีรูป" ?></td>
            <td><?= $row['comment'] ? "<a href='{$row['comment']}' target='_blank'>ลิงก์</a>" : "-" ?></td>
            <!-- แสดงปุ่ม "แก้ไข" และ "ลบ" เฉพาะสำหรับ admin -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <td><a href="update_page.php?id=<?= $row['id'] ?>" class="btn btn-success">แก้ไข</a></td>
                <td><a href="delete_page.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('แน่ใจว่าจะลบเมนูนี้?')">ลบ</a></td>
            <?php else: ?>
                <!-- ลูกค้าไม่สามารถแก้ไขหรือลบเมนูได้ -->
                <td>-</td>
                <td>-</td>
            <?php endif; ?>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php
if(isset($_GET['message'])) echo "<h6>{$_GET['message']}</h6>";
if(isset($_GET['insert_msg'])) echo "<h6>{$_GET['insert_msg']}</h6>";
?>

<!-- Modal เพิ่มเมนู -->
<div class="modal fade" id="exampleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="insert_data.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h1 class="modal-title fs-5">เพิ่มเมนูอาหาร</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
              <label>ชื่อเมนูอาหาร</label>
              <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
              <label>หมวดหมู่</label>
              <select name="category" class="form-control" required>
                  <?php foreach (array_slice($categories, 1) as $cat): ?>
                      <option value="<?= $cat ?>"><?= $cat ?></option>
                  <?php endforeach; ?>
              </select>
          </div>
          <div class="form-group">
              <label>ราคา</label>
              <input type="number" step="0.01" name="price" class="form-control" required>
          </div>
          <div class="form-group">
              <label>รูปภาพ</label>
              <input type="file" name="image" class="form-control">
          </div>
          <div class="form-group">
              <label>ลิงก์แบบฟอร์ม</label>
              <input type="text" name="comment" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">เพิ่มเมนู</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        </div>
      </form>
    </div>
  </div>
</div>
