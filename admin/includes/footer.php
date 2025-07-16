    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('expanded');
                    
                    const icon = sidebarToggle.querySelector('i');
                    if (sidebar.classList.contains('expanded')) {
                        icon.className = 'fas fa-times';
                    } else {
                        icon.className = 'fas fa-bars';
                    }
                    
                    // Save state to localStorage
                    localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
                });
                
                // Restore sidebar state from localStorage
                const sidebarExpanded = localStorage.getItem('sidebarExpanded');
                if (sidebarExpanded === 'true') {
                    sidebar.classList.add('expanded');
                    const icon = sidebarToggle.querySelector('i');
                    icon.className = 'fas fa-times';
                }
            }
            
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            const dropzones = document.querySelectorAll('.dropzone');
            dropzones.forEach(function(dropzone) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });
                
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, highlight, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, unhighlight, false);
                });
                
                dropzone.addEventListener('drop', handleDrop, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            function highlight(e) {
                e.currentTarget.classList.add('dragover');
            }
            
            function unhighlight(e) {
                e.currentTarget.classList.remove('dragover');
            }
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                const fileInput = e.currentTarget.querySelector('input[type="file"]');
                
                if (fileInput) {
                    fileInput.files = files;
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            }
            
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const confirmMessage = this.getAttribute('data-confirm') || 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?';
                    
                    if (confirm(confirmMessage)) {
                        window.location.href = this.href;
                    }
                });
            });
            
            const forms = document.querySelectorAll('form[data-confirm="true"]');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const confirmMessage = this.getAttribute('data-confirm-message') || 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?';
                    
                    if (confirm(confirmMessage)) {
                        this.submit();
                    }
                });
            });
            
            const imagePreviewInputs = document.querySelectorAll('input[type="file"][data-preview]');
            imagePreviewInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const previewId = this.getAttribute('data-preview');
                    const preview = document.getElementById(previewId);
                    
                    if (this.files && this.files[0] && preview) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        };
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });
            
            const autoSaveForms = document.querySelectorAll('form[data-auto-save="true"]');
            autoSaveForms.forEach(function(form) {
                const inputs = form.querySelectorAll('input, textarea, select');
                
                inputs.forEach(function(input) {
                    input.addEventListener('input', debounce(function() {
                        saveFormData(form);
                    }, 1000));
                });
            });
            
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
            
            function saveFormData(form) {
                const formData = new FormData(form);
                formData.append('auto_save', '1');
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Otomatik kayıt yapıldı', 'success', 2000);
                    }
                })
                .catch(error => {
                    console.error('Otomatik kayıt hatası:', error);
                });
            }
            
            function showNotification(message, type = 'info', duration = 5000) {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} notification-toast`;
                notification.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Close"></button>
                `;
                
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 300px;
                    opacity: 0;
                    transform: translateX(300px);
                    transition: all 0.3s ease;
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                const closeBtn = notification.querySelector('.btn-close');
                closeBtn.addEventListener('click', () => {
                    removeNotification(notification);
                });
                
                setTimeout(() => {
                    removeNotification(notification);
                }, duration);
            }
            
            function removeNotification(notification) {
                if (notification && notification.parentNode) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(300px)';
                    setTimeout(() => {
                        notification.parentNode.removeChild(notification);
                    }, 300);
                }
            }
            
            window.showNotification = showNotification;
            
            console.log('🚀 BERAT K - R10 Admin Panel loaded successfully!');
        });
    </script>
    
    <style>
        .dropzone {
            border: 2px dashed var(--dark-border);
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .dropzone:hover,
        .dropzone.dragover {
            border-color: var(--primary-color);
            background-color: rgba(108, 92, 231, 0.1);
        }
        
        .notification-toast {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            border: none;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        .table-actions {
            white-space: nowrap;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--dark-border);
        }
        
        @media (max-width: 768px) {
            .notification-toast {
                right: 10px;
                left: 10px;
                min-width: auto;
            }
        }
    </style>
</body>
</html>