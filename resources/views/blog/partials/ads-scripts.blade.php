<!-- ==========================================
     Blogfront Global Advertising & Analytics Scripts
     Non-blocking & performance-optimized loading
     ========================================== -->

<!-- Google tag (gtag.js) - Asynchronous -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZPLGDYRX3C"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-ZPLGDYRX3C');
</script>

<!-- Non-Blocking Deferred Ad Network Loader (Zero Render-Blocking / Zero LCP Delay) -->
<script>
(function() {
    let adsLoaded = false;

    function loadAdScripts() {
        if (adsLoaded) return;
        adsLoaded = true;

        // Cleanup interaction listeners once triggered
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

    // Trigger on first user interaction (scroll, touch, click)
    ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(event) {
        window.addEventListener(event, loadAdScripts, { once: true, passive: true });
    });

    // Fallback: load during browser idle or after 3.5s timeout
    if ('requestIdleCallback' in window) {
        window.addEventListener('load', function() {
            requestIdleCallback(function() {
                setTimeout(loadAdScripts, 2000);
            }, { timeout: 4000 });
        });
    } else {
        window.addEventListener('load', function() {
            setTimeout(loadAdScripts, 3000);
        });
    }
})();
</script>
