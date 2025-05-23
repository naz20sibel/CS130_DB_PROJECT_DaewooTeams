<?php
// DB connection
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS";
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

$action = $_GET['action'] ?? '';
$notice_id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message_text = $_POST['message'];
    $posted_by = $_POST['posted_by'];

    try {
        if ($action === 'create') {
            $query = "INSERT INTO NOTICEBOARD (title, message, posted_by) VALUES (?, ?, ?)";
            $params = [$title, $message_text, $posted_by];
        } elseif ($action === 'update') {
            $query = "UPDATE NOTICEBOARD SET title = ?, message = ?, posted_by = ? WHERE notice_id = ?";
            $params = [$title, $message_text, $posted_by, $notice_id];
        }

        $stmt = sqlsrv_prepare($conn, $query, $params);
        if (sqlsrv_execute($stmt)) {
            header("Location: noticeboard.php?success=Notice " . ($action === 'create' ? 'added' : 'updated') . " successfully");
            exit;
        }
    } catch (Exception $e) {
        header("Location: noticeboard.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

if ($action === 'delete') {
    try {
        $query = "DELETE FROM NOTICEBOARD WHERE notice_id = ?";
        $stmt = sqlsrv_prepare($conn, $query, [$notice_id]);
        if (sqlsrv_execute($stmt)) {
            header("Location: noticeboard.php?success=Notice deleted successfully");
            exit;
        }
    } catch (Exception $e) {
        header("Location: noticeboard.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

// Admin list for dropdown
$admin_query = "SELECT admin_id as admin_id, full_name as full_name FROM ADMINS ORDER BY full_name";
$admin_stmt = sqlsrv_query($conn, $admin_query);
if ($admin_stmt === false) {
    die("Admin query failed: " . print_r(sqlsrv_errors(), true));
}
$admins = [];
while ($row = sqlsrv_fetch_array($admin_stmt, SQLSRV_FETCH_ASSOC)) {
    $admins[] = $row;
}

// For edit form
$notice = null;
if ($action === 'edit') {
    $query = "SELECT n.*, a.full_name as posted_by_name 
              FROM NOTICEBOARD n 
              JOIN ADMINS a ON n.posted_by = a.admin_id 
              WHERE n.notice_id = ?";
    $stmt = sqlsrv_prepare($conn, $query, [$notice_id]);
    if (sqlsrv_execute($stmt)) {
        $notice = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

// All notices
$query = "SELECT n.*, a.full_name as posted_by_name 
          FROM NOTICEBOARD n 
          JOIN ADMINS a ON n.posted_by = a.admin_id 
          ORDER BY n.notice_id DESC";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die("Notices query failed: " . print_r(sqlsrv_errors(), true));
}
$all_notices = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $all_notices[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice Board Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .message-cell:hover {
            white-space: normal;
            overflow: visible;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notice Board Management</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addNoticeForm">
                        <?= $action === 'edit' ? 'Edit Notice' : 'Add New Notice' ?>
                    </button>
                </div>

                <!-- Add/Edit Form -->
                <div class="collapse <?= $action === 'edit' ? 'show' : '' ?>" id="addNoticeForm">
                    <div class="card-body border-bottom">
                        <form method="POST" action="noticeboard.php?action=<?= $action === 'edit' ? 'update&id=' . $notice_id : 'create' ?>">
                            <div class="mb-3">
                                <label class="form-label">Title*</label>
                                <input type="text" name="title" class="form-control" maxlength="100"
                                       value="<?= htmlspecialchars($notice['title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message*</label>
                                <textarea name="message" class="form-control" rows="4" maxlength="500" required><?= htmlspecialchars($notice['message'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Posted By*</label>
                                <select name="posted_by" class="form-select" required>
                                    <?php foreach ($admins as $admin): ?>
                                        <option value="<?= $admin['admin_id'] ?>" 
                                            <?= ($notice['posted_by'] ?? '') == $admin['admin_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($admin['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#addNoticeForm">Cancel</button>
                                <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Update' : 'Add' ?> Notice</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Feedback -->
                <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>

                    <!-- Notices Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Message Preview</th>
                                <th>Posted By</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($all_notices as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['notice_id']) ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td class="message-cell" title="<?= htmlspecialchars($row['message']) ?>"><?= htmlspecialchars($row['message']) ?></td>
                                    <td><?= htmlspecialchars($row['posted_by_name']) ?></td>
                                    <td>
                                        <a href="noticeboard.php?action=edit&id=<?= $row['notice_id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="noticeboard.php?action=delete&id=<?= $row['notice_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notice?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>