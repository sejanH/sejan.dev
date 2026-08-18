# 📋 Project Roadmap & Task Board: sejan.dev

> **A modern, high-performance Laravel 12 blog system with a WordPress-like Media Manager, self-hosted Rich Text Editor (CKEditor), manual comment moderation workflow, and full WordPress legacy data migration.**

---

## 🧭 Milestone Progress Overview

| Module / Milestone | Description | Priority | Status |
| :--- | :--- | :---: | :---: |
| **1. WordPress-like Media Manager** | Visual library, drag & drop uploader, media picker modal, thumbnail inspector | 🔴 High | ✅ Completed |
| **2. Self-Hosted Rich Text Editor** | Self-hosted CKEditor 5 with Media Manager insertion bridge | 🔴 High | ✅ Completed |
| **3. Comments Engine & Moderation** | Threaded replies, WP comments importer, manual approval admin workflow | 🔴 High | ✅ Completed |
| **4. WordPress Complete Ingestion** | Extract DB, copy `wp-content/uploads/`, rewrite inline URLs, build 301 redirects | 🔴 High | ⏳ Ready for Path |
| **5. Core Blog & Admin System** | Laravel 12 foundation, Blade + Tailwind UI, admin auth, registration lock | 🟢 Completed | ✅ Done |

---

## 📸 Phase 1: WordPress-like Media Manager (Completed ✅)

- [x] **Database Schema & Model (`media` table)**
  - [x] Migration: `id`, `user_id`, `filename`, `original_name`, `disk`, `path`, `mime_type`, `size`, `alt_text`, `caption`, `width`, `height`, `wp_attachment_id`, timestamps.
  - [x] Model `App\Models\Media` with scopes (images, search), formatters, and public URL helpers.
- [x] **File Storage & Upload Processing**
  - [x] Storage connected at `storage/app/public/media/` with symlink to `public/storage`.
  - [x] Drag-and-drop / multi-file asynchronous upload processing in `Admin\MediaController`.
- [x] **Admin Media Manager Interface (`/admin/media`)**
  - [x] Grid view of uploaded media items with search & MIME filtering.
  - [x] Detail modal: View image preview, edit Alt text, Caption, dimensions, file size, and delete.
- [x] **Reusable Media Picker Modal (`admin.partials.media-picker-modal`)**
  - [x] Reusable modal component for selecting featured images or inserting images directly into the post editor.

---

## ✍️ Phase 2: Self-Hosted Rich Text Editor (CKEditor) (Completed ✅)

- [x] **Self-Hosted Editor Bundle**
  - [x] Self-hosted CKEditor 5 standalone build (`public/vendor/ckeditor5/ckeditor.js`) with zero cloud dependencies or API keys.
  - [x] Custom dark-theme stylesheet (`public/vendor/ckeditor5/editor-dark.css`).
- [x] **Editor Tooling & Capabilities**
  - [x] Headings (H2, H3, H4), Bold, Italic, Underline, Strikethrough, Inline code, Blockquotes, Lists, Tables, Code Blocks.
- [x] **Media Manager Integration Bridge**
  - [x] Custom "Insert Media from Library" toolbar button launching the Laravel Media Picker modal.
  - [x] Direct image insertion into article content with responsive markup, alt text, and captions.

---

## 💬 Phase 3: Comments Engine & Manual Approval Workflow (Completed ✅)

- [x] **Database Schema & Model (`comments` table)**
  - [x] Migration: `id`, `post_id`, `user_id`, `parent_id`, `author_name`, `author_email`, `author_url`, `content`, `status` (`pending`, `approved`, `spam`, `trash`), `ip_address`, `user_agent`, `wp_comment_id`, timestamps.
  - [x] Model `App\Models\Comment` with threaded relationships (`replies`, `approvedReplies`), Gravatar accessor, and scopes.
- [x] **WordPress Comments Importer Engine**
  - [x] Extracted comments from `wp_comments`, mapped reply parent/child hierarchy, and imported into `WordPressDatabaseMigrator`.
- [x] **Admin Comments Moderation Center (`/admin/comments`)**
  - [x] Filter tabs: **Pending Review** (with live count badge), **Approved**, **Spam**, **Trash**.
  - [x] Actions: 1-click **Approve**, **Reject / Hold**, **Mark as Spam**, **Delete Permanently**, and **Quick Reply**.
- [x] **Public Blog Comments UI (`resources/views/blog/show.blade.php`)**
  - [x] Comment submission form with anti-spam honeypot.
  - [x] Clear moderation notice: *"Comments are manually reviewed and approved by the administrator before appearing."*
  - [x] Threaded nested replies display with Gravatar avatars and author badges.

---

## 📦 Phase 4: Full WordPress System Migration Execution (Ready ⏳)

- [ ] **Automatic Configuration Detection**
  - [ ] Parse `wp-config.php` to extract database credentials and table prefix.
  - [ ] Detect WordPress upload directory (`wp-content/uploads/`).
- [ ] **Complete Data Extraction & Mapping**
  - [ ] Ingest all published & draft posts and pages.
  - [ ] Ingest categories and tags.
  - [ ] Ingest media library and copy uploads into Laravel storage.
  - [ ] Ingest and map historical comments.
  - [ ] Generate 301 permanent redirect rules for all legacy URL structures.
