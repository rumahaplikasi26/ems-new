/**
 * Tour Guide Implementation using @sjmc11/tourguidejs
 * Dashboard Tour Guide for EMS Application
 */

// TourGuide will be loaded via CDN

class EMSTourGuide {
    constructor() {
        this.tourGuide = null;
        this.currentStep = 0;
        this.tourSteps = [];
        this.isActive = false;
        
        this.init();
    }

    init() {
        // Wait for TourGuide to be available
        if (typeof TourGuide === 'undefined') {
            console.error('TourGuide library not loaded');
            return;
        }

        // Initialize TourGuide
        this.tourGuide = new TourGuide({
            // Tour guide options
            backdrop: true,
            backdropColor: 'rgba(0, 0, 0, 0.5)',
            backdropPadding: 10,
            allowKeyboard: true,
            allowPageScrolling: true,
            allowTourGuideScrolling: true,
            autoScroll: true,
            autoScrollOffset: 20,
            autoScrollBehavior: 'smooth',
            closeButton: true,
            debug: false,
            exitOnEscape: true,
            exitOnOverlayClick: true,
            highlightPadding: 4,
            nextLabel: this.getTranslation('next'),
            prevLabel: this.getTranslation('previous'),
            finishLabel: this.getTranslation('finish'),
            skipLabel: this.getTranslation('skip'),
            showProgress: true,
            showStepNumbers: true,
            stepNumberPosition: 'bottom-right',
            theme: 'light',
            useKeyboardNavigation: true,
            zIndex: 9999,
            
            // Custom styling
            customStyles: {
                '--tg-primary': '#007bff',
                '--tg-secondary': '#6c757d',
                '--tg-success': '#28a745',
                '--tg-danger': '#dc3545',
                '--tg-warning': '#ffc107',
                '--tg-info': '#17a2b8',
                '--tg-light': '#f8f9fa',
                '--tg-dark': '#343a40',
                '--tg-white': '#ffffff',
                '--tg-black': '#000000',
                '--tg-border-radius': '8px',
                '--tg-box-shadow': '0 4px 6px rgba(0, 0, 0, 0.1)',
                '--tg-font-family': 'inherit',
                '--tg-font-size': '14px',
                '--tg-line-height': '1.5',
                '--tg-padding': '20px',
                '--tg-margin': '10px',
            }
        });

        // Define tour steps
        this.defineTourSteps();
        
        // Bind events
        this.bindEvents();
    }

    defineTourSteps() {
        this.tourSteps = [
            {
                target: '[data-tour="welcome"]',
                title: this.getTranslation('tour_welcome_title'),
                content: this.getTranslation('tour_welcome_content'),
                placement: 'bottom',
                order: 1
            },
            {
                target: '[data-tour="dashboard-profile"]',
                title: this.getTranslation('tour_dashboard_profile_title'),
                content: this.getTranslation('tour_dashboard_profile_content'),
                placement: 'right',
                order: 2
            },
            {
                target: '[data-tour="working-hours-analytic"]',
                title: this.getTranslation('tour_working_hours_title'),
                content: this.getTranslation('tour_working_hours_content'),
                placement: 'left',
                order: 3
            },
            {
                target: '[data-tour="working-day-analytic"]',
                title: this.getTranslation('tour_working_day_title'),
                content: this.getTranslation('tour_working_day_content'),
                placement: 'right',
                order: 4
            },
            {
                target: '[data-tour="working-hours-table"]',
                title: this.getTranslation('tour_working_hours_table_title'),
                content: this.getTranslation('tour_working_hours_table_content'),
                placement: 'top',
                order: 5
            },
            {
                target: '[data-tour="activity-card"]',
                title: this.getTranslation('tour_activity_card_title'),
                content: this.getTranslation('tour_activity_card_content'),
                placement: 'left',
                order: 6
            },
            {
                target: '[data-tour="sidebar"]',
                title: this.getTranslation('tour_sidebar_title'),
                content: this.getTranslation('tour_sidebar_content'),
                placement: 'right',
                order: 7
            },
            {
                target: '[data-tour="header"]',
                title: this.getTranslation('tour_header_title'),
                content: this.getTranslation('tour_header_content'),
                placement: 'bottom',
                order: 8
            }
        ];
    }

    bindEvents() {
        // Start tour button
        const startTourBtn = document.getElementById('start-tour');
        if (startTourBtn) {
            startTourBtn.addEventListener('click', () => {
                this.startTour();
            });
        }

        // Tour guide events
        this.tourGuide.on('start', () => {
            this.isActive = true;
            this.updateStartTourButton();
            console.log('Tour started');
        });

        this.tourGuide.on('step', (step) => {
            this.currentStep = step.order;
            console.log('Tour step:', step.order);
        });

        this.tourGuide.on('finish', () => {
            this.isActive = false;
            this.currentStep = 0;
            this.updateStartTourButton();
            console.log('Tour finished');
        });

        this.tourGuide.on('skip', () => {
            this.isActive = false;
            this.currentStep = 0;
            this.updateStartTourButton();
            console.log('Tour skipped');
        });

        this.tourGuide.on('error', (error) => {
            console.error('Tour error:', error);
            this.isActive = false;
            this.updateStartTourButton();
        });
    }

    startTour() {
        if (this.isActive) {
            this.tourGuide.finish();
            return;
        }

        // Check if we're on dashboard page
        if (!this.isDashboardPage()) {
            this.showError(this.getTranslation('tour_dashboard_only'));
            return;
        }

        // Validate tour elements exist
        const missingElements = this.validateTourElements();
        if (missingElements.length > 0) {
            console.warn('Missing tour elements:', missingElements);
            this.showError(this.getTranslation('tour_elements_missing'));
            return;
        }

        // Start the tour
        this.tourGuide.startTour(this.tourSteps);
    }

    isDashboardPage() {
        const currentPath = window.location.pathname;
        return currentPath === '/' || currentPath === '/dashboard' || currentPath.includes('dashboard');
    }

    validateTourElements() {
        const missingElements = [];
        
        this.tourSteps.forEach(step => {
            const element = document.querySelector(step.target);
            if (!element) {
                missingElements.push(step.target);
            }
        });

        return missingElements;
    }

    updateStartTourButton() {
        const startTourBtn = document.getElementById('start-tour');
        if (!startTourBtn) return;

        const btnContent = startTourBtn.querySelector('.btn-content-start-tour');
        const tourText = startTourBtn.querySelector('.start-tour-text');
        
        if (this.isActive) {
            btnContent.innerHTML = `
                <i class='bx bx-stop'></i>
                <span class="start-tour-text">${this.getTranslation('stop_tour')}</span>
            `;
            startTourBtn.classList.add('active');
        } else {
            btnContent.innerHTML = `
                <i class='bx bx-play'></i>
                <span class="start-tour-text">${this.getTranslation('start_tour')}</span>
            `;
            startTourBtn.classList.remove('active');
        }
    }

    showError(message) {
        // You can implement a toast notification here
        alert(message);
    }

    getTranslation(key) {
        // Get translation from Laravel
        const translations = window.Laravel?.translations || {};
        return translations[key] || key;
    }

    // Public methods
    finish() {
        if (this.tourGuide) {
            this.tourGuide.finish();
        }
    }

    skip() {
        if (this.tourGuide) {
            this.tourGuide.skip();
        }
    }

    next() {
        if (this.tourGuide) {
            this.tourGuide.next();
        }
    }

    previous() {
        if (this.tourGuide) {
            this.tourGuide.previous();
        }
    }

    // Destroy tour guide
    destroy() {
        if (this.tourGuide) {
            this.tourGuide.destroy();
            this.tourGuide = null;
        }
    }
}

// Initialize tour guide when DOM and TourGuide are ready
function initializeTourGuide() {
    // Only initialize on dashboard page
    if (window.location.pathname === '/' || 
        window.location.pathname === '/dashboard' || 
        window.location.pathname.includes('dashboard')) {
        
        // Check if TourGuide is available
        if (typeof TourGuide !== 'undefined') {
            window.EMSTourGuide = new EMSTourGuide();
        } else {
            // Retry after a short delay
            setTimeout(initializeTourGuide, 100);
        }
    }
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure TourGuide CDN is loaded
    setTimeout(initializeTourGuide, 200);
});

// Export for global access
window.EMSTourGuide = window.EMSTourGuide || null;
