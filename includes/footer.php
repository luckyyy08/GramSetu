</main>

<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 text-center text-lg-start">
                <img src="<?php echo APP_URL; ?>/assets/img/rectangular logo.png" alt="GramSetu Logo" class="mb-4 bg-white p-2 rounded shadow-sm" style="height: 60px; width: auto; object-fit: contain;">
                <p class="text-white-50">गाव, ग्रामपंचायत, व्यवसाय आणि विकास यांना जोडणारं डिजिटल ecosystem.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 mb-4">
                <h6 class="mb-4 fw-bold">दुवे</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none mb-2 d-block">मुख्यपृष्ठ</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none mb-2 d-block">सूचना फलक</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none mb-2 d-block">तक्रार नोंदवा</a></li>
                </ul>
            </div>
            <div class="col-lg-3 mb-4">
                <h6 class="mb-4 fw-bold">संपर्क</h6>
                <ul class="list-unstyled">
                    <li class="text-white-50 mb-2"><i class="fas fa-envelope me-2"></i> info@gramsetu.com</li>
                    <li class="text-white-50 mb-2"><i class="fas fa-phone me-2"></i> +91 98765 43210</li>
                    <li class="text-white-50 mb-2"><i class="fas fa-map-marker-alt me-2"></i> ग्रामपंचायत कार्यालय, महाराष्ट्र</li>
                </ul>
            </div>
            <div class="col-lg-3 mb-4">
                <h6 class="mb-4 fw-bold">आणीबाणीचे संपर्क</h6>
                <ul class="list-unstyled">
                    <li class="text-danger mb-2 fw-bold"><i class="fas fa-ambulance me-2"></i> रुग्णवाहिका: 108</li>
                    <li class="text-danger mb-2 fw-bold"><i class="fas fa-shield-alt me-2"></i> पोलीस: 100</li>
                </ul>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center text-white-50 py-3">
            <small>&copy; <?php echo date('Y'); ?> GramSetu. सर्व हक्क राखीव. | डिजिटल ग्रामपंचायत उपक्रम</small>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Dark Mode Logic
    const darkModeBtn = document.getElementById('darkModeBtn');
    const body = document.body;
    const icon = darkModeBtn ? darkModeBtn.querySelector('i') : null;

    // Check for saved user preference
    const savedTheme = localStorage.getItem('theme') || 'light';
    body.setAttribute('data-bs-theme', savedTheme);
    updateIcon(savedTheme);

    if (darkModeBtn) {
        darkModeBtn.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            body.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    function updateIcon(theme) {
        if (!icon) return;
        if (theme === 'dark') {
            icon.classList.replace('fa-moon', 'fa-sun');
            icon.style.color = '#ffc107';
        } else {
            icon.classList.replace('fa-sun', 'fa-moon');
            icon.style.color = 'inherit';
        }
    }
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
