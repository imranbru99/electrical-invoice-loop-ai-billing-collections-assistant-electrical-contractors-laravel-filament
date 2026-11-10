<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ElectricalInvoiceLoop Feature Modules
    |--------------------------------------------------------------------------
    | Toggle modules on/off for each contractor business. Core billing modules
    | default ON; niche helpers can stay OFF until needed.
    */
    'invoicing' => [
        'enabled' => env('MODULE_INVOICING', true),
        'label' => 'AI Invoice Drafting',
        'description' => 'Create draft invoices from job tickets and service requests.',
        'core' => true,
    ],
    'collections_reminders' => [
        'enabled' => env('MODULE_COLLECTIONS', true),
        'label' => 'Collections & Reminders',
        'description' => 'Approve and send payment reminders with consent tracking.',
        'core' => true,
    ],
    'payments_reconciliation' => [
        'enabled' => env('MODULE_PAYMENTS', true),
        'label' => 'Payment Reconciliation',
        'description' => 'Match payments to invoices and track open balances.',
        'core' => true,
    ],
    'service_requests' => [
        'enabled' => env('MODULE_SERVICE_REQUESTS', true),
        'label' => 'Service Requests',
        'description' => 'Intake electrical service requests that feed billing.',
        'core' => true,
    ],
    'job_tickets' => [
        'enabled' => env('MODULE_JOB_TICKETS', true),
        'label' => 'Job Tickets',
        'description' => 'Field job tickets linked to technicians and invoices.',
        'core' => true,
    ],
    'technicians' => [
        'enabled' => env('MODULE_TECHNICIANS', true),
        'label' => 'Field Technicians',
        'description' => 'Manage licensed technicians and assignments.',
        'core' => true,
    ],
    'customers' => [
        'enabled' => env('MODULE_CUSTOMERS', true),
        'label' => 'Customer Directory',
        'description' => 'Homeowner and commercial customer CRM for billing.',
        'core' => true,
    ],
    'ai_draft_assistant' => [
        'enabled' => env('MODULE_AI_DRAFT', true),
        'label' => 'AI Draft Assistant',
        'description' => 'AI drafts invoices, reminder copy, and follow-ups.',
        'core' => true,
    ],
    'audit_log' => [
        'enabled' => env('MODULE_AUDIT_LOG', true),
        'label' => 'Approval Audit Log',
        'description' => 'Human-approval trail for drafts and reminder sends.',
        'core' => true,
    ],
    'unicode_pdf' => [
        'enabled' => env('MODULE_UNICODE_PDF', true),
        'label' => 'Unicode PDF Invoices',
        'description' => 'Generate invoice PDFs with multi-language support.',
        'core' => false,
    ],
    'universal_slug' => [
        'enabled' => env('MODULE_UNIVERSAL_SLUG', true),
        'label' => 'Universal Slugs',
        'description' => 'SEO-safe slugs for public invoice and ticket links.',
        'core' => false,
    ],
    'attendance_hub' => [
        'enabled' => env('MODULE_ATTENDANCE', false),
        'label' => 'Technician Attendance Hub',
        'description' => 'Optional attendance machine sync for field crews.',
        'core' => false,
    ],
    'ai_catalog_scrape' => [
        'enabled' => env('MODULE_AI_CATALOG', false),
        'label' => 'AI Parts Catalog Scrape',
        'description' => 'Optional online parts/pricing catalog enrichment.',
        'core' => false,
    ],
    'integrations_hub' => [
        'enabled' => env('MODULE_INTEGRATIONS', false),
        'label' => 'Integrations Hub',
        'description' => 'Jobber / ServiceTitan / QuickBooks / Google Calendar bridges.',
        'core' => false,
    ],
    'calendar_sync' => [
        'enabled' => env('MODULE_CALENDAR', false),
        'label' => 'Google Calendar Sync',
        'description' => 'Sync job schedules to Google Calendar.',
        'core' => false,
    ],
    'quickbooks_sync' => [
        'enabled' => env('MODULE_QUICKBOOKS', false),
        'label' => 'QuickBooks Sync',
        'description' => 'Push approved invoices and payments to QuickBooks.',
        'core' => false,
    ],
];
