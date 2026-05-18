<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Restaurant Complaints</h2>
        <p class="muted">View admin complaints connected to this restaurant in one place.</p>
    </div>
    <img src="assets/images/dashboard-preview.jpg" alt="Restaurant Complaints illustration">
</section>
<section class="panel">
    <h2>Restaurant Complaints Submitted to Admin</h2>
    <p>Complaints directly linked to this restaurant, plus complaints from customers who ordered from this restaurant.</p>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Submitter</th><th>Subject</th><th>Description</th><th>Status</th><th>Admin Note</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?= htmlspecialchars($complaint['submitter_name']) ?></td>
                        <td><?= htmlspecialchars($complaint['subject']) ?></td>
                        <td><?= htmlspecialchars($complaint['description']) ?></td>
                        <td><span class="badge <?= $complaint['status'] === 'resolved' ? 'open' : 'closed' ?>"><?= htmlspecialchars($complaint['status']) ?></span></td>
                        <td><?= htmlspecialchars($complaint['admin_note'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($complaint['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
