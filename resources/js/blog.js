// Modern, Ultra-Light JavaScript for Sejan Blog Frontend (Zero Forced Reflows)
document.addEventListener('DOMContentLoaded', () => {
    // 1. Reading Progress Bar (for single post pages)
    const progressEl = document.getElementById('scrollProgress');
    if (progressEl) {
        let maxScroll = 0;
        let ticking = false;

        const calculateMaxScroll = () => {
            maxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        };

        calculateMaxScroll();
        window.addEventListener('resize', calculateMaxScroll, { passive: true });

        const onScroll = () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
                    const progress = Math.min(100, Math.max(0, (scrollY / maxScroll) * 100));
                    progressEl.style.transform = `scaleX(${progress / 100})`;
                    ticking = false;
                });
                ticking = true;
            }
        };

        window.addEventListener('scroll', onScroll, { passive: true });
    }

    // 2. Mobile Menu Toggle
    const menuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // 3. Comment Reply Trigger
    window.replyToComment = function (commentId, authorName) {
        const parentIdInput = document.getElementById('parent_id_input');
        const replyBanner = document.getElementById('replying_to_banner');
        const replyAuthorName = document.getElementById('reply_author_name');
        const commentForm = document.getElementById('comment_form_container');

        if (parentIdInput) parentIdInput.value = commentId;
        if (replyAuthorName) replyAuthorName.textContent = authorName;
        if (replyBanner) replyBanner.classList.remove('hidden');

        if (commentForm) {
            commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const contentTextarea = document.getElementById('comment_content');
            if (contentTextarea) contentTextarea.focus();
        }
    };

    window.cancelReply = function () {
        const parentIdInput = document.getElementById('parent_id_input');
        const replyBanner = document.getElementById('replying_to_banner');

        if (parentIdInput) parentIdInput.value = '';
        if (replyBanner) replyBanner.classList.add('hidden');
    };
});
