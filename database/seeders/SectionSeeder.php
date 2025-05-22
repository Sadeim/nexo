<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Section::truncate();
        Section::create([
            'key' => 'services_section',
            'title' => 'Our Services',
            'description' => 'We offer a variety of services to meet your needs.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'testimonials_section',
            'title' => '',
            'description' => '',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'works_section',
            'title' => 'Our Works',
            'description' => 'Check out our latest projects.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'about_section',
            'title' => 'About Us',
            'description' => 'Learn more about our company and team.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'teams_section',
            'title' => 'Meet Our Team',
            'description' => 'Get to know the people behind our success.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'faqs_section',
            'title' => 'Frequently Asked Questions',
            'description' => 'Find answers to common questions.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'sliders_section',
            'title' => 'Sliders',
            'description' => 'Check out our latest promotions and offers.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'clients_section',
            'title' => 'Our Clients',
            'description' => 'We are proud to work with these amazing clients.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'blog_section',
            'title' => 'Latest News',
            'description' => 'Stay updated with our latest news and articles.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'events_section',
            'title' => 'Upcoming Events',
            'description' => 'Join us for our upcoming events and activities.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'newsletter_section',
            'title' => 'Newsletter Subscription',
            'description' => 'Subscribe to our newsletter for the latest updates.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'skills_section',
            'title' => 'Our Skills',
            'description' => 'Discover the skills that set us apart.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'reasons_section',
            'title' => 'Why Choose Us',
            'description' => 'Learn why we are the best choice for your needs.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'approaches_section',
            'title' => 'Our Approach',
            'description' => 'Discover our unique approach to service.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'contact_section',
            'title' => 'Contact Us',
            'description' => 'Get in touch with us for any inquiries.',
            'is_active' => true,
        ]);
        Section::create([
            'key' => 'about_page',
            'image' => 'about.jpg',
        ]);
        Section::create([
            'key' => 'blog_page',
            'image' => 'blog.jpg',
        ]);
        Section::create([
            'key' => 'contact_page',
            'image' => 'contact.jpg',
        ]);
        Section::create([
            'key' => 'achievements_section',
            'title' => 'Our achievements',
            'description' => 'We offer a variety of services to meet your needs.',
            'is_active' => true,
        ]);
    }
}
