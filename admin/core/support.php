<div class="row">
    <div class="col-lg-9">
        <div class="section">
            <h5 class="mb-4"><?php _e('Support Request') ?></h5>
            <div class="alert alert-info mb-4">
                <h6 class="mb-2">About Our Support System</h6>
                <p class="mb-3">We offer multiple support channels to serve you better. Here's how they work:</p>
                
                <strong>1. Built-in Support (Recommended)</strong>
                <ul class="mb-3">
                    <li>Fastest response time</li>
                    <li>Handled by both CloudArcade Support Team and developers</li>
                    <li>Priority support handling</li>
                </ul>

                <strong>2. Codecanyon Item Support</strong>
                <ul class="mb-3">
                    <li>Alternative support channel</li>
                    <li>Handled by developers only</li>
                    <li>Responses may take longer</li>
                </ul>

                <p class="mb-0"><small><strong>Note:</strong> Please use only one support method per issue. Multiple submissions of the same issue will not result in faster responses.</small></p>
            </div>
            <form id="supportForm">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="form-label"><?php _e('Your Email') ?></label>
                    <input type="email" class="form-control" id="email" name="email" maxlength="70" value="<?php echo !is_null($login_user->email) ? $login_user->email : '' ?>" required>
                </div>

                <!-- Request Type Selection -->
                <div class="mb-4">
                    <label for="requestType" class="form-label"><?php _e('Request Type') ?></label>
                    <select class="form-select" id="requestType" name="requestType" required>
                        <option value="technical" selected><?php _e('Technical Support') ?></option>
                        <option value="bug"><?php _e('Bug Report') ?></option>
                        <option value="suggestion"><?php _e('Feature Suggestion') ?></option>
                        <option value="other"><?php _e('Other') ?></option>
                    </select>
                </div>

                <!-- Main Support Message -->
                <div class="mb-4">
                    <label for="subject" class="form-label"><?php _e('Subject') ?></label>
                    <input type="text" class="form-control" id="subject" name="subject" autocomplete="off" maxlength="100" required>
                </div>
                
                <div class="mb-4">
                    <label for="message" class="form-label"><?php _e('Message') ?></label>
                    <textarea class="form-control" id="message" name="message" rows="6" maxlength="800" required></textarea>
                    <div class="form-text">
                        <span id="charCount">0</span>/800 <?php _e('characters') ?>
                    </div>
                </div>

                <!-- Optional Credentials Section -->
                <div class="mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="includeCredentials" name="includeCredentials">
                        <label class="form-check-label" for="includeCredentials">
                            <?php _e('Include Site Credentials (Optional)') ?>
                        </label>
                    </div>
                    
                    <div class="credentials-section d-none" id="credentialsSection">
                        <div class="alert alert-info mb-3">
                            <small>Providing your admin credentials allows our support team to investigate issues directly in your admin panel, significantly reducing resolution time. Your credentials are securely transmitted and only used for support purposes.</small>
                        </div>
                        <div class="p-3 border rounded">
                            <div class="mb-3">
                                <label for="siteUsername" class="form-label"><?php _e('Site Username') ?></label>
                                <input type="text" class="form-control" id="siteUsername" name="siteUsername">
                            </div>
                            <div class="mb-3">
                                <label for="sitePassword" class="form-label"><?php _e('Site Password') ?></label>
                                <input type="password" class="form-control" id="sitePassword" name="sitePassword">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optional FTP Section -->
                <div class="mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="includeFTP" name="includeFTP">
                        <label class="form-check-label" for="includeFTP">
                            <?php _e('Include FTP Information (Optional)') ?>
                        </label>
                    </div>
                    
                    <div class="ftp-section d-none" id="ftpSection">
                        <div class="alert alert-info mb-3">
                            <small>FTP access helps us assist with file-related issues, theme installations, or server configurations. This can drastically speed up problem resolution, especially for technical issues. All information is transmitted securely and used only for support purposes.</small>
                        </div>
                        <div class="p-3 border rounded">
                            <div class="mb-3">
                                <label for="ftpHost" class="form-label"><?php _e('FTP Host') ?></label>
                                <input type="text" class="form-control" id="ftpHost" name="ftpHost">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ftpUsername" class="form-label"><?php _e('FTP Username') ?></label>
                                    <input type="text" class="form-control" id="ftpUsername" name="ftpUsername">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ftpPassword" class="form-label"><?php _e('FTP Password') ?></label>
                                    <input type="password" class="form-control" id="ftpPassword" name="ftpPassword">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ftpPort" class="form-label"><?php _e('FTP Port') ?></label>
                                    <input type="number" class="form-control" id="ftpPort" name="ftpPort" value="21">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?php _e('Send Support Request') ?></button>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation and submission handler
const supportForm = document.getElementById('supportForm');
const emailInput = document.getElementById('email');
const subjectInput = document.getElementById('subject');
const messageInput = document.getElementById('message');

// Helper function to sanitize input
function sanitizeInput(input) {
    return input.replace(/[<>]/g, ''); // Basic XSS prevention
}

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to show error message
function showError(element, message) {
    const existingError = element.nextElementSibling?.classList.contains('invalid-feedback') 
        ? element.nextElementSibling 
        : null;
    
    if (existingError) {
        existingError.textContent = message;
    } else {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        element.parentNode.insertBefore(errorDiv, element.nextSibling);
    }
    element.classList.add('is-invalid');
}

// Helper function to clear error message
function clearError(element) {
    const errorDiv = element.nextElementSibling;
    if (errorDiv?.classList.contains('invalid-feedback')) {
        errorDiv.remove();
    }
    element.classList.remove('is-invalid');
}

// Form validation function
function validateForm() {
    let isValid = true;
    
    // Clear all previous errors
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Email validation
    const email = sanitizeInput(emailInput.value.trim());
    if (!email) {
        showError(emailInput, '<?php _e("Email is required") ?>');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError(emailInput, '<?php _e("Please enter a valid email address") ?>');
        isValid = false;
    }

    // Subject validation
    const subject = sanitizeInput(subjectInput.value.trim());
    if (!subject) {
        showError(subjectInput, '<?php _e("Subject is required") ?>');
        isValid = false;
    } else if (subject.length < 5) {
        showError(subjectInput, '<?php _e("Subject must be at least 5 characters long") ?>');
        isValid = false;
    }

    // Message validation
    const message = sanitizeInput(messageInput.value.trim());
    if (!message) {
        showError(messageInput, '<?php _e("Message is required") ?>');
        isValid = false;
    } else if (message.length < 10) {
        showError(messageInput, '<?php _e("Message must be at least 10 characters long") ?>');
        isValid = false;
    }

    // Validate credentials if included
    if (document.getElementById('includeCredentials').checked) {
        const username = document.getElementById('siteUsername').value.trim();
        const password = document.getElementById('sitePassword').value.trim();
        
        if (!username || !password) {
            if (!username) showError(document.getElementById('siteUsername'), '<?php _e("Username is required when credentials are included") ?>');
            if (!password) showError(document.getElementById('sitePassword'), '<?php _e("Password is required when credentials are included") ?>');
            isValid = false;
        }
    }

    // Validate FTP if included
    if (document.getElementById('includeFTP').checked) {
        const ftpHost = document.getElementById('ftpHost').value.trim();
        const ftpUsername = document.getElementById('ftpUsername').value.trim();
        const ftpPassword = document.getElementById('ftpPassword').value.trim();
        const ftpPort = document.getElementById('ftpPort').value.trim();

        if (!ftpHost) {
            showError(document.getElementById('ftpHost'), '<?php _e("FTP host is required when FTP information is included") ?>');
            isValid = false;
        }
        if (!ftpUsername) {
            showError(document.getElementById('ftpUsername'), '<?php _e("FTP username is required when FTP information is included") ?>');
            isValid = false;
        }
        if (!ftpPassword) {
            showError(document.getElementById('ftpPassword'), '<?php _e("FTP password is required when FTP information is included") ?>');
            isValid = false;
        }
        if (!ftpPort) {
            showError(document.getElementById('ftpPort'), '<?php _e("FTP port is required when FTP information is included") ?>');
            isValid = false;
        }
    }

    return isValid;
}

// Form submission handler with AJAX
supportForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!validateForm()) {
        return;
    }

    const submitButton = this.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    try {
        // Disable form during submission
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php _e('Sending...') ?>`;
        
        const formData = new FormData(this);
        formData.append('action', 'submit_support_request');

        const response = await fetch('includes/ajax-actions.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        // Get the raw text response first
        const responseText = await response.text();
        
        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('Server response:', responseText);
            throw new Error(`Server response is not valid JSON. Raw response: ${responseText}`);
        }

        if (data.status !== 'success') {
            // Handle specific error cases
            let errorMessage = data.message;
            switch(data.value) {
                case 'access_denied':
                    errorMessage = '<?php _e("You don\'t have permission to perform this action") ?>';
                    break;
                case 'invalid_token':
                    errorMessage = '<?php _e("Security validation failed. Please refresh the page and try again") ?>';
                    break;
                case 'invalid_email':
                    errorMessage = '<?php _e("Please enter a valid email address") ?>';
                    break;
                case 'missing_fields':
                    errorMessage = '<?php _e("Please fill in all required fields") ?>';
                    break;
                case 'message_too_long':
                    errorMessage = '<?php _e("Message is too long (maximum 800 characters)") ?>';
                    break;
                case 'connection_failed':
                    errorMessage = '<?php _e("Failed to connect to support server. Please try again later") ?>';
                    break;
                case 'email_failed':
                    errorMessage = '<?php _e("Failed to send support request. Please try again later") ?>';
                    break;
            }
            throw new Error(errorMessage);
        }

        // Show success message with ticket ID if provided
        const successMessage = data.value ? 
            `<?php _e("Support request sent successfully. Ticket ID:") ?> ${data.value}` :
            '<?php _e("Support request sent successfully") ?>';

        const successAlert = document.createElement('div');
        successAlert.className = 'alert alert-success mt-3';
        successAlert.role = 'alert';
        successAlert.textContent = successMessage;
        this.insertAdjacentElement('beforebegin', successAlert);

        // Clear form
        this.reset();
        document.getElementById('charCount').textContent = '0';
        
        // Reset optional sections
        document.getElementById('credentialsSection').classList.add('d-none');
        document.getElementById('ftpSection').classList.add('d-none');

        // Store ticket ID in localStorage for potential future reference
        if (data.value) {
            localStorage.setItem('lastSupportTicket', data.value);
        }

    } catch (error) {
        // Show error message
        const errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger mt-3';
        errorAlert.role = 'alert';
        errorAlert.textContent = error.message;
        this.insertAdjacentElement('beforebegin', errorAlert);

        // Log the error to console for debugging
        console.error('Form submission error:', error);

    } finally {
        // Re-enable form
        //submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
});

// Real-time validation for email
emailInput.addEventListener('blur', function() {
    const email = this.value.trim();
    if (email && !isValidEmail(email)) {
        showError(this, '<?php _e("Please enter a valid email address") ?>');
    } else {
        clearError(this);
    }
});

// Character counter for textarea
messageInput.addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Toggle sections
document.getElementById('includeCredentials').addEventListener('change', function() {
    document.getElementById('credentialsSection').classList.toggle('d-none', !this.checked);
});

document.getElementById('includeFTP').addEventListener('change', function() {
    document.getElementById('ftpSection').classList.toggle('d-none', !this.checked);
});
</script>