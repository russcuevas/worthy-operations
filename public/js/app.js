/**
 * Worthy Acosta Inventory & Task Management
 * Core Interactive Scripts & UI Handlers
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sidebar Mobile Toggle
    const sidebar = document.querySelector('.app-sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle-btn');
    const overlay = document.querySelector('.sidebar-overlay');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }

    // 2. DataTables Auto-Initialization
    if (window.$ && $.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            }
        });
    }

    // 3. Modal Trigger Helpers (data-modal-open / data-modal-close)
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-modal-open');
            openModal(targetId);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = btn.closest('.modal-backdrop-custom');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });

    // 4. Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop-custom').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // 5. Notification Dropdown Toggle & AJAX Fetch
    setupNotificationDropdown();

    // 6. User Profile Dropdown Toggle
    setupUserProfileDropdown();
});

// ================= USER PROFILE DROPDOWN =================
function setupUserProfileDropdown() {
    const profileBtn = document.getElementById('userProfileDropdownBtn');
    const profileMenu = document.getElementById('userProfileDropdownMenu');

    if (!profileBtn || !profileMenu) return;

    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = profileMenu.classList.contains('show');
        // close other dropdowns
        document.querySelectorAll('.notification-dropdown-menu.show').forEach(el => el.classList.remove('show'));
        if (isOpen) {
            profileMenu.classList.remove('show');
        } else {
            profileMenu.classList.add('show');
        }
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
            profileMenu.classList.remove('show');
        }
    });
}

// ================= NOTIFICATION DROPDOWN =================
function setupNotificationDropdown() {
    const notifBtn = document.getElementById('notificationDropdownBtn');
    const notifMenu = document.getElementById('notificationDropdownMenu');
    const notifBody = document.getElementById('notifDropdownBody');
    const notifBadge = document.getElementById('notifBadge');
    const markAllBtn = document.getElementById('markAllReadBtn');

    if (!notifBtn || !notifMenu) return;

    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = notifMenu.classList.contains('show');
        if (isOpen) {
            notifMenu.classList.remove('show');
        } else {
            notifMenu.classList.add('show');
            loadRecentNotifications();
        }
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
            notifMenu.classList.remove('show');
        }
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (notifBadge) notifBadge.style.display = 'none';
                    document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
                    showToast('Notifications', 'All notifications marked as read', 'success');
                }
            })
            .catch(err => console.error(err));
        });
    }

    function loadRecentNotifications() {
        fetch('/notifications/recent', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (notifBadge) {
                if (data.unread_count > 0) {
                    notifBadge.innerText = data.unread_count;
                    notifBadge.style.display = 'flex';
                } else {
                    notifBadge.style.display = 'none';
                }
            }

            notifBody.innerHTML = '';
            if (!data.notifications || data.notifications.length === 0) {
                notifBody.innerHTML = `
                    <div style="padding: 30px 20px; text-align: center; color: var(--text-light);">
                        <i class="bi bi-bell-slash" style="font-size: 32px; color: var(--color-wave-cyan); display: block; margin-bottom: 6px;"></i>
                        <div style="font-size: 13px; font-weight: 600; color: var(--color-deep-navy);">No notifications yet</div>
                        <div style="font-size: 11.5px; color: var(--text-muted);">You're all caught up!</div>
                    </div>
                `;
                return;
            }

            data.notifications.forEach(n => {
                let iconClass = 'bi-info-circle-fill';
                let iconBg = '#E0F2FE';
                let iconColor = '#0284C7';

                if (n.type === 'task_assigned') {
                    iconClass = 'bi-plus-circle-fill';
                    iconBg = '#E0F2FE';
                    iconColor = '#075998';
                } else if (n.type === 'task_approved') {
                    iconClass = 'bi-check-circle-fill';
                    iconBg = '#DCFCE7';
                    iconColor = '#166534';
                } else if (n.type === 'task_returned') {
                    iconClass = 'bi-exclamation-circle-fill';
                    iconBg = '#FEE2E2';
                    iconColor = '#DC2626';
                } else if (n.type === 'low_stock') {
                    iconClass = 'bi-exclamation-triangle-fill';
                    iconBg = '#FEF3C7';
                    iconColor = '#B45309';
                }

                const item = document.createElement('div');
                item.className = `notif-item ${n.is_read ? '' : 'unread'}`;
                item.innerHTML = `
                    <div class="notif-icon" style="background: ${iconBg}; color: ${iconColor};">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title-text">${n.title}</div>
                        <div class="notif-message-text">${n.message}</div>
                        <div class="notif-time-text">${n.time_ago}</div>
                    </div>
                `;

                item.addEventListener('click', () => {
                    if (!n.is_read) {
                        fetch(`/notifications/${n.id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        item.classList.remove('unread');
                    }

                    notifMenu.classList.remove('show');

                    if (n.link_url) {
                        let targetUrl = n.link_url;
                        try {
                            if (targetUrl.startsWith('http://') || targetUrl.startsWith('https://')) {
                                const parsed = new URL(targetUrl);
                                targetUrl = parsed.pathname + parsed.search;
                            }
                        } catch (e) {}
                        window.location.href = targetUrl;
                    }
                });

                notifBody.appendChild(item);
            });
        })
        .catch(err => {
            console.error(err);
            notifBody.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--danger);">Failed to load notifications.</div>';
        });
    }
}

// ================= TOAST MANAGER =================
function showToast(title, message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `wa-toast toast-${type}`;

    let iconClass = 'bi-check-circle-fill';
    if (type === 'danger') iconClass = 'bi-x-circle-fill';
    if (type === 'warning') iconClass = 'bi-exclamation-triangle-fill';
    if (type === 'info') iconClass = 'bi-info-circle-fill';

    toast.innerHTML = `
        <div class="toast-icon">
            <i class="bi ${iconClass}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button type="button" class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 50);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 4500);
}

// ================= MODAL HELPERS =================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        modal.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        modal.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }
}

window.openModal = openModal;
window.closeModal = closeModal;
window.openCustomModal = openModal;
window.closeCustomModal = closeModal;

function clearUrlParams(keys = []) {
    if (!window.history || !window.history.replaceState) return;
    try {
        const url = new URL(window.location.href);
        const modalKeys = keys.length > 0 ? keys : ['stock_in_item', 'stock_out_item', 'transfer_item', 'adjust_item', 'verify_task', 'open_task', 'submit_task', 'view_item'];
        let changed = false;
        modalKeys.forEach(k => {
            if (url.searchParams.has(k)) {
                url.searchParams.delete(k);
                changed = true;
            }
        });
        if (changed) {
            const cleanHref = url.pathname + (url.search ? url.search : '') + url.hash;
            window.history.replaceState({}, document.title, cleanHref);
        }
    } catch (e) {
        console.error(e);
    }
}

// ================= AJAX FORM SUBMITTER =================
function handleAjaxForm(formElement, options = {}) {
    if (!formElement) return;

    formElement.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous error messages & highlights
        formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        formElement.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        const submitBtn = formElement.querySelector('button[type="submit"]');
        let originalText = '';
        if (submitBtn) {
            originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }

        const formData = new FormData(formElement);
        const actionUrl = formElement.getAttribute('action') || window.location.href;
        const method = formElement.getAttribute('method') || 'POST';

        fetch(actionUrl, {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json().catch(() => null);
            if (!response.ok) {
                if (response.status === 422 && data && data.errors) {
                    Object.keys(data.errors).forEach(fieldName => {
                        let input = formElement.querySelector(`[name="${fieldName}"]`);
                        if (!input) {
                            input = formElement.querySelector(`[name="${fieldName}[]"]`);
                        }
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.innerText = data.errors[fieldName][0];
                            input.parentNode.appendChild(feedback);
                        }
                    });
                    showToast('Validation Error', 'Please check the highlighted fields below.', 'danger');
                } else {
                    showToast('Error', (data && data.message) ? data.message : 'An error occurred while saving.', 'danger');
                }
                return;
            }

            showToast('Success', (data && data.message) ? data.message : 'Action completed successfully!', 'success');
            
            const modal = formElement.closest('.modal-backdrop-custom');
            if (modal) {
                closeModal(modal.id);
            }

            if (options.onSuccess) {
                options.onSuccess(data);
            } else {
                setTimeout(() => {
                    const url = new URL(window.location.href);
                    ['stock_in_item', 'stock_out_item', 'transfer_item', 'adjust_item', 'verify_task', 'open_task', 'submit_task', 'view_item'].forEach(k => url.searchParams.delete(k));
                    const cleanHref = url.pathname + (url.search ? url.search : '') + url.hash;
                    if (window.location.pathname + window.location.search + window.location.hash === cleanHref) {
                        window.location.reload();
                    } else {
                        window.location.href = cleanHref;
                    }
                }, 900);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Connection Error', 'Failed to communicate with the server.', 'danger');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });
}
