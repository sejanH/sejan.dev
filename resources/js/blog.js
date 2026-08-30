// Modern, Ultra-Light JavaScript for Sejan Blog Frontend (Zero Forced Reflows)
document.addEventListener('DOMContentLoaded', () => {
    // 1. Reading Progress Bar
    const progressEl = document.getElementById('scrollProgress');
    if (progressEl) {
        let maxScroll = 0;
        let ticking = false;

        const calculateMaxScroll = () => {
            const doc = document.documentElement;
            const body = document.body;
            const scrollHeight = Math.max(
                body.scrollHeight, doc.scrollHeight,
                body.offsetHeight, doc.offsetHeight,
                body.clientHeight, doc.clientHeight
            );
            maxScroll = Math.max(1, scrollHeight - window.innerHeight);
        };

        const updateProgress = () => {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
            const progress = Math.min(1, Math.max(0, scrollY / maxScroll));
            progressEl.style.transform = `scaleX(${progress})`;
            ticking = false;
        };

        calculateMaxScroll();
        window.addEventListener('resize', () => { calculateMaxScroll(); updateProgress(); }, { passive: true });
        window.addEventListener('load', () => { calculateMaxScroll(); updateProgress(); }, { passive: true });

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateProgress);
                ticking = true;
            }
        }, { passive: true });

        updateProgress();
    }

    // 2. Dynamic Sticky Navigation Header Shrinking on Scroll
    const headerEl = document.getElementById('mainHeader');
    if (headerEl) {
        let isScrolled = false;
        const checkHeaderScroll = () => {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
            const shouldBeScrolled = scrollY > 20;
            if (shouldBeScrolled !== isScrolled) {
                isScrolled = shouldBeScrolled;
                if (isScrolled) {
                    headerEl.classList.add('header-scrolled');
                } else {
                    headerEl.classList.remove('header-scrolled');
                }
            }
        };

        window.addEventListener('scroll', checkHeaderScroll, { passive: true });
        checkHeaderScroll();
    }

    // 3. Mobile Menu Toggle
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
