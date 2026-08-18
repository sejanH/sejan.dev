// Dedicated ultra-light frontend JavaScript for public blog
document.addEventListener('DOMContentLoaded', () => {
    // 1. Reading Progress Bar
    const progressEl = document.getElementById('scrollProgress');
    if (progressEl) {
        window.addEventListener('scroll', () => {
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            if (totalHeight > 0) {
                const progress = (window.scrollY / totalHeight) * 100;
                progressEl.style.width = `${Math.min(100, Math.max(0, progress))}%`;
            }
        }, { passive: true });
    }
});
