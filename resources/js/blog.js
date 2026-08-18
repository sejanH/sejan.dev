// Modern, Ultra-Light JavaScript for Sejan Blog Frontend
document.addEventListener('DOMContentLoaded', () => {
    // 1. Reading Progress Bar (for single post pages)
    const progressEl = document.getElementById('scrollProgress');
    if (progressEl) {
        let progressTicking = false;
        const updateProgress = () => {
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            if (totalHeight > 0) {
                const progress = (window.scrollY / totalHeight) * 100;
                progressEl.style.width = `${Math.min(100, Math.max(0, progress))}%`;
            }
            progressTicking = false;
        };

        window.addEventListener('scroll', () => {
            if (!progressTicking) {
                window.requestAnimationFrame(updateProgress);
                progressTicking = true;
            }
        }, { passive: true });
    }

    // 2. Sticky Navbar Shadow Enhancement on Scroll
    const headerEl = document.getElementById('mainHeader');
    if (headerEl) {
        let isScrolled = false;
        let headerTicking = false;

        const updateHeader = () => {
            const shouldBeScrolled = window.scrollY > 20;
            if (shouldBeScrolled !== isScrolled) {
                isScrolled = shouldBeScrolled;
                if (isScrolled) {
                    headerEl.classList.add('shadow-sm', 'bg-white/95');
                    headerEl.classList.remove('shadow-xs', 'bg-white/90');
                } else {
                    headerEl.classList.remove('shadow-sm', 'bg-white/95');
                    headerEl.classList.add('shadow-xs', 'bg-white/90');
                }
            }
            headerTicking = false;
        };

        window.addEventListener('scroll', () => {
            if (!headerTicking) {
                window.requestAnimationFrame(updateHeader);
                headerTicking = true;
            }
        }, { passive: true });
    }

    // 3. Rotating Words Animation in Hero Section
    const rotatingWordEl = document.getElementById('rotatingWord');
    if (rotatingWordEl) {
        const words = ["Innovation", "Technology", "Creativity", "Discovery"];
        let index = 0;

        setInterval(() => {
            rotatingWordEl.style.opacity = '0';
            rotatingWordEl.style.transform = 'translateY(10px)';

            setTimeout(() => {
                index = (index + 1) % words.length;
                rotatingWordEl.textContent = words[index];
                rotatingWordEl.style.opacity = '1';
                rotatingWordEl.style.transform = 'translateY(0)';
            }, 300);
        }, 3000);
    }

    // 4. Mobile Menu Toggle
    const menuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.contains('hidden');
            if (isHidden) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    // 5. Comment Reply Trigger
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
