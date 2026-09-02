<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

$news_id = $_GET['id'] ?? 'news-101';
$article = get_news_by_id($news_id);
$all_news = get_all_news();

include 'include/header.php';
?>

<div class="container my-4">
    <?php if (!$article): ?>
        <div class="alert alert-warning text-center my-5 rounded-4 p-5">
            <h4 class="fw-bold">Article Not Found</h4>
            <p class="text-muted">The requested news article is not available.</p>
            <a href="index.php" class="btn btn-primary rounded-pill px-4">Back to Home</a>
        </div>
    <?php else: ?>
        <!-- BREADCRUMB & BACK LINK -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none"><i class="bi bi-house-door me-1"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="index.php#latest-news" class="text-decoration-none">Latest News</a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 300px;"><?= htmlspecialchars($article['title']); ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- MAIN ARTICLE CONTENT -->
            <div class="col-lg-8">
                <article class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-4 p-md-5">
                    <!-- CATEGORY BADGE & META -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <span class="badge bg-<?= htmlspecialchars($article['category_color'] ?? 'primary'); ?> rounded-pill px-3 py-2 fs-6 fw-bold">
                            <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($article['category']); ?>
                        </span>
                        <div class="text-muted small">
                            <span class="me-3"><i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($article['formatted_date']); ?></span>
                            <span><i class="bi bi-clock me-1"></i> <?= htmlspecialchars($article['read_time']); ?></span>
                        </div>
                    </div>

                    <!-- ARTICLE TITLE -->
                    <h1 class="fw-extrabold display-6 text-dark mb-3" style="line-height: 1.3;">
                        <?= htmlspecialchars($article['title']); ?>
                    </h1>

                    <!-- AUTHOR & VIEWS -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <div class="logo-circle" style="width: 36px; height: 36px; font-size: 1rem;">✈</div>
                            <div>
                                <strong class="text-dark d-block"><?= htmlspecialchars($article['author']); ?></strong>
                                <span>Verified Aviation Newsdesk</span>
                            </div>
                        </div>
                        <span class="badge bg-white text-dark border px-3 py-2 rounded-pill">
                            <i class="bi bi-eye-fill me-1 text-primary"></i> <?= htmlspecialchars($article['views'] ?? '4,280 views'); ?>
                        </span>
                    </div>

                    <!-- ARTICLE HERO IMAGE -->
                    <div class="mb-4 rounded-4 overflow-hidden shadow-sm position-relative">
                        <img src="<?= htmlspecialchars($article['image']); ?>" alt="<?= htmlspecialchars($article['title']); ?>" class="w-100 object-fit-cover" style="max-height: 420px;">
                    </div>

                    <!-- ARTICLE SUMMARY LEAD -->
                    <div class="p-3 bg-primary-subtle text-primary border-start border-4 border-primary rounded-3 mb-4 lead fs-6 fw-semibold">
                        <?= htmlspecialchars($article['summary']); ?>
                    </div>

                    <!-- FULL BODY CONTENT -->
                    <div class="article-body lh-lg text-secondary" style="font-size: 1.05rem;">
                        <?= $article['full_content']; ?>
                    </div>

                    <!-- SHARE & TAGS -->
                    <div class="pt-4 mt-5 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark me-2 small">Share Article:</span>
                            <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="navigator.clipboard.writeText(window.location.href); alert('Article link copied to clipboard!');">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']); ?>&url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-outline-info btn-sm rounded-circle">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'] . ' ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-outline-success btn-sm rounded-circle">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>

                        <a href="index.php#latest-news" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Back to News Feed
                        </a>
                    </div>
                </article>
            </div>

            <!-- SIDEBAR: RELATED ARTICLES -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-newspaper me-2 text-primary"></i>More Present Aviation News
                    </h5>

                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($all_news as $item): ?>
                            <?php if ($item['id'] !== $article['id']): ?>
                                <a href="news.php?id=<?= urlencode($item['id']); ?>" class="text-decoration-none">
                                    <div class="card border-0 bg-light rounded-3 p-2 hover-shadow transition">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-4">
                                                <img src="<?= htmlspecialchars($item['image']); ?>" alt="News Thumbnail" class="img-fluid rounded-2 object-fit-cover" style="height: 65px; width: 100%;">
                                            </div>
                                            <div class="col-8">
                                                <span class="badge bg-<?= htmlspecialchars($item['category_color'] ?? 'primary'); ?>-subtle text-<?= htmlspecialchars($item['category_color'] ?? 'primary'); ?> rounded-pill mb-1" style="font-size: 0.65rem;">
                                                    <?= htmlspecialchars($item['category']); ?>
                                                </span>
                                                <h6 class="fw-bold text-dark mb-1 text-truncate-2 small" style="line-height: 1.2;">
                                                    <?= htmlspecialchars($item['title']); ?>
                                                </h6>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($item['formatted_date']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4">

                    <!-- QUICK FLIGHT SEARCH WIDGET -->
                    <div class="p-3 bg-primary text-white rounded-4 text-center">
                        <i class="bi bi-airplane-fill fs-2 mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1 text-white">Ready for your next trip?</h6>
                        <p class="small opacity-90 mb-3">Book flights with instant seating & boarding pass generation.</p>
                        <a href="index.php#search" class="btn btn-warning rounded-pill btn-sm px-4 fw-bold text-dark">
                            Book Flights Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.article-body p {
    margin-bottom: 1.25rem;
}
.article-body h5 {
    font-weight: 700;
    color: #1e293b;
    margin-top: 1.75rem;
    margin-bottom: 1rem;
}
.article-body ul {
    margin-bottom: 1.5rem;
    padding-left: 1.25rem;
}
.article-body li {
    margin-bottom: 0.5rem;
}
</style>

<?php include 'include/footer.php'; ?>
