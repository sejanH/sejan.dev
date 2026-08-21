<!-- ==========================================
     Blogfront High-Performance Analytics & Ads Loader
     Zero Render-Blocking, Zero Forced Reflows, 100% Core Web Vitals Friendly
     ========================================== -->

<!-- Deferred Google Analytics & Tag Manager (Zero TBT / Zero Initial Reflow) -->
<script>
(function() {
    window.dataLayer = window.dataLayer || [];
    function gtag(){ dataLayer.push(arguments); }
    window.gtag = gtag;
    gtag('js', new Date());
    gtag('config', 'G-ZPLGDYRX3C', { 'send_page_view': true });

    let analyticsLoaded = false;
    function loadAnalytics() {
        if (analyticsLoaded) return;
        analyticsLoaded = true;

        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=G-ZPLGDYRX3C';
        document.head.appendChild(script);
    }

    // Load analytics on first human interaction or after browser is completely idle
    ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(event) {
        window.addEventListener(event, loadAnalytics, { once: true, passive: true });
    });

    if ('requestIdleCallback' in window) {
        window.addEventListener('load', function() {
            requestIdleCallback(function() {
                setTimeout(loadAnalytics, 2500);
            }, { timeout: 4000 });
        });
    } else {
        window.addEventListener('load', function() {
            setTimeout(loadAnalytics, 3000);
        });
    }
})();
</script>

<!-- Non-Blocking Deferred Ad Network Loader (Triggered Strictly On User Interaction) -->
<script>
(function() {
    let adsLoaded = false;

    function loadAdScripts() {
        if (adsLoaded) return;
        adsLoaded = true;

        // Cleanup interaction listeners
        ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(event) {
            window.removeEventListener(event, loadAdScripts, { passive: true });
        });

        // 1. Effective CPM Network Script
        var script1 = document.createElement('script');
        script1.type = 'text/javascript';
        script1.async = true;
        script1.src = 'https://pl30906445.effectivecpmnetwork.com/ad/70/03/ad7003ca9265ab5517c0adcac719c9a1.js';
        document.body.appendChild(script1);

        // 2. High Performance Format Options & Script
        window.atOptions = {
            'key' : '808a98325f4d7750452e69c136936ba3',
            'format' : 'iframe',
            'height' : 60,
            'width' : 468,
            'params' : {}
        };

        var script2 = document.createElement('script');
        script2.type = 'text/javascript';
        script2.async = true;
        script2.src = 'https://www.highperformanceformat.com/808a98325f4d7750452e69c136936ba3/invoke.js';
        document.body.appendChild(script2);
    }

    // Trigger on first user interaction
    ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(event) {
        window.addEventListener(event, loadAdScripts, { once: true, passive: true });
    });

    // Fallback on idle
    if ('requestIdleCallback' in window) {
        window.addEventListener('load', function() {
            requestIdleCallback(function() {
                setTimeout(loadAdScripts, 4000);
            }, { timeout: 6000 });
        });
    } else {
        window.addEventListener('load', function() {
            setTimeout(loadAdScripts, 5000);
        });
    }
})();
</script>
