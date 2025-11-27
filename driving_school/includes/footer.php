<footer class="animated-footer">
    <div class="footer-container">
        <p>&copy; <?= date('Y'); ?> Driving School Booking System 🚗💈 — All rights reserved.</p>
        
        <div class="social-icons">
            <a href="https://instagram.com/yourhandle" target="_blank" title="Instagram">📸</a>
            <a href="https://facebook.com/yourhandle" target="_blank" title="Facebook">📘</a>
            <a href="https://tiktok.com/@yourhandle" target="_blank" title="TikTok">🎵</a>
            <a href="mailto:contact@yourdomain.com" title="Email">✉️</a>
        </div>
    </div>
</footer>

<style>
    .animated-footer {
        background: linear-gradient(to right, rgb(53, 18, 230), #1f1c2c);
        color: white;
        padding: 20px 0;
        text-align: center;
        position: relative;
        animation: fadeInUp 1s ease-in-out;
    }

    .footer-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .animated-footer p {
        margin: 10px 0;
        font-size: 1rem;
    }

    .social-icons {
        margin-top: 10px;
    }

    .social-icons a {
        margin: 0 8px;
        font-size: 1.4rem;
        text-decoration: none;
        color: white;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .social-icons a:hover {
        transform: scale(1.3);
        filter: drop-shadow(0 0 5px #fff);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script src="/driving_school/assets/js/css-tracker.js"></script>
