<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Customer Reviews</h2>
        <p class="muted">Reply publicly to feedback and build stronger customer relationships.</p>
    </div>
    <img src="assets/images/hero-food.svg" alt="Customer Reviews illustration">
</section>
<?php if (isset($_GET['success'])): ?><div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<section class="panel">
    <h2>Customer Reviews & Public Replies</h2>
    <?php if (empty($reviews)): ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
    <?php foreach ($reviews as $review): ?>
        <article class="review-card">
            <h3><?= htmlspecialchars($review['customer_name']) ?> rated <?= (int)$review['rating'] ?>/5</h3>
            <p><?= htmlspecialchars($review['comment']) ?></p>
            <p><b>Current public reply:</b> <?= htmlspecialchars($review['manager_reply'] ?? 'No reply yet') ?></p>
            <form method="post" action="index.php?route=review_reply">
                <input type="hidden" name="review_id" value="<?= (int)$review['id'] ?>">
                <label>Reply<textarea name="manager_reply" placeholder="Write a public reply to this review"><?= htmlspecialchars($review['manager_reply'] ?? '') ?></textarea></label>
                <button type="submit">Save Reply</button>
            </form>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
