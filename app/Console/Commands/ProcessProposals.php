<?php

namespace App\Console\Commands;

use App\Project;
use App\Repository\State;
use App\Repository\Connection;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Yaml;

class ProcessProposals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proposal:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for changes to proposals';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function getMergedMrFilenameToUrlMap()
    {
        $result = [];

        $connection = new Connection(new Client());
        $merged = $connection->mergeRequests(State::Merged);
        foreach ($merged as $request) {
            $newFiles = $connection->getNewFiles($request);
            if ($newFiles->count() != 1) {
                continue;
            }
            $filename = $newFiles->first();
            if (!preg_match('/.+\.md$/', $filename)) {
                continue;
            }
            if (basename($filename) != $filename) {
                continue;
            }
            $result[$filename] = $request->url();
        }

        return $result;
    }

    private const layoutToState = [ 'fr'    => 'FUNDING-REQUIRED',
                                    'wip'   => 'WORK-IN-PROGRESS',
                                    'cp'    => 'COMPLETED'];

    private const mandatoryFields = [   'amount',
                                        'author',
                                        'date',
                                        'layout',
                                        'milestones',
                                        'title'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mergedMrFilenameToUrlMap = null;

        $files = Storage::files('proposals');
        foreach ($files as $file) {
            if (!strpos($file,'.md')) {
                continue;
            }

            $filename = basename($file);

            try {
                $detail['name'] = $filename;
                $detail['values'] = $this->getAmountFromText($file);

                foreach ($this::mandatoryFields as $field) {
                    if (empty($detail['values'][$field])) {
                        throw new \Exception("Mandatory field $field is missing");
                    }
                }

                $amount = floatval(str_replace(",", ".", $detail['values']['amount']));
                $author = htmlspecialchars($detail['values']['author'], ENT_QUOTES);
                $date = strtotime($detail['values']['date']);
                $state = $this::layoutToState[$detail['values']['layout']];
                $milestones = $detail['values']['milestones'];
                $title = htmlspecialchars($detail['values']['title'], ENT_QUOTES);

                $project = Project::where('filename', $filename)->first();
                if (!$project) {
                    if ($mergedMrFilenameToUrlMap === null) {
                        $mergedMrFilenameToUrlMap = $this->getMergedMrFilenameToUrlMap();
                    }
                    if (!isset($mergedMrFilenameToUrlMap[$filename])) {
                        $this->error("Project $filename: failed to find matching merged MR");
                        $gitlab_url = null;
                    } else {
                        $gitlab_url = htmlspecialchars($mergedMrFilenameToUrlMap[$filename], ENT_QUOTES);
                    }

                    $this->info("New project $filename Gitlab MR '$gitlab_url'");
                    $project = new Project();
                    $project->gitlab_url = $gitlab_url;
                    $project->created_at = $date;
                    $project->filename = $filename;
                } else {
                    $this->info("Updating project $filename");
                }

                if (isset($detail['values']['gitlab_url'])) {
                    $project->gitlab_url = htmlspecialchars($detail['values']['gitlab_url'], ENT_QUOTES);
                }

                $project->author = $author;
                $project->state = $state;
                $project->target_amount = $amount;
                $project->title = $title;
                $project->milestones = sizeof($milestones);
                $project->milestones_completed = array_reduce($milestones, function($k, $milestone) { return $milestone['done'] ? $k + 1 : $k; }, 0);
                $project->save();
            } catch (\Exception $e) {
                $this->error("Error processing project $filename: {$e->getMessage()}");
            }
        }
    }

    /**
     * Gets the proposal variables out the top of the file
     *
     * @param string $filename
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getAmountFromText($filename = 'additional-gui-dev.md')
    {
        $contents = preg_split('/\r?\n?---\r?\n/m', Storage::get($filename));
        if (sizeof($contents) < 3) {
            throw new \Exception("Failed to parse proposal, can't find YAML description surrounded by '---' lines");
        }
        return Yaml::parse($contents[1]);
    }
}
