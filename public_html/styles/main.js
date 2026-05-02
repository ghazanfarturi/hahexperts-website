// HAH Healthcare Experts - Main JavaScript

// Contact Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get form data
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone')?.value || '';
            const facility = document.getElementById('facility')?.value || '';
            const service = document.getElementById('service').value;
            const message = document.getElementById('message').value;
            
            // Basic validation
            if (!name || !email || !service || !message) {
                showFormStatus('Please fill in all required fields', 'error');
                return;
            }
            
            // Email validation
            if (!validateEmail(email)) {
                showFormStatus('Please enter a valid email address', 'error');
                return;
            }
            
            // Get submit button and show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            try {
                // Send to PHP backend
                const formData = new FormData();
                formData.append('name', name);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('facility', facility);
                formData.append('service', service);
                formData.append('message', message);
                
                const response = await fetch('send-email.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showFormStatus(result.message, 'success');
                    contactForm.reset();
                } else {
                    showFormStatus(result.message || 'An error occurred. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showFormStatus('Sorry, there was an error sending your message. Please try again or email us directly at info@hahexperts.com', 'error');
            } finally {
                // Restore button state
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    }
});

// Show form status message
function showFormStatus(message, type) {
    const statusDiv = document.getElementById('form-status');
    if (statusDiv) {
        statusDiv.textContent = message;
        statusDiv.className = 'form-status ' + type;
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                statusDiv.className = 'form-status';
                statusDiv.textContent = '';
            }, 5000);
        }
    }
}

// Mobile menu toggle (if implemented)
function toggleMobileMenu() {
    const navMenu = document.querySelector('.nav-menu');
    if (navMenu) {
        navMenu.classList.toggle('active');
    }
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add active class to current nav item based on URL
document.addEventListener('DOMContentLoaded', function() {
    const currentLocation = location.pathname.split('/').pop() || 'index.html';
    const navItems = document.querySelectorAll('.nav-menu a');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href === currentLocation || (href === 'index.html' && currentLocation === '')) {
            item.classList.add('active');
        }
    });
});

// Analytics tracking (placeholder for future integration)
function trackPageView(pageName) {
    console.log('Page view tracked:', pageName);
    // Implementation for Google Analytics or other tracking
}

// Form validation helper
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Utility function to add loading state to buttons
function setButtonLoading(button, isLoading) {
    if (isLoading) {
        button.disabled = true;
        button.textContent = 'Sending...';
    } else {
        button.disabled = false;
        button.textContent = 'Send Message';
    }
}

// Initialize tooltips (if needed)
function initializeTooltips() {
    // Placeholder for tooltip initialization
    console.log('Tooltips initialized');
}

// Export functions for use in other scripts
window.HAHExperts = {
    toggleMobileMenu,
    trackPageView,
    validateEmail,
    setButtonLoading,
    showFormStatus
};
