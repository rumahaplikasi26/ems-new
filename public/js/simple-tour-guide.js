/**
 * Simple Tour Guide Implementation
 * Universal Tour Guide for EMS Application
 */

class SimpleTourGuide {
    constructor() {
        this.currentStep = 0;
        this.isActive = false;
        this.tourSteps = [];
        this.overlay = null;
        this.tooltip = null;
        this.currentPage = this.detectCurrentPage();
        
        this.init();
    }

    init() {
        console.log('Initializing tour guide for page:', this.currentPage);
        this.defineTourSteps();
        this.bindEvents();
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.createOverlay();
                this.createTooltip();
            });
        } else {
            this.createOverlay();
            this.createTooltip();
        }
    }

    detectCurrentPage() {
        const path = window.location.pathname;
        const segments = path.split('/').filter(segment => segment);
        
        // Dashboard
        if (path === '/' || path === '/dashboard' || segments.includes('dashboard')) {
            return 'dashboard';
        }
        
        // Employee Management
        if (segments.includes('employee')) {
            return 'employee';
        }
        
        // Attendance Management
        if (segments.includes('attendance') && !segments.includes('temp')) {
            return 'attendance';
        }
        
        // Attendance Temp Management
        if (segments.includes('attendance-temp')) {
            return 'attendance-temp';
        }
        
        // Leave Request Management
        if (segments.includes('leave-request')) {
            return 'leave-request';
        }
        
        // Financial Request Management
        if (segments.includes('financial-request')) {
            return 'financial-request';
        }
        
        // Daily Report Management
        if (segments.includes('daily-report')) {
            return 'daily-report';
        }
        
        // Announcement Management
        if (segments.includes('announcement')) {
            return 'announcement';
        }
        
        // Department Management
        if (segments.includes('department')) {
            return 'department';
        }
        
        // Position Management
        if (segments.includes('position')) {
            return 'position';
        }
        
        // Role Management
        if (segments.includes('role')) {
            return 'role';
        }
        
        // Site Management
        if (segments.includes('site')) {
            return 'site';
        }
        
        // Machine Management
        if (segments.includes('machine')) {
            return 'machine';
        }
        
        // Project Management
        if (segments.includes('project')) {
            return 'project';
        }
        
        // Visit Management
        if (segments.includes('visit')) {
            return 'visit';
        }
        
        // Profile Management
        if (segments.includes('profile')) {
            return 'profile';
        }
        
        // Settings Management
        if (segments.includes('setting')) {
            return 'setting';
        }
        
        // Import Master Data
        if (segments.includes('import-master-data')) {
            return 'import-master-data';
        }
        
        // Email Template Manager
        if (segments.includes('email-template-manager')) {
            return 'email-template-manager';
        }
        
        // Reports
        if (segments.includes('report')) {
            return 'report';
        }
        
        // Overtime Request Management
        if (segments.includes('overtime-request')) {
            return 'overtime-request';
        }
        
        // Absent Request Management
        if (segments.includes('absent-request')) {
            return 'absent-request';
        }
        
        // Activity Management
        if (segments.includes('activity')) {
            return 'activity';
        }
        
        // Default to dashboard
        return 'dashboard';
    }

    defineTourSteps() {
        this.tourSteps = this.getTourStepsForPage(this.currentPage);
    }

    getTourStepsForPage(page) {
        const commonSteps = [
            {
                target: '[data-tour="sidebar"]',
                title: this.getTranslation('tour_sidebar_title'),
                content: this.getTranslation('tour_sidebar_content'),
                placement: 'right',
                order: 1
            },
            {
                target: '[data-tour="header"]',
                title: this.getTranslation('tour_header_title'),
                content: this.getTranslation('tour_header_content'),
                placement: 'bottom',
                order: 2
            }
        ];

        switch (page) {
            case 'dashboard':
                return [
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
                    ...commonSteps
                ];

            case 'employee':
                return [
                    {
                        target: '[data-tour="employee-search"]',
                        title: this.getTranslation('tour_employee_search_title'),
                        content: this.getTranslation('tour_employee_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="employee-filter"]',
                        title: this.getTranslation('tour_employee_filter_title'),
                        content: this.getTranslation('tour_employee_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="employee-table"]',
                        title: this.getTranslation('tour_employee_table_title'),
                        content: this.getTranslation('tour_employee_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="employee-actions"]',
                        title: this.getTranslation('tour_employee_actions_title'),
                        content: this.getTranslation('tour_employee_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'attendance':
                return [
                    {
                        target: '[data-tour="attendance-search"]',
                        title: this.getTranslation('tour_attendance_search_title'),
                        content: this.getTranslation('tour_attendance_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="attendance-date"]',
                        title: this.getTranslation('tour_attendance_date_title'),
                        content: this.getTranslation('tour_attendance_date_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="attendance-table"]',
                        title: this.getTranslation('tour_attendance_table_title'),
                        content: this.getTranslation('tour_attendance_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="attendance-create"]',
                        title: this.getTranslation('tour_attendance_create_title'),
                        content: this.getTranslation('tour_attendance_create_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'leave-request':
                return [
                    {
                        target: '[data-tour="leave-search"]',
                        title: this.getTranslation('tour_leave_search_title'),
                        content: this.getTranslation('tour_leave_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="leave-filter"]',
                        title: this.getTranslation('tour_leave_filter_title'),
                        content: this.getTranslation('tour_leave_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="leave-table"]',
                        title: this.getTranslation('tour_leave_table_title'),
                        content: this.getTranslation('tour_leave_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="leave-actions"]',
                        title: this.getTranslation('tour_leave_actions_title'),
                        content: this.getTranslation('tour_leave_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'financial-request':
                return [
                    {
                        target: '[data-tour="financial-search"]',
                        title: this.getTranslation('tour_financial_search_title'),
                        content: this.getTranslation('tour_financial_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="financial-filter"]',
                        title: this.getTranslation('tour_financial_filter_title'),
                        content: this.getTranslation('tour_financial_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="financial-table"]',
                        title: this.getTranslation('tour_financial_table_title'),
                        content: this.getTranslation('tour_financial_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="financial-actions"]',
                        title: this.getTranslation('tour_financial_actions_title'),
                        content: this.getTranslation('tour_financial_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'daily-report':
                return [
                    {
                        target: '[data-tour="daily-report-search"]',
                        title: this.getTranslation('tour_daily_report_search_title'),
                        content: this.getTranslation('tour_daily_report_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="daily-report-filter"]',
                        title: this.getTranslation('tour_daily_report_filter_title'),
                        content: this.getTranslation('tour_daily_report_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="daily-report-table"]',
                        title: this.getTranslation('tour_daily_report_table_title'),
                        content: this.getTranslation('tour_daily_report_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="daily-report-actions"]',
                        title: this.getTranslation('tour_daily_report_actions_title'),
                        content: this.getTranslation('tour_daily_report_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'announcement':
                return [
                    {
                        target: '[data-tour="announcement-search"]',
                        title: this.getTranslation('tour_announcement_search_title'),
                        content: this.getTranslation('tour_announcement_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="announcement-filter"]',
                        title: this.getTranslation('tour_announcement_filter_title'),
                        content: this.getTranslation('tour_announcement_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="announcement-table"]',
                        title: this.getTranslation('tour_announcement_table_title'),
                        content: this.getTranslation('tour_announcement_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="announcement-actions"]',
                        title: this.getTranslation('tour_announcement_actions_title'),
                        content: this.getTranslation('tour_announcement_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'department':
                return [
                    {
                        target: '[data-tour="department-search"]',
                        title: this.getTranslation('tour_department_search_title'),
                        content: this.getTranslation('tour_department_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="department-table"]',
                        title: this.getTranslation('tour_department_table_title'),
                        content: this.getTranslation('tour_department_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="department-actions"]',
                        title: this.getTranslation('tour_department_actions_title'),
                        content: this.getTranslation('tour_department_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'position':
                return [
                    {
                        target: '[data-tour="position-search"]',
                        title: this.getTranslation('tour_position_search_title'),
                        content: this.getTranslation('tour_position_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="position-table"]',
                        title: this.getTranslation('tour_position_table_title'),
                        content: this.getTranslation('tour_position_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="position-actions"]',
                        title: this.getTranslation('tour_position_actions_title'),
                        content: this.getTranslation('tour_position_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'role':
                return [
                    {
                        target: '[data-tour="role-search"]',
                        title: this.getTranslation('tour_role_search_title'),
                        content: this.getTranslation('tour_role_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="role-table"]',
                        title: this.getTranslation('tour_role_table_title'),
                        content: this.getTranslation('tour_role_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="role-actions"]',
                        title: this.getTranslation('tour_role_actions_title'),
                        content: this.getTranslation('tour_role_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'site':
                return [
                    {
                        target: '[data-tour="site-search"]',
                        title: this.getTranslation('tour_site_search_title'),
                        content: this.getTranslation('tour_site_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="site-table"]',
                        title: this.getTranslation('tour_site_table_title'),
                        content: this.getTranslation('tour_site_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="site-actions"]',
                        title: this.getTranslation('tour_site_actions_title'),
                        content: this.getTranslation('tour_site_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'machine':
                return [
                    {
                        target: '[data-tour="machine-search"]',
                        title: this.getTranslation('tour_machine_search_title'),
                        content: this.getTranslation('tour_machine_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="machine-table"]',
                        title: this.getTranslation('tour_machine_table_title'),
                        content: this.getTranslation('tour_machine_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="machine-actions"]',
                        title: this.getTranslation('tour_machine_actions_title'),
                        content: this.getTranslation('tour_machine_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'project':
                return [
                    {
                        target: '[data-tour="project-search"]',
                        title: this.getTranslation('tour_project_search_title'),
                        content: this.getTranslation('tour_project_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="project-table"]',
                        title: this.getTranslation('tour_project_table_title'),
                        content: this.getTranslation('tour_project_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="project-actions"]',
                        title: this.getTranslation('tour_project_actions_title'),
                        content: this.getTranslation('tour_project_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'visit':
                return [
                    {
                        target: '[data-tour="visit-search"]',
                        title: this.getTranslation('tour_visit_search_title'),
                        content: this.getTranslation('tour_visit_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="visit-table"]',
                        title: this.getTranslation('tour_visit_table_title'),
                        content: this.getTranslation('tour_visit_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="visit-actions"]',
                        title: this.getTranslation('tour_visit_actions_title'),
                        content: this.getTranslation('tour_visit_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'profile':
                return [
                    {
                        target: '[data-tour="profile-form"]',
                        title: this.getTranslation('tour_profile_form_title'),
                        content: this.getTranslation('tour_profile_form_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="profile-avatar"]',
                        title: this.getTranslation('tour_profile_avatar_title'),
                        content: this.getTranslation('tour_profile_avatar_content'),
                        placement: 'right',
                        order: 2
                    },
                    {
                        target: '[data-tour="profile-save"]',
                        title: this.getTranslation('tour_profile_save_title'),
                        content: this.getTranslation('tour_profile_save_content'),
                        placement: 'top',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'setting':
                return [
                    {
                        target: '[data-tour="settings-form"]',
                        title: this.getTranslation('tour_settings_form_title'),
                        content: this.getTranslation('tour_settings_form_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="settings-save"]',
                        title: this.getTranslation('tour_settings_save_title'),
                        content: this.getTranslation('tour_settings_save_content'),
                        placement: 'top',
                        order: 2
                    },
                    ...commonSteps
                ];

            case 'import-master-data':
                return [
                    {
                        target: '[data-tour="import-file"]',
                        title: this.getTranslation('tour_import_file_title'),
                        content: this.getTranslation('tour_import_file_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="import-preview"]',
                        title: this.getTranslation('tour_import_preview_title'),
                        content: this.getTranslation('tour_import_preview_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="import-process"]',
                        title: this.getTranslation('tour_import_process_title'),
                        content: this.getTranslation('tour_import_process_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'email-template-manager':
                return [
                    {
                        target: '[data-tour="email-template-search"]',
                        title: this.getTranslation('tour_email_template_search_title'),
                        content: this.getTranslation('tour_email_template_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="email-template-table"]',
                        title: this.getTranslation('tour_email_template_table_title'),
                        content: this.getTranslation('tour_email_template_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="email-template-actions"]',
                        title: this.getTranslation('tour_email_template_actions_title'),
                        content: this.getTranslation('tour_email_template_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'report':
                return [
                    {
                        target: '[data-tour="report-filter"]',
                        title: this.getTranslation('tour_report_filter_title'),
                        content: this.getTranslation('tour_report_filter_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="report-table"]',
                        title: this.getTranslation('tour_report_table_title'),
                        content: this.getTranslation('tour_report_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="report-export"]',
                        title: this.getTranslation('tour_report_export_title'),
                        content: this.getTranslation('tour_report_export_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'overtime-request':
                return [
                    {
                        target: '[data-tour="overtime-search"]',
                        title: this.getTranslation('tour_overtime_search_title'),
                        content: this.getTranslation('tour_overtime_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="overtime-filter"]',
                        title: this.getTranslation('tour_overtime_filter_title'),
                        content: this.getTranslation('tour_overtime_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="overtime-table"]',
                        title: this.getTranslation('tour_overtime_table_title'),
                        content: this.getTranslation('tour_overtime_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="overtime-actions"]',
                        title: this.getTranslation('tour_overtime_actions_title'),
                        content: this.getTranslation('tour_overtime_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'absent-request':
                return [
                    {
                        target: '[data-tour="absent-search"]',
                        title: this.getTranslation('tour_absent_search_title'),
                        content: this.getTranslation('tour_absent_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="absent-filter"]',
                        title: this.getTranslation('tour_absent_filter_title'),
                        content: this.getTranslation('tour_absent_filter_content'),
                        placement: 'bottom',
                        order: 2
                    },
                    {
                        target: '[data-tour="absent-table"]',
                        title: this.getTranslation('tour_absent_table_title'),
                        content: this.getTranslation('tour_absent_table_content'),
                        placement: 'top',
                        order: 3
                    },
                    {
                        target: '[data-tour="absent-actions"]',
                        title: this.getTranslation('tour_absent_actions_title'),
                        content: this.getTranslation('tour_absent_actions_content'),
                        placement: 'left',
                        order: 4
                    },
                    ...commonSteps
                ];

            case 'activity':
                return [
                    {
                        target: '[data-tour="activity-search"]',
                        title: this.getTranslation('tour_activity_search_title'),
                        content: this.getTranslation('tour_activity_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="activity-table"]',
                        title: this.getTranslation('tour_activity_table_title'),
                        content: this.getTranslation('tour_activity_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="activity-actions"]',
                        title: this.getTranslation('tour_activity_actions_title'),
                        content: this.getTranslation('tour_activity_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            case 'attendance-temp':
                return [
                    {
                        target: '[data-tour="attendance-temp-search"]',
                        title: this.getTranslation('tour_attendance_temp_search_title'),
                        content: this.getTranslation('tour_attendance_temp_search_content'),
                        placement: 'bottom',
                        order: 1
                    },
                    {
                        target: '[data-tour="attendance-temp-table"]',
                        title: this.getTranslation('tour_attendance_temp_table_title'),
                        content: this.getTranslation('tour_attendance_temp_table_content'),
                        placement: 'top',
                        order: 2
                    },
                    {
                        target: '[data-tour="attendance-temp-actions"]',
                        title: this.getTranslation('tour_attendance_temp_actions_title'),
                        content: this.getTranslation('tour_attendance_temp_actions_content'),
                        placement: 'left',
                        order: 3
                    },
                    ...commonSteps
                ];

            default:
                return commonSteps;
        }
    }

    bindEvents() {
        // Bind start tour button
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'start-tour') {
                e.preventDefault();
                this.startTour();
            }
        });

        // Bind keyboard events
        document.addEventListener('keydown', (e) => {
            if (!this.isActive) return;

            switch (e.key) {
                case 'Escape':
                    this.skip();
                    break;
                case 'ArrowRight':
                case 'Enter':
                    e.preventDefault();
                    this.nextStep();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.prevStep();
                    break;
            }
        });
    }

    createOverlay() {
        if (this.overlay) return;

        this.overlay = document.createElement('div');
        this.overlay.id = 'tour-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        if (document.body) {
            document.body.appendChild(this.overlay);
        }
    }

    createTooltip() {
        if (this.tooltip) return;

        this.tooltip = document.createElement('div');
        this.tooltip.id = 'tour-tooltip';
        this.tooltip.style.cssText = `
            position: fixed;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid #e0e0e0;
            z-index: 1050;
            max-width: 350px;
            min-width: 300px;
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.3s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        `;

        if (document.body) {
            document.body.appendChild(this.tooltip);
        }
    }

    startTour() {
        console.log('Starting tour for page:', this.currentPage);
        this.cleanupAllTourGuideStyles();
        
        if (this.tourSteps.length === 0) {
            this.showError('No tour steps available for this page.');
            return;
        }

        this.isActive = true;
        this.currentStep = 0;
        this.updateStartTourButton();
        this.showStep();
    }

    showStep() {
        if (this.currentStep >= this.tourSteps.length) {
            this.finish();
            return;
        }

        const step = this.tourSteps[this.currentStep];
        const element = document.querySelector(step.target);

        if (!element) {
            console.warn(`Element not found: ${step.target}`);
            this.nextStep();
            return;
        }

        this.highlightElement(element);
        this.showTooltip(step, element);
    }

    highlightElement(element) {
        // Store original styles
        if (!element.dataset.originalStyles) {
            element.dataset.originalStyles = element.style.cssText;
        }

        // Check if element is sidebar or header to avoid conflicts
        const isSidebar = element.closest('[data-tour="sidebar"]') || element.hasAttribute('data-tour') && element.getAttribute('data-tour') === 'sidebar';
        const isHeader = element.closest('[data-tour="header"]') || element.hasAttribute('data-tour') && element.getAttribute('data-tour') === 'header';
        
        if (isSidebar || isHeader) {
            // For sidebar and header, use different highlighting approach
            element.style.position = 'relative';
            element.style.zIndex = '1045';
            element.style.borderRadius = '8px';
            element.style.boxShadow = '0 0 0 3px rgba(0, 123, 255, 0.4), 0 0 15px rgba(0, 123, 255, 0.3)';
            element.style.transition = 'all 0.3s ease';
            element.style.transform = 'scale(1.01)';
        } else {
            // Apply normal highlight styles for other elements
            element.style.position = 'relative';
            element.style.zIndex = '1045';
            element.style.borderRadius = '8px';
            element.style.boxShadow = '0 0 0 4px rgba(0, 123, 255, 0.3), 0 0 20px rgba(0, 123, 255, 0.2)';
            element.style.transition = 'all 0.3s ease';
            element.style.transform = 'scale(1.02)';
        }
        
        // Add class for identification
        element.classList.add('tour-guide-highlight');
        
        // Focus element
        if (element.focus) {
            element.focus();
        }
    }

    removeHighlight(element) {
        if (!element) return;
        
        element.classList.remove('tour-guide-highlight');
        this.removeTourGuideStyles(element);
    }

    removeTourGuideStyles(element) {
        const originalStyles = element.dataset.originalStyles || '';
        
        element.style.position = '';
        element.style.zIndex = '';
        element.style.borderRadius = '';
        element.style.boxShadow = '';
        element.style.transition = '';
        element.style.transform = '';
        
        let cssText = element.style.cssText;
        
        cssText = cssText.replace(/position:\s*relative[^;]*;?/gi, '');
        cssText = cssText.replace(/z-index:\s*1045[^;]*;?/gi, '');
        cssText = cssText.replace(/z-index:\s*9999[^;]*;?/gi, '');
        cssText = cssText.replace(/border-radius:\s*8px[^;]*;?/gi, '');
        cssText = cssText.replace(/box-shadow:\s*[^;]*rgba\(0,\s*123,\s*255[^;]*;?/gi, '');
        cssText = cssText.replace(/transition:\s*all\s*0\.3s\s*ease[^;]*;?/gi, '');
        cssText = cssText.replace(/transform:\s*scale\(1\.02\)[^;]*;?/gi, '');
        cssText = cssText.replace(/transform:\s*scale\(1\.01\)[^;]*;?/gi, '');
        
        cssText = cssText.replace(/;\s*;/g, ';');
        cssText = cssText.replace(/^\s*;\s*/, '');
        cssText = cssText.replace(/;\s*$/, '');
        cssText = cssText.trim();
        
        if (cssText) {
            element.style.cssText = cssText;
        } else {
            if (originalStyles) {
                element.style.cssText = originalStyles;
            } else {
                element.removeAttribute('style');
            }
        }
        
        delete element.dataset.originalStyles;
    }

    cleanupAllTourGuideStyles() {
        const allElements = document.querySelectorAll('*');
        
        allElements.forEach(element => {
            const style = element.style;
            const cssText = style.cssText;
            
            if (cssText.includes('rgba(0, 123, 255') || 
                cssText.includes('z-index: 1045') ||
                cssText.includes('z-index: 9999') ||
                cssText.includes('transform: scale(1.02)') ||
                cssText.includes('transform: scale(1.01)') ||
                cssText.includes('box-shadow: 0 0 0 4px') ||
                cssText.includes('box-shadow: 0 0 0 3px') ||
                cssText.includes('box-shadow: 0 0 20px') ||
                cssText.includes('box-shadow: 0 0 15px')) {
                
                console.log('Found element with tour guide styles:', element);
                this.removeTourGuideStyles(element);
            }
        });
    }

    showTooltip(step, element) {
        if (!this.tooltip) return;

        this.tooltip.innerHTML = `
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #333;">${step.title}</h3>
                    <button id="tour-close" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #999; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">&times;</button>
                </div>
                <p style="margin: 0 0 16px 0; color: #666; line-height: 1.5; font-size: 14px;">${step.content}</p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 8px;">
                        <button id="tour-prev" style="padding: 8px 16px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; font-size: 14px; color: #666;" ${this.currentStep === 0 ? 'disabled' : ''}>${this.getTranslation('previous')}</button>
                        <button id="tour-next" style="padding: 8px 16px; border: none; background: #007bff; color: white; border-radius: 4px; cursor: pointer; font-size: 14px;">${this.currentStep === this.tourSteps.length - 1 ? this.getTranslation('finish') : this.getTranslation('next')}</button>
                    </div>
                    <button id="tour-skip" style="background: none; border: none; color: #999; cursor: pointer; font-size: 14px; text-decoration: underline;">${this.getTranslation('skip')}</button>
                </div>
                <div style="margin-top: 12px; text-align: center; font-size: 12px; color: #999;">
                    ${this.currentStep + 1} / ${this.tourSteps.length}
                </div>
            </div>
        `;

        // Bind tooltip events
        this.bindTooltipEvents();
        
        // Position and show tooltip
        this.positionTooltip(step, element);
        this.tooltip.style.opacity = '1';
        this.tooltip.style.transform = 'scale(1)';
        
        // Focus tooltip
        this.tooltip.focus();
        this.tooltip.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    bindTooltipEvents() {
        if (!this.tooltip) return;

        const closeBtn = this.tooltip.querySelector('#tour-close');
        const prevBtn = this.tooltip.querySelector('#tour-prev');
        const nextBtn = this.tooltip.querySelector('#tour-next');
        const skipBtn = this.tooltip.querySelector('#tour-skip');

        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.skip());
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prevStep());
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextStep());
        }

        if (skipBtn) {
            skipBtn.addEventListener('click', () => this.skip());
        }
    }

    positionTooltip(step, element) {
        if (!this.tooltip || !element) return;

        const rect = element.getBoundingClientRect();
        const tooltipRect = this.tooltip.getBoundingClientRect();
        const viewport = {
            width: window.innerWidth,
            height: window.innerHeight
        };

        let top, left;

        switch (step.placement) {
            case 'top':
                top = rect.top - tooltipRect.height - 20;
                left = rect.left + (rect.width - tooltipRect.width) / 2;
                break;
            case 'bottom':
                top = rect.bottom + 20;
                left = rect.left + (rect.width - tooltipRect.width) / 2;
                break;
            case 'left':
                top = rect.top + (rect.height - tooltipRect.height) / 2;
                left = rect.left - tooltipRect.width - 20;
                break;
            case 'right':
                top = rect.top + (rect.height - tooltipRect.height) / 2;
                left = rect.right + 20;
                break;
            default:
                top = rect.bottom + 20;
                left = rect.left + (rect.width - tooltipRect.width) / 2;
        }

        // Ensure tooltip stays within viewport
        if (left < 20) left = 20;
        if (left + tooltipRect.width > viewport.width - 20) {
            left = viewport.width - tooltipRect.width - 20;
        }
        if (top < 20) top = 20;
        if (top + tooltipRect.height > viewport.height - 20) {
            top = viewport.height - tooltipRect.height - 20;
        }

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
    }

    nextStep() {
        this.currentStep++;
        this.showStep();
    }

    prevStep() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.showStep();
        }
    }

    finish() {
        console.log('Tour finished');
        this.isActive = false;
        this.currentStep = 0;
        
        // Remove highlights from all elements
        const highlightedElements = document.querySelectorAll('.tour-guide-highlight');
        highlightedElements.forEach(element => {
            this.removeHighlight(element);
        });
        
        // Clean up any remaining tour guide styles
        this.cleanupAllTourGuideStyles();
        
        // Hide overlay and tooltip
        if (this.overlay) {
            this.overlay.style.opacity = '0';
        }
        if (this.tooltip) {
            this.tooltip.style.opacity = '0';
            this.tooltip.style.transform = 'scale(0.9)';
        }
        
        this.updateStartTourButton();
    }

    skip() {
        console.log('Tour skipped');
        this.finish();
    }

    updateStartTourButton() {
        const startTourBtn = document.getElementById('start-tour');
        if (!startTourBtn) return;

        const btnContent = startTourBtn.querySelector('.btn-content');
        if (!btnContent) return;

        if (this.isActive) {
            startTourBtn.classList.add('active');
            btnContent.innerHTML = `
                <i class='bx bx-stop'></i>
                <span class="start-tour-text">${this.getTranslation('stop_tour')}</span>
            `;
        } else {
            startTourBtn.classList.remove('active');
            btnContent.innerHTML = `
                <i class='bx bx-play'></i>
                <span class="start-tour-text">${this.getTranslation('start_tour')}</span>
            `;
        }
    }

    showError(message) {
        console.error('Tour Guide Error:', message);
        alert(message);
    }

    getTranslation(key) {
        if (window.Laravel && window.Laravel.translations && window.Laravel.translations[key]) {
            return window.Laravel.translations[key];
        }
        
        // Fallback translations
        const fallbacks = {
            'start_tour': 'Mulai Tour',
            'stop_tour': 'Hentikan Tour',
            'next': 'Selanjutnya',
            'previous': 'Sebelumnya',
            'finish': 'Selesai',
            'skip': 'Lewati'
        };
        
        return fallbacks[key] || key;
    }
}

// Initialize tour guide when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.tourGuide = new SimpleTourGuide();
});

// Also initialize if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.tourGuide = new SimpleTourGuide();
    });
} else {
    window.tourGuide = new SimpleTourGuide();
}
