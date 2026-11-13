<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Notification;
use App\Models\Recruiter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $c1 = User::firstOrCreate(['email' => 'candidate@example.com'], [
            'name' => 'Alex Rivera', 'role' => 'candidate', 'password' => bcrypt('password'),
            'email_verified_at' => now(), 'phone' => '+1-555-0101', 'category' => 'Software Development',
            'experience' => '3-5 years', 'degree' => 'BS Computer Science', 'institution' => 'State University',
            'major' => 'Computer Science', 'grad_year' => 2022, 'cgpa' => 3.5]);

        $c2 = User::firstOrCreate(['email' => 'priya@example.com'], [
            'name' => 'Priya Sharma', 'role' => 'candidate', 'password' => bcrypt('password'),
            'email_verified_at' => now(), 'phone' => '+1-555-0102', 'category' => 'Design',
            'experience' => '1-2 years', 'degree' => 'Bachelor of Design', 'institution' => 'Art Institute',
            'major' => 'Visual Communication', 'grad_year' => 2023, 'cgpa' => 3.8]);

        $r = Recruiter::firstOrCreate(['email' => 'recruiter@example.com'], [
            'name' => 'Hiring Manager', 'job_title' => 'Technical Recruiter', 'company_name' => 'TechCorp Inc',
            'password' => bcrypt('password'), 'phone' => '+1-555-0200', 'industry' => 'Technology',
            'hq_location' => 'New York, NY', 'verification_status' => 'verified', 'verified_at' => now(),
            'registration_no' => 'TC-2024-001', 'website' => 'https://techcorp.example.com']);

        $j1 = JobPost::firstOrCreate(['title' => 'Senior PHP Developer', 'recruiter_id' => $r->id], [
            'status' => 'open', 'location' => 'New York, NY', 'type' => 'Full-time', 'salary_min' => 90000,
            'salary_max' => 140000, 'description' => 'PHP developer for backend team using Laravel and MySQL.',
            'skills' => ['PHP', 'Laravel', 'MySQL', 'Redis'], 'published_at' => now(), 'is_suspicious' => false]);

        $j2 = JobPost::firstOrCreate(['title' => 'Frontend React Developer', 'recruiter_id' => $r->id], [
            'status' => 'open', 'location' => 'Remote', 'type' => 'Full-time', 'salary_min' => 80000,
            'salary_max' => 120000, 'description' => 'Build UIs with React and TypeScript.',
            'skills' => ['React', 'TypeScript', 'CSS', 'JavaScript'], 'published_at' => now(), 'is_suspicious' => false]);

        $j3 = JobPost::firstOrCreate(['title' => 'UX/UI Designer', 'recruiter_id' => $r->id], [
            'status' => 'open', 'location' => 'San Francisco, CA', 'type' => 'Contract', 'salary_min' => 70000,
            'salary_max' => 100000, 'description' => 'User-centered designs for web and mobile products.',
            'skills' => ['Figma', 'UI Design', 'UX Research', 'Prototyping'], 'published_at' => now(), 'is_suspicious' => false]);

        $j4 = JobPost::firstOrCreate(['title' => 'DevOps Engineer', 'recruiter_id' => $r->id], [
            'status' => 'open', 'location' => 'Austin, TX', 'type' => 'Full-time', 'salary_min' => 100000,
            'salary_max' => 150000, 'description' => 'Cloud infrastructure, CI/CD, deployment automation.',
            'skills' => ['AWS', 'Docker', 'Kubernetes', 'CI/CD'], 'published_at' => now(), 'is_suspicious' => false]);

        $a1 = Application::firstOrCreate(['candidate_id' => $c1->id, 'job_post_id' => $j1->id], [
            'match_score' => 88, 'status' => 'new', 'skills_score' => 85, 'experience_score' => 90,
            'location_score' => 90, 'summary' => 'Strong PHP and Laravel background with 4 years experience.']);

        Application::firstOrCreate(['candidate_id' => $c2->id, 'job_post_id' => $j3->id], [
            'match_score' => 92, 'status' => 'shortlisted', 'skills_score' => 95, 'experience_score' => 85,
            'location_score' => 95, 'summary' => 'Excellent design portfolio with Figma expertise.']);

        if ($a1) {
            Interview::firstOrCreate(['application_id' => $a1->id], [
                'recruiter_id' => $r->id, 'scheduled_at' => now()->addDays(2)->setTime(10, 0, 0),
                'round' => 'Technical', 'mode' => 'meet', 'status' => 'waiting']);
        }

        $c1->savedJobs()->syncWithoutDetaching([$j2->id, $j4->id]);
        $c2->savedJobs()->syncWithoutDetaching([$j1->id]);

        Notification::firstOrCreate(['user_id' => $c1->id, 'type' => 'application', 'title' => 'Application submitted'],
            ['body' => 'Your application for Senior PHP Developer has been received.']);
        Notification::firstOrCreate(['user_id' => $c1->id, 'type' => 'interview', 'title' => 'Interview scheduled'],
            ['body' => 'Technical interview scheduled for Senior PHP Developer.',
             'data' => ['job_id' => $j1->id, 'application_id' => $a1->id], 'read_at' => null]);
        Notification::firstOrCreate(['user_id' => $c2->id, 'type' => 'application', 'title' => 'Application shortlisted'],
            ['body' => 'Your application for UX/UI Designer has been shortlisted.']);
        Notification::firstOrCreate(['user_id' => $c1->id, 'type' => 'profile_view', 'title' => 'Profile viewed'],
            ['body' => 'TechCorp Inc viewed your profile.']);

        echo "Seeded: candidates, recruiter, jobs, applications, interviews, saved jobs, notifications.\n";
    }
}
