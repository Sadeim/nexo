<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'company_logo' => asset('frontend_assets/images/logo.svg'),
            'email'        => 'example@example.com',
            'phone'        => '+1 305-253-7850',
            'whatsapp'     => '+1 305-253-7850',
            'linkedin'     => 'https://linkedin.com/company/example',
            'facebook'     => 'https://facebook.com/company/example',
            'instagram'    => 'https://instagram.com/company/example',
            'twitter'      => 'https://twitter.com/company/example',
            'address'      => '1283 WILLOW CREEK ROAD',
            'web_address'  => 'NEXOBARBER.COM',
            'site_description'  => 'Professionally develop long-term performance based architectures metrics rather than',
            'map_embed'  => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.2528000654!2d-74.14448744699546!3d40.69763123333061!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1733550855459!5m2!1sen!2sbd',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
