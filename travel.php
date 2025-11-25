<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; 
$db_name = 'travel_db';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die('DB connection error: ' . mysqli_connect_error());
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Mongolia → Korea Travel Guide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8fafc; }
    .hero {
      background-image: linear-gradient(0deg, rgba(0,0,0,0.45), rgba(0,0,0,0.25)), url('https://images.unsplash.com/photo-1549693578-d683be217e58?w=1600&q=80&auto=format&fit=crop');
      background-size: cover;
      background-position: center;
      color: white;
      padding: 80px 0;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    .hero h1 { font-weight:700; letter-spacing: -0.02em; }
    .card-img-top { height: 200px; object-fit: cover; }
    .badge-pill { border-radius: 999px; }
    footer { padding: 30px 0; margin-top: 40px; color:#6b7280; }
    .search-input { max-width: 520px; margin: 0 auto; }
    @media (max-width:576px) {
      .hero { padding: 40px 15px; }
      .card-img-top { height: 160px; }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="?page=home">MN → KR Travel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="?page=home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="?page=places">Places</a></li>
        <li class="nav-item"><a class="nav-link" href="?page=plan">Plan & Tips</a></li>
        <li class="nav-item"><a class="nav-link" href="?page=contact">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <?php if ($page === 'home'): ?>
    <div class="hero rounded">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-7">
            <h1 class="display-5">Plan your trip from Mongolia to South Korea</h1>
            <p class="lead mt-3">Simple guide for students: visa basics, best seasons, transport tips and must-see places in Korea.</p>
            <p class="mt-4">
              <a href="?page=places" class="btn btn-primary btn-lg me-2">Explore Places</a>
              <a href="?page=plan" class="btn btn-outline-light btn-lg">Travel Tips</a>
            </p>
          </div>
          <div class="col-md-5 text-center d-none d-md-block">
            <img src="https://images.unsplash.com/photo-1505765051227-89b0f8c4f3b9?w=800&q=80&auto=format&fit=crop" alt="plane" class="img-fluid rounded shadow-sm" style="max-height:250px; object-fit:cover;">
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-12">
            <form class="d-flex justify-content-center" method="get" action="travel.php">
              <input type="hidden" name="page" value="places">
              <input name="q" class="form-control search-input me-2" placeholder="Search places (e.g. Seoul, Nami)..." value="<?php echo isset($_GET['q'])? esc($_GET['q']) : ''; ?>">
              <button class="btn btn-light">Search</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  <?php elseif ($page === 'places'): ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0">Featured Places</h2>
      <div class="small text-muted">Click "Details" to open a preview</div>
    </div>

    <div class="row">
      <?php
        $q = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
        if ($q !== '') {
          $sql = "SELECT * FROM places WHERE name LIKE '%$q%' OR location LIKE '%$q%' ORDER BY id ASC";
        } else {
          $sql = "SELECT * FROM places ORDER BY id ASC";
        }
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0):
          while ($row = mysqli_fetch_assoc($res)):
            $data_name = esc($row['name']);
            $data_location = esc($row['location']);
            $data_desc = esc($row['description']);
            $data_image = esc($row['image'] ?: 'https://via.placeholder.com/800x500?text=No+Image');
      ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="<?php echo $data_image; ?>" class="card-img-top" alt="<?php echo $data_name; ?>">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo $data_name; ?></h5>
              <p class="card-text text-muted mb-2"><?php echo $data_location; ?></p>
              <p class="card-text small text-truncate"><?php echo esc(mb_strimwidth($row['description'], 0, 140, '...')); ?></p>
              <div class="mt-auto d-flex gap-2">
                <button
                  class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#placeModal"
                  data-name="<?php echo $data_name; ?>"
                  data-location="<?php echo $data_location; ?>"
                  data-desc="<?php echo $data_desc; ?>"
                  data-image="<?php echo $data_image; ?>"
                >Details</button>
                <a class="btn btn-sm btn-primary" href="?page=places&show=<?php echo (int)$row['id']; ?>">Open Page</a>
              </div>
            </div>
          </div>
        </div>
      <?php
          endwhile;
        else:
      ?>
        <div class="col-12">
          <div class="alert alert-warning">No places found. Try a different keyword or check your database.</div>
        </div>
      <?php endif; ?>
    </div>

    <?php
      if (isset($_GET['show'])):
        $id = (int)$_GET['show'];
        $s = "SELECT * FROM places WHERE id = $id LIMIT 1";
        $r = mysqli_query($conn, $s);
        if ($r && mysqli_num_rows($r) === 1):
          $p = mysqli_fetch_assoc($r);
    ?>
      <div class="card mt-4 shadow-sm">
        <div class="row g-0">
          <div class="col-md-5">
            <img src="<?php echo esc($p['image'] ?: 'https://via.placeholder.com/800x500?text=No+Image'); ?>" class="img-fluid rounded-start" style="height:100%; object-fit:cover;">
          </div>
          <div class="col-md-7">
            <div class="card-body">
              <h3><?php echo esc($p['name']); ?></h3>
              <p class="text-muted"><?php echo esc($p['location']); ?></p>
              <p><?php echo nl2br(esc($p['description'])); ?></p>
              <a class="btn btn-primary" href="?page=places">Back to list</a>
            </div>
          </div>
        </div>
      </div>
    <?php
        endif;
      endif;
    ?>

  <?php elseif ($page === 'plan'): ?>
    <h2>Travel Plan & Tips</h2>
    <div class="row">
      <div class="col-md-8">
        <article class="mb-4">
          <h4>Before you go</h4>
          <ul>
            <li>Check passport validity (at least 6 months recommended).</li>
            <li>Apply for K-ETA or visa if required. Keep your flight and hotel confirmations.</li>
            <li>Currency: South Korean Won (KRW). Use card where possible, carry small cash.</li>
          </ul>
        </article>
        <article class="mb-4">
          <h4>Transport & Communication</h4>
          <p>Subway system in major cities is excellent. Buy T-money card for convenience. Most restaurants support English; have Google Translate ready for menus.</p>
        </article>
      </div>
      <div class="col-md-4">
        <div class="card p-3 shadow-sm">
          <h5>Quick checklist</h5>
          <ul class="small mb-0">
            <li>Passport & visa/K-ETA</li>
            <li>Travel insurance</li>
            <li>Phone roaming / eSIM</li>
            <li>Adapter (Korea uses Type C/F)</li>
          </ul>
        </div>
      </div>
    </div>

  <?php elseif ($page === 'contact'): ?>
    <h2>Contact</h2>
    <p>If you have questions, leave a short message (demo uses GET to show submitted values).</p>
    <form method="get" class="row g-3">
      <input type="hidden" name="page" value="contact">
      <div class="col-md-6">
        <label class="form-label">Your name</label>
        <input class="form-control" name="name" value="<?php echo isset($_GET['name'])? esc($_GET['name']) : ''; ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" value="<?php echo isset($_GET['email'])? esc($_GET['email']) : ''; ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Message</label>
        <textarea class="form-control" rows="4" name="msg"><?php echo isset($_GET['msg'])? esc($_GET['msg']) : ''; ?></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Send (GET)</button>
      </div>
    </form>

    <?php if (isset($_GET['name']) || isset($_GET['msg'])): ?>
      <div class="alert alert-success mt-3">
        Thank you, <strong><?php echo isset($_GET['name'])? esc($_GET['name']) : 'guest'; ?></strong>. We received your message.
        <div class="small mt-1"><strong>Message:</strong> <?php echo esc(isset($_GET['msg'])? $_GET['msg'] : ''); ?></div>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <h2>Page not found</h2>
    <p><a href="?page=home">Return to Home</a></p>
  <?php endif; ?>
</div>

<footer class="bg-white mt-5 border-top">
  <div class="container py-4 d-flex justify-content-between align-items-center">
    <div class="small text-muted">© <?php echo date('Y'); ?> MN → KR Travel • Student demo</div>
    <div>
      <a class="small text-muted me-3" href="?page=plan">Travel tips</a>
      <a class="small text-muted" href="?page=contact">Contact</a>
    </div>
  </div>
</footer>
<div class="modal fade" id="placeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Place</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <img id="modalImage" src="" alt="" class="img-fluid mb-3" style="width:100%; height:300px; object-fit:cover;">
        <p class="text-muted" id="modalLocation"></p>
        <p id="modalDesc"></p>
      </div>
      <div class="modal-footer">
        <a id="modalOpenPage" href="#" class="btn btn-primary">Open Page</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  var placeModal = document.getElementById('placeModal');
  placeModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var name = button.getAttribute('data-name');
    var loc = button.getAttribute('data-location');
    var desc = button.getAttribute('data-desc');
    var img = button.getAttribute('data-image');
    document.getElementById('modalTitle').textContent = name;
    document.getElementById('modalLocation').textContent = loc;
    document.getElementById('modalDesc').textContent = desc;
    document.getElementById('modalImage').src = img;
    document.getElementById('modalOpenPage').href = '?page=places';
  });
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
