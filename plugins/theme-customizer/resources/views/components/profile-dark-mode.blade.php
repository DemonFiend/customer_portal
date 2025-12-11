<!-- Dark Mode Toggle Component for Profile Page -->
<!-- To use: @include('theme-customizer::components.profile-dark-mode') -->

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fe fe-moon"></i> Theme Preference
        </h6>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1">Dark Mode</h6>
                <small class="text-muted">Switch between light and dark theme</small>
            </div>
            <div>
                <label class="theme-toggle-switch">
                    <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()">
                    <span class="slider-round"></span>
                </label>
            </div>
        </div>
        <div id="themeStatus" class="mt-3 text-center">
            <small class="text-muted">
                <i class="fe fe-sun"></i> Currently using <strong id="currentTheme">Light Mode</strong>
            </small>
        </div>
    </div>
</div>

<style>
    .theme-toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .theme-toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider-round {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 30px;
    }

    .slider-round:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider-round {
        background-color: #667eea;
    }

    input:checked + .slider-round:before {
        transform: translateX(30px);
    }
</style>

<script>
    // Load current theme preference
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/portal/theme/preference', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const toggle = document.getElementById('darkModeToggle');
            const currentTheme = document.getElementById('currentTheme');
            
            if (data.dark_mode) {
                toggle.checked = true;
                currentTheme.textContent = 'Dark Mode';
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })
        .catch(error => {
            console.error('Error loading theme preference:', error);
            // Show user-facing error
            const themeStatus = document.getElementById('themeStatus');
            if (themeStatus) {
                themeStatus.innerHTML = '<small class="text-danger"><i class="fe fe-alert-triangle"></i> Failed to load theme preference</small>';
            }
        });
    });

    function toggleDarkMode() {
        const toggle = document.getElementById('darkModeToggle');
        const currentTheme = document.getElementById('currentTheme');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            toggle.checked = !toggle.checked;
            return;
        }
        
        fetch('/portal/theme/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.dark_mode) {
                    currentTheme.textContent = 'Dark Mode';
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    currentTheme.textContent = 'Light Mode';
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            }
        })
        .catch(error => {
            console.error('Error toggling theme:', error);
            toggle.checked = !toggle.checked; // Revert on error
            
            // Show user-facing error (safe text-only update)
            const themeStatus = document.getElementById('themeStatus');
            if (themeStatus) {
                // Clear and rebuild safely
                themeStatus.textContent = '';
                
                const small = document.createElement('small');
                small.className = 'text-danger';
                
                const icon = document.createElement('i');
                icon.className = 'fe fe-alert-triangle';
                
                small.appendChild(icon);
                small.appendChild(document.createTextNode(' Failed to toggle theme. Please try again.'));
                themeStatus.appendChild(small);
                
                // Reset to normal after 3 seconds
                setTimeout(() => {
                    const isDark = toggle.checked;
                    themeStatus.textContent = '';
                    
                    const resetSmall = document.createElement('small');
                    resetSmall.className = 'text-muted';
                    
                    const resetIcon = document.createElement('i');
                    resetIcon.className = 'fe fe-' + (isDark ? 'moon' : 'sun');
                    
                    resetSmall.appendChild(resetIcon);
                    resetSmall.appendChild(document.createTextNode(' Currently using '));
                    
                    const strong = document.createElement('strong');
                    strong.id = 'currentTheme';
                    strong.textContent = isDark ? 'Dark Mode' : 'Light Mode';
                    
                    resetSmall.appendChild(strong);
                    themeStatus.appendChild(resetSmall);
                }, 3000);
            }
        });
    }
</script>
