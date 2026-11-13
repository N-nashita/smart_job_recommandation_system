<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use App\Services\MatchScoreService;
use Illuminate\Http\Request;

class CandidateJobsController extends Controller
{
    public function index(Request $request, MatchScoreService $match)
    {
        $c = $request->user();

        $appliedJobIds = Application::where('candidate_id', $c->id)->pluck('job_post_id')->toArray();
        $savedJobIds   = $c->savedJobs()->pluck('job_post_id')->toArray();

        $query = JobPost::open()
            ->where('is_suspicious', false)
            ->with('recruiter');

        $q = $request->string('q')->trim()->toString();
        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $location = $request->string('location')->trim()->toString();
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }

        $type = $request->string('type')->trim()->toString();
        if ($type) {
            $query->where('type', $type);
        }

        $category = $request->string('category')->trim()->toString();
        if ($category) {
            $query->where('skills', 'like', "%{$category}%");
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();

        $jobs->getCollection()->transform(fn ($j) => [
            'job'   => $j,
            'score' => $match->score($c, $j),
            'applied' => in_array($j->id, $appliedJobIds, true),
            'saved'   => in_array($j->id, $savedJobIds, true),
        ]);

        $locations = JobPost::open()->where('is_suspicious', false)->distinct()->pluck('location');
        $allCountries = [
            'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan',
            'Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi',
            'Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo (Congo-Brazzaville)','Costa Rica','Croatia','Cuba','Cyprus','Czechia',
            'Democratic Republic of the Congo','Denmark','Djibouti','Dominica','Dominican Republic',
            'Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia',
            'Fiji','Finland','France',
            'Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana',
            'Haiti','Honduras','Hungary',
            'Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Ivory Coast',
            'Jamaica','Japan','Jordan',
            'Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan',
            'Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg',
            'Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique','Myanmar (Burma)',
            'Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','North Korea','North Macedonia','Norway',
            'Oman',
            'Pakistan','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal',
            'Qatar',
            'Romania','Russia','Rwanda',
            'Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria',
            'Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu',
            'Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan',
            'Vanuatu','Vatican City','Venezuela','Vietnam',
            'Yemen',
            'Zambia','Zimbabwe'
        ];
        $types     = ['Full-time', 'Part-time', 'Contract', 'Internship'];
        $categories = ['Software Development','Web Development','Mobile Development','Frontend Development','Backend Development','Full Stack Development','DevOps','Data Science','Data Engineering','Data Analysis','Machine Learning','AI & ML','Cybersecurity','Cloud Computing','Database Administration','Network Engineering','Quality Assurance','UI/UX Design','Graphic Design','Product Design','Product Management','Project Management','Marketing','Digital Marketing','Content Marketing','SEO','Social Media Marketing','Sales','Business Development','Account Management','Human Resources','Recruitment','Finance','Accounting','Investment','Business Analysis','Operations','Supply Chain','Logistics','Customer Support','Customer Success','Healthcare','Medical','Nursing','Pharmacy','Biotechnology','Research','Science','Engineering','Mechanical Engineering','Electrical Engineering','Civil Engineering','Chemical Engineering','Architecture','Construction','Real Estate','Education','Teaching','Training','Legal','Law','Compliance','Administration','Office Management','Executive','Hospitality','Tourism','Travel','Food & Beverage','Retail','E-commerce','Transportation','Automotive','Aviation','Maritime','Agriculture','Manufacturing','Media','Journalism','Publishing','Broadcasting','Film','Video Production','Animation','Music','Performing Arts','Sports','Fitness','Wellness','Beauty','Fashion','Non-profit','Government','Public Relations','Communications','Writing','Editing','Translation','Language','Religion','Other'];

        return view('candidate.jobs.index', compact(
            'jobs', 'q', 'location', 'type', 'category',
            'locations', 'allCountries', 'types', 'categories'
        ));
    }

    public function show(Request $request, JobPost $job, MatchScoreService $match)
    {
        $c = $request->user();
        $job->load(['recruiter', 'applications' => fn ($q) => $q->where('candidate_id', $c->id)]);

        $applied = $job->applications->isNotEmpty();
        $saved   = $job->savedBy()->where('user_id', $c->id)->exists();
        $score   = $match->score($c, $job);

        return view('candidate.jobs.show', compact('job', 'applied', 'saved', 'score'));
    }
}