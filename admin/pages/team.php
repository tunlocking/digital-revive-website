<?php
session_start();
require_once '../../config/db.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$page = intval($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total count
$count_stmt = $conn->query("SELECT COUNT(*) as total FROM team_members");
$total = $count_stmt->fetch()['total'];
$total_pages = ceil($total / $limit);

// Get team members with pagination
$stmt = $conn->prepare("
    SELECT * FROM team_members
    ORDER BY position_order, name
    LIMIT ? OFFSET ?
");
$stmt->execute([$limit, $offset]);
$team = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        // Delete image if exists
        $stmt = $conn->prepare("SELECT image_path FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch();
        if ($member && $member['image_path'] && file_exists('../../uploads/' . $member['image_path'])) {
            unlink('../../uploads/' . $member['image_path']);
        }
        
        $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: team.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - Digital Revive Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include '../../admin/includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../admin/includes/sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <h2><i class="fas fa-users"></i> Manage Team Members</h2>
                    <a href="add_team_member.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Team Member
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Operation completed successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($team)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-user-slash"></i> No team members found
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($team as $member): ?>
                                        <tr>
                                            <td>
                                                <?php if ($member['image_path']): ?>
                                                    <img src="../../uploads/<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" style="height: 40px; width: 40px; object-fit: cover; border-radius: 50%;">
                                                <?php else: ?>
                                                    <i class="fas fa-user-circle" style="font-size: 40px; color: #ccc;"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($member['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($member['position']); ?></td>
                                            <td><small><?php echo htmlspecialchars($member['email'] ?? 'N/A'); ?></small></td>
                                            <td>
                                                <span class="badge bg-<?php echo $member['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($member['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_team_member.php?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteId(<?php echo $member['id']; ?>)" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1">First</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?>">Last</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this team member?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function setDeleteId(id) {
            document.getElementById('deleteId').value = id;
        }
    </script>
</body>
</html>
