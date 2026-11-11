<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Content module
            ['module' => 'content', 'action' => 'view', 'slug' => 'content.view', 'description' => 'View all content items'],
            ['module' => 'content', 'action' => 'create', 'slug' => 'content.create', 'description' => 'Create draft articles and content'],
            ['module' => 'content', 'action' => 'edit', 'slug' => 'content.edit', 'description' => 'Edit assigned articles'],
            ['module' => 'content', 'action' => 'schedule', 'slug' => 'content.schedule', 'description' => 'Schedule publishing times'],
            ['module' => 'content', 'action' => 'publish', 'slug' => 'content.publish', 'description' => 'Publish or unpublish articles'],
            ['module' => 'content', 'action' => 'delete', 'slug' => 'content.delete', 'description' => 'Delete or archive articles'],

            // Taxonomy module
            ['module' => 'taxonomy', 'action' => 'view', 'slug' => 'taxonomy.view', 'description' => 'View categories, tags, exams'],
            ['module' => 'taxonomy', 'action' => 'manage', 'slug' => 'taxonomy.manage', 'description' => 'Create, edit, merge categories & tags'],

            // Media module
            ['module' => 'media', 'action' => 'view', 'slug' => 'media.view', 'description' => 'Browse media library'],
            ['module' => 'media', 'action' => 'upload', 'slug' => 'media.upload', 'description' => 'Upload images and PDF documents'],
            ['module' => 'media', 'action' => 'delete', 'slug' => 'media.delete', 'description' => 'Delete media assets'],

            // Slots & Menus
            ['module' => 'slots', 'action' => 'manage', 'slug' => 'slots.manage', 'description' => 'Configure homepage slots & banners'],
            ['module' => 'menus', 'action' => 'manage', 'slug' => 'menus.manage', 'description' => 'Manage mega-menus and footers'],

            // Quizzes & Tests
            ['module' => 'quiz', 'action' => 'view', 'slug' => 'quiz.view', 'description' => 'View question bank & quizzes'],
            ['module' => 'quiz', 'action' => 'create', 'slug' => 'quiz.create', 'description' => 'Author questions and quiz sets'],
            ['module' => 'quiz', 'action' => 'publish', 'slug' => 'quiz.publish', 'description' => 'Publish quizzes and answer keys'],
            ['module' => 'evaluator', 'action' => 'evaluate', 'slug' => 'evaluator.grade', 'description' => 'Grade subjective answer copies'],

            // LMS & Courses
            ['module' => 'lms', 'action' => 'manage', 'slug' => 'lms.manage', 'description' => 'Manage courses, batches & lessons'],

            // Commerce & Finance
            ['module' => 'commerce', 'action' => 'view', 'slug' => 'commerce.view', 'description' => 'View orders and products'],
            ['module' => 'commerce', 'action' => 'manage', 'slug' => 'commerce.manage', 'description' => 'Manage prices, coupons & inventory'],
            ['module' => 'finance', 'action' => 'reconcile', 'slug' => 'finance.manage', 'description' => 'Manage payments, refunds & invoices'],

            // CRM & Leads
            ['module' => 'crm', 'action' => 'view_assigned', 'slug' => 'crm.view_assigned', 'description' => 'View assigned leads'],
            ['module' => 'crm', 'action' => 'manage_all', 'slug' => 'crm.manage_all', 'description' => 'Manage all leads & counsellor routing'],
            ['module' => 'counselling', 'action' => 'schedule', 'slug' => 'counselling.schedule', 'description' => 'Book and conduct counselling sessions'],

            // Centre Management
            ['module' => 'centres', 'action' => 'manage', 'slug' => 'centres.manage', 'description' => 'Manage centre schedules and offline slots'],

            // Support & Users
            ['module' => 'support', 'action' => 'tickets', 'slug' => 'support.tickets', 'description' => 'Handle support tickets and OTP resends'],
            ['module' => 'users', 'action' => 'view', 'slug' => 'users.view', 'description' => 'View registered users'],
            ['module' => 'users', 'action' => 'manage', 'slug' => 'users.manage', 'description' => 'Manage user accounts & block/unblock'],
            ['module' => 'users', 'action' => 'impersonate', 'slug' => 'users.impersonate', 'description' => 'Audit-logged impersonation of students'],

            // System & Audit
            ['module' => 'audit', 'action' => 'view', 'slug' => 'audit.view', 'description' => 'Inspect audit logs and tamper-evident hashes'],
            ['module' => 'settings', 'action' => 'manage', 'slug' => 'settings.manage', 'description' => 'Configure platform keys and feature flags'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        $allPermissions = Permission::all();

        // 12 Roles
        $roles = [
            'super_admin' => [
                'name' => 'Super Admin',
                'description' => 'Full administrative control over all modules, staff, settings, and logs',
                'is_system' => true,
                'permissions' => $allPermissions->pluck('slug')->toArray(),
            ],
            'senior_editor' => [
                'name' => 'Senior Editor / Publisher',
                'description' => 'Can author, edit, schedule, publish, manage taxonomy, slots, menus, and translations',
                'is_system' => true,
                'permissions' => [
                    'content.view', 'content.create', 'content.edit', 'content.schedule', 'content.publish', 'content.delete',
                    'taxonomy.view', 'taxonomy.manage',
                    'media.view', 'media.upload', 'media.delete',
                    'slots.manage', 'menus.manage',
                    'audit.view',
                ],
            ],
            'editor' => [
                'name' => 'Editor',
                'description' => 'Drafts, edits and schedules articles in assigned sections/languages without live publishing rights',
                'is_system' => true,
                'permissions' => [
                    'content.view', 'content.create', 'content.edit', 'content.schedule',
                    'taxonomy.view',
                    'media.view', 'media.upload',
                ],
            ],
            'quiz_author' => [
                'name' => 'Quiz Author / Faculty',
                'description' => 'Manages question bank, builds quizzes, sets scoring rules, and publishes explanations',
                'is_system' => true,
                'permissions' => [
                    'quiz.view', 'quiz.create', 'quiz.publish',
                    'media.view', 'media.upload',
                ],
            ],
            'evaluator' => [
                'name' => 'Evaluator',
                'description' => 'Grades subjective Mains answer copies and uploads annotated evaluation copies',
                'is_system' => true,
                'permissions' => [
                    'evaluator.grade',
                    'media.view', 'media.upload',
                ],
            ],
            'sales_counsellor' => [
                'name' => 'Sales Counsellor',
                'description' => 'Manages assigned leads, logs calls and notes, books sessions; no access to content or finance',
                'is_system' => true,
                'permissions' => [
                    'crm.view_assigned',
                    'counselling.schedule',
                ],
            ],
            'finance' => [
                'name' => 'Finance',
                'description' => 'Manages orders, transactions, refunds, GST invoices, and reconciliation',
                'is_system' => true,
                'permissions' => [
                    'commerce.view',
                    'finance.manage',
                    'audit.view',
                ],
            ],
            'centre_manager' => [
                'name' => 'Centre Manager',
                'description' => 'Manages centre batches, offline exam slots, local notices, and attendance',
                'is_system' => true,
                'permissions' => [
                    'centres.manage',
                    'crm.view_assigned',
                ],
            ],
            'support_agent' => [
                'name' => 'Support Agent',
                'description' => 'Handles student enquiries, enrollments search, tickets, and OTP resends',
                'is_system' => true,
                'permissions' => [
                    'support.tickets',
                    'users.view',
                ],
            ],
            'paid_student' => [
                'name' => 'Paid Student (Enrolled)',
                'description' => 'Access purchased courses, test series, offline test slots, and evaluated copies',
                'is_system' => false,
                'permissions' => [],
            ],
            'registered_student' => [
                'name' => 'Registered Student (Free Tier)',
                'description' => 'Free account with bookmarks, test attempts history, downloads, and personal notes',
                'is_system' => false,
                'permissions' => [],
            ],
            'visitor' => [
                'name' => 'Visitor (Anonymous)',
                'description' => 'Public read access to articles, public quizzes, feeds, and lead forms',
                'is_system' => false,
                'permissions' => [],
            ],
        ];

        foreach ($roles as $slug => $data) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_system' => $data['is_system'],
                ]
            );

            if (!empty($data['permissions'])) {
                $permIds = Permission::whereIn('slug', $data['permissions'])->pluck('id');
                $role->permissions()->sync($permIds);
            }
        }
    }
}
