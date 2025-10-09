// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.menu-item');
    const pages = document.querySelectorAll('.page');
    const toggleSidebar = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    
    // Function to switch pages
    function switchPage(pageId) {
        // Hide all pages
        pages.forEach(page => {
            page.classList.remove('active');
        });
        
        // Show selected page
        document.getElementById(pageId).classList.add('active');
        
        // Update active menu item
        menuItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-page') === pageId) {
                item.classList.add('active');
            }
        });
    }
    
    // Add click event to menu items
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const pageId = this.getAttribute('data-page');
            switchPage(pageId);
            
            // Close sidebar on mobile after selection
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('active');
            }
        });
    });
    
    // Toggle sidebar on mobile
    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Content tabs
    const contentTabs = document.querySelectorAll('.content-tab');
    contentTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            contentTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Initialize with dashboard
    switchPage('dashboard');
    
    // Initialize charts
    initializeCharts();
    
    // Initialize modals
    initializeModals();
    
    // Initialize form handlers
    initializeFormHandlers();
});

// Chart initialization
function initializeCharts() {
    // Traffic chart
    const trafficCtx = document.getElementById('trafficChart');
    if (trafficCtx) {
        // Simple chart implementation
        const canvas = trafficCtx;
        const ctx = canvas.getContext('2d');
        
        // Draw a simple line chart
        ctx.strokeStyle = '#fdbb2d';
        ctx.lineWidth = 2;
        ctx.beginPath();
        
        const data = [120, 150, 180, 200, 180, 220, 250];
        const width = canvas.width;
        const height = canvas.height;
        const padding = 40;
        
        data.forEach((value, index) => {
            const x = padding + (index * (width - 2 * padding) / (data.length - 1));
            const y = height - padding - (value / 300 * (height - 2 * padding));
            
            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        
        ctx.stroke();
        
        // Draw data points
        ctx.fillStyle = '#fdbb2d';
        data.forEach((value, index) => {
            const x = padding + (index * (width - 2 * padding) / (data.length - 1));
            const y = height - padding - (value / 300 * (height - 2 * padding));
            
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, 2 * Math.PI);
            ctx.fill();
        });
    }
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        modal.style.opacity = '0';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 300);
    }
}

function initializeModals() {
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });
    
    // Close modals with close buttons
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal.id);
        });
    });
}

// Content management functions
function editContent(contentId) {
    // Implement edit content functionality
    console.log('Edit content:', contentId);
    // You can open a modal or redirect to edit page
}

function deleteContent(contentId) {
    if (confirm('Are you sure you want to delete this content?')) {
        // Implement delete functionality
        fetch(`/admin/api/delete-content.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: contentId,
                type: 'guide'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function editVideo(videoId) {
    console.log('Edit video:', videoId);
}

function deleteVideo(videoId) {
    if (confirm('Are you sure you want to delete this video?')) {
        fetch(`/admin/api/delete-video.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: videoId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

// Form handlers
function initializeFormHandlers() {
    // Settings form
    const settingsForm = document.querySelector('form[action="settings.php"]');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/admin/settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Settings saved successfully', 'success');
                } else {
                    showAlert('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred', 'danger');
            });
        });
    }
}

// Alert system
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            alertDiv.style.background = 'var(--success)';
            break;
        case 'danger':
            alertDiv.style.background = 'var(--danger)';
            break;
        case 'warning':
            alertDiv.style.background = 'var(--warning)';
            break;
        default:
            alertDiv.style.background = '#0d6efd';
    }
    
    document.body.appendChild(alertDiv);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateX(100%)';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Search functionality
function initializeSearch() {
    const searchInput = document.querySelector('#search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });
    }
}

function performSearch(query) {
    if (query.length < 2) return;
    
    fetch(`/admin/api/search.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
}

function displaySearchResults(results) {
    const searchResults = document.querySelector('#search-results');
    if (!searchResults) return;
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div class="no-results">No results found</div>';
        return;
    }
    
    let html = '<div class="search-results-list">';
    results.forEach(result => {
        html += `
            <div class="search-result-item">
                <h4><a href="${result.type}.php?id=${result.id}">${result.title}</a></h4>
                <p>${result.excerpt}</p>
                <small>${result.type} • ${formatDate(result.created_at)}</small>
            </div>
        `;
    });
    html += '</div>';
    
    searchResults.innerHTML = html;
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

// Export functions for global use
window.AdminHub = {
    openModal,
    closeModal,
    editContent,
    deleteContent,
    editVideo,
    deleteVideo,
    showAlert
};
