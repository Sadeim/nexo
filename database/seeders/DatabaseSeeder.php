<?php

namespace Database\Seeders;

use Database\Seeders\PermissionSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(StaticPageSeeder::class);
        $this->call(CountrySeeder::class);
        // $this->call(BannerSeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(ServiceSeeder::class);
        // $this->call(ReasonSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(AboutSeeder::class);
        // $this->call(SkillSeeder::class);
        $this->call(WorkSeeder::class);
        // $this->call(AchievementSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(TestimonialSeeder::class);
        $this->call(SettingSeeder::class);
        // $this->call(EventSeeder::class);
        // $this->call(InstagramSeeder::class);
        // $this->call(MenuItemSeeder::class);
       $this->call(SliderSeeder::class);
        $this->call(ReasonTabSeeder::class);
        $this->call(FeatureSeeder::class);
    }
}
