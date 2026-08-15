<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Is my feedback anonymous?',
                'answer' => 'You must enter your first and last name to submit a feedback.',
                'keywords' => 'anonymous, name, identity, hide name, unknown',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Who will see my feedback?',
                'answer' => 'Your feedback will be reviewed by relevant staff based on classification.',
                'keywords' => 'see, view, read, staff, who, privacy',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I get a response?',
                'answer' => 'If you leave your email, we may contact you for follow-up or clarification.',
                'keywords' => 'response, reply, email, contact, update, status',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Faq::insert($faqs);
    }
}
