<?php

if (!defined('USER_ADMIN')) {
    exit('No direct script access allowed');
}

if(!has_admin_access()){
    exit;
}

?>

<div class="general-wrapper">
    <!-- Header with Title and Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i><?php _e('Plugin Repository'); ?></h4>
        <a href="dashboard.php?viewpage=plugin" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> <?php _e('Back to Plugin List'); ?>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="plugin-search" placeholder="<?php _e('Search plugins...'); ?>">
            <button class="btn btn-primary" id="search-button" type="button"><?php _e('Search'); ?></button>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" id="tab-new" href="#"><?php _e('Latest Update'); ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-popular" href="#"><?php _e('Popular'); ?></a>
        </li>
    </ul>

    <!-- Loading Indicator -->
    <div class="text-center mb-4" id="loading-plugins">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden"><?php _e('Loading...'); ?></span>
        </div>
        <p class="mt-2"><?php _e('Loading plugins...'); ?></p>
    </div>

    <!-- Error Message -->
    <div class="alert alert-danger" id="plugin-error" style="display: none;">
        <i class="fas fa-exclamation-circle me-2"></i>
        <span><?php _e('Error loading plugins. Please try again.'); ?></span>
    </div>

    <!-- No Results Message -->
    <div class="alert alert-info" id="no-results" style="display: none;">
        <i class="fas fa-info-circle me-2"></i>
        <span><?php _e('No plugins found matching your search.'); ?></span>
    </div>

    <!-- Active Search Tag -->
    <div id="active-search-tag" class="mb-3" style="display: none;">
        <span class="badge bg-primary p-2">
            <span id="search-term-display"></span>
            <button type="button" class="btn-close btn-close-white ms-2" id="clear-search" aria-label="Clear search"></button>
        </span>
    </div>

    <!-- Plugin Grid -->
    <div class="row row-cols-1 row-cols-md-2 g-4" id="plugin-grid">
        <!-- Plugins will be loaded here dynamically -->
    </div>

    <!-- Pagination -->
    <nav class="mt-4">
        <ul class="pagination justify-content-center" id="pagination">
            <!-- Pagination will be generated dynamically -->
        </ul>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const apiUrl = 'https://api.cloudarcade.net/plugin-repo/plugin-repo-v3.php';
        let currentPage = 1;
        let currentTab = 'new';
        let currentSearch = '';
        const cmsVersion = '<?php echo VERSION; ?>';
        const purchaseCode = '<?php echo check_purchase_code(); ?>';
        const domain = '<?php echo DOMAIN; ?>';

        // Get installed plugins list
        const installedPlugins = <?php
                                    // Get the installed plugins names
                                    $installed_plugin_names = [];
                                    $_plugin_list = get_plugin_list();
                                    foreach ($_plugin_list as $plugin) {
                                        // Check if the first character is an underscore
                                        if (substr($plugin['dir_name'], 0, 1) === '_') {
                                            // Remove the first character
                                            $plugin['dir_name'] = substr($plugin['dir_name'], 1);
                                        }
                                        $installed_plugin_names[] = $plugin['dir_name'];
                                    }
                                    echo json_encode($installed_plugin_names);
                                    ?>;

        // Initial load
        loadPluginsByTab('new');

        // Tab event listeners
        document.getElementById('tab-popular').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveTab(this, 'popular');
        });

        document.getElementById('tab-new').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveTab(this, 'new');
        });

        // Search button event listener
        document.getElementById('search-button').addEventListener('click', function() {
            performSearch();
        });

        // Search input enter key event
        document.getElementById('plugin-search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Clear search button
        document.getElementById('clear-search').addEventListener('click', function() {
            clearSearch();
        });

        function performSearch() {
            const searchTerm = document.getElementById('plugin-search').value.trim();
            if (searchTerm.length > 0) {
                currentSearch = searchTerm;
                currentPage = 1;
                loadPlugins(getCurrentSort(), '', 1, searchTerm);
                
                // Show active search tag
                document.getElementById('search-term-display').textContent = searchTerm;
                document.getElementById('active-search-tag').style.display = 'inline-block';
            }
        }

        function clearSearch() {
            document.getElementById('plugin-search').value = '';
            currentSearch = '';
            document.getElementById('active-search-tag').style.display = 'none';
            loadPluginsByTab(currentTab);
        }

        function setActiveTab(element, tab) {
            // Clear search if active
            if (currentSearch) {
                clearSearch();
            }
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            element.classList.add('active');
            // Update current tab and load plugins
            currentTab = tab;
            currentPage = 1;
            loadPluginsByTab(tab);
        }

        function loadPluginsByTab(tab) {
            showLoading();

            let sort = 'popular';
            let filter = '';

            switch (tab) {
                case 'popular':
                    sort = 'popular';
                    break;
                case 'new':
                    sort = 'newest';
                    break;
            }

            loadPlugins(sort, filter);
        }

        function loadPlugins(sort, filter, page = 1, search = '') {
            // Build query parameters
            let params = new URLSearchParams({
                action: 'list_plugins',
                page: page,
                per_page: 12,
                sort: sort,
                code: purchaseCode,
                ref: domain,
                v: cmsVersion
            });

            if (filter) params.append('filter', filter);
            if (search) params.append('search', search);

            fetch(`${apiUrl}?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        if (data.error) {
                            document.getElementById('plugin-error').innerHTML = 
                                '<i class="fas fa-exclamation-circle me-2"></i>' +
                                '<span>' + data.error + '</span>';
                            document.getElementById('plugin-error').style.display = 'block';
                            hideLoading();
                            return;
                        }
                    }
                    
                    if (data.success && data.plugins && data.plugins.length > 0) {
                        // Mark plugins that are already installed
                        data.plugins.forEach(plugin => {
                            plugin.is_installed = installedPlugins.includes(plugin.dir_name);
                            // Check version compatibility
                            plugin.is_compatible = checkVersionCompatibility(plugin.require_version, cmsVersion);
                        });
                        
                        displayPlugins(data.plugins);
                        createPagination(data.page, data.total_pages);
                    } else {
                        document.getElementById('plugin-grid').innerHTML = '';
                        document.getElementById('no-results').style.display = 'block';
                        document.getElementById('pagination').style.display = 'none';
                    }
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error loading plugins:', error);
                    showError();
                });
        }

        function checkVersionCompatibility(requiredVersion, currentVersion) {
            if (!requiredVersion) return true; // If no required version specified, assume compatible

            // Compare version strings
            return compareVersions(currentVersion, requiredVersion) >= 0;
        }

        function compareVersions(v1, v2) {
            // Split version strings by dots
            const v1Parts = v1.split('.').map(Number);
            const v2Parts = v2.split('.').map(Number);
            
            // Compare each part of the version
            for (let i = 0; i < Math.max(v1Parts.length, v2Parts.length); i++) {
                const v1Part = v1Parts[i] || 0;
                const v2Part = v2Parts[i] || 0;
                
                if (v1Part > v2Part) return 1;
                if (v1Part < v2Part) return -1;
            }
            
            return 0; // Versions are equal
        }

        function getVersionLabel(plugin) {
            if (!plugin.require_version) return '';
            
            const isCompatible = checkVersionCompatibility(plugin.require_version, cmsVersion);
            
            // Only show information if not compatible
            if (!isCompatible) {
                return `<span class="badge bg-danger" data-bs-toggle="tooltip" title="Requires CMS version ${plugin.require_version} or higher">Not Compatible</span>`;
            }
            return '';
        }

        function displayPlugins(plugins) {
            const grid = document.getElementById('plugin-grid');
            grid.innerHTML = '';
            document.getElementById('no-results').style.display = 'none';

            plugins.forEach(plugin => {
                const col = document.createElement('div');
                col.className = 'col';

                // Determine badge
                let badge = '';
                if (plugin.is_new) {
                    badge = '<span class="badge bg-success position-absolute top-0 end-0 m-2"><?php _e("New"); ?></span>';
                }

                // Choose icon background color
                const bgColors = ['primary', 'danger', 'success', 'warning', 'info'];
                const randomBg = bgColors[Math.floor(Math.random() * bgColors.length)];

                // Version compatibility badge
                const versionBadge = getVersionLabel(plugin);

                // Determine button state
                let buttonHtml = '';
                if (plugin.is_installed) {
                    buttonHtml = `
                    <button class="btn btn-sm btn-secondary" disabled>
                        <i class="fas fa-check-circle me-1"></i> <?php _e('Installed'); ?>
                    </button>`;
                } else if (plugin.is_premium) {
                    if (!plugin.is_compatible) {
                        buttonHtml = `
                        <button class="btn btn-sm btn-warning" disabled>
                            <i class="fas fa-exclamation-circle me-1"></i> <?php _e('Incompatible'); ?>
                        </button>`;
                    } else {
                        buttonHtml = `
                        <button class="btn btn-sm btn-warning install-premium-plugin" data-plugin="${plugin.dir_name}" data-url="${plugin.url}" data-version="${plugin.require_version}">
                            <i class="fas fa-crown me-1"></i> <?php _e('Get Premium'); ?>
                        </button>`;
                    }
                } else {
                    if (!plugin.is_compatible) {
                        buttonHtml = `
                        <button class="btn btn-sm btn-warning" disabled>
                            <i class="fas fa-exclamation-circle me-1"></i> <?php _e('Incompatible'); ?>
                        </button>`;
                    } else {
                        buttonHtml = `
                        <button class="btn btn-sm btn-primary install-plugin" data-plugin="${plugin.dir_name}" data-url="${plugin.url}" data-version="${plugin.require_version}">
                            <i class="fas fa-download me-1"></i> <?php _e('Install'); ?>
                        </button>`;
                    }
                }

                col.innerHTML = `
                <div class="card h-100 position-relative">
                    ${badge}
                    <div class="card-body d-flex flex-column">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="bg-${randomBg} bg-opacity-10 p-2 rounded text-${randomBg}">
                                        <i class="fas fa-puzzle-piece"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">${plugin.name}</h5>
                                    <p class="text-muted mb-0">by ${plugin.website ? `<a href="${plugin.website}" target="_blank">${plugin.author}</a>` : plugin.author}</p>
                                </div>
                            </div>
                            <p class="card-text">${plugin.description}</p>
                            <div>
                                ${versionBadge}
                                ${!plugin.is_compatible && plugin.require_version ? `<small class="text-muted">Requires v${plugin.require_version}+</small>` : ''}
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                            <div class="small text-muted">
                                <i class="fas fa-star text-warning"></i> ${plugin.rating || '4.5'} 
                                <span class="ms-2">${plugin.total_installations ? plugin.total_installations.toLocaleString() : '1,000'}+ <?php _e('active'); ?></span>
                            </div>
                            ${buttonHtml}
                        </div>
                    </div>
                </div>
            `;

                grid.appendChild(col);
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add install event listeners
            document.querySelectorAll('.install-plugin').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.hasAttribute('disabled')) return;
                    
                    const pluginSlug = this.getAttribute('data-plugin');
                    const pluginUrl = this.getAttribute('data-url');
                    const requiredVersion = this.getAttribute('data-version');

                    // Redirect to the plugin installation handler
                    window.location.href = 'request.php?action=pluginAction&reqversion=' + requiredVersion + '&url=' + encodeURIComponent(pluginUrl) + '&plugin_action=add_plugin&redirect=dashboard.php?viewpage=plugin';
                });
            });

            // Add premium plugin install event listeners
            document.querySelectorAll('.install-premium-plugin').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.hasAttribute('disabled')) return;
                    
                    const pluginSlug = this.getAttribute('data-plugin');

                    // Redirect to premium plugin store
                    window.open('https://store.cloudarcade.net/product/' + pluginSlug, '_blank');
                });
            });
        }

        function createPagination(currentPage, totalPages) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) {
                pagination.style.display = 'none';
                return;
            }

            pagination.style.display = 'flex';

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
            if (currentPage > 1) {
                prevLi.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    loadPlugins(getCurrentSort(), '', currentPage - 1, currentSearch);
                });
            }
            pagination.appendChild(prevLi);

            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, startPage + 4);

            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageLi.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    loadPlugins(getCurrentSort(), '', i, currentSearch);
                });
                pagination.appendChild(pageLi);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
            if (currentPage < totalPages) {
                nextLi.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    loadPlugins(getCurrentSort(), '', currentPage + 1, currentSearch);
                });
            }
            pagination.appendChild(nextLi);
        }

        function getCurrentSort() {
            switch (currentTab) {
                case 'popular':
                    return 'popular';
                case 'new':
                    return 'newest';
                default:
                    return 'popular';
            }
        }

        function showLoading() {
            document.getElementById('loading-plugins').style.display = 'block';
            document.getElementById('plugin-grid').innerHTML = '';
            document.getElementById('plugin-error').style.display = 'none';
            document.getElementById('no-results').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loading-plugins').style.display = 'none';
        }

        function showError() {
            document.getElementById('loading-plugins').style.display = 'none';
            document.getElementById('plugin-error').style.display = 'block';
            document.getElementById('plugin-grid').innerHTML = '';
            document.getElementById('no-results').style.display = 'none';
        }
    });
</script>

<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    #active-search-tag {
        display: inline-block;
    }

    #active-search-tag .badge {
        font-size: 14px;
    }

    .btn-close-white {
        font-size: 10px;
    }
</style>