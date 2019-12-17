<?php

namespace App\Console\Commands;

use App\Project;
use App\Repository\State;
use App\Repository\Connection;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use stdClass;
use Symfony\Component\Yaml\Yaml;

class UpdateSiteProposals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proposal:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the files required for jeykll site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = [
            $this->ideaProposals(),
            $this->getProposals('Funding Required', 'FUNDING-REQUIRED'),
            $this->getProposals('Work in Progress', 'WORK-IN-PROGRESS'),
        ];
        $json = json_encode($response, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        \Storage::put('proposals.json', $json);

        $response = [
            $this->getProposals('Completed Proposals', 'COMPLETED'),
        ];
        $json = json_encode($response, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        \Storage::put('complete.json', $json);
    }

    private function sortProposalsByDateDesc($responseProposals)
    {
        usort($responseProposals, function($a, $b){
            return strtotime($a->date) < strtotime($b->date) ? 1 : -1;
        });
        return $responseProposals;
    }

    private function proposalFileExists($filename)
    {
        return \Storage::exists('proposals/'. basename($filename));
    }

    private function ideaProposals()
    {
        $group = new stdClass();
        $group->stage = 'Ideas';
        $responseProposals = [];

        $ideas = [];
        $connection = new Connection(new Client());
        $mergeRequests = $connection->mergeRequests(State::Opened);
        foreach ($mergeRequests as $mergeRequest) {
            $newFiles = $connection->getNewFiles($mergeRequest);
            if ($newFiles->count() != 1) {
                $this->error ("Skipping MR #{$mergeRequest->id()} '{$mergeRequest->title()}': contains multiple files");
                continue;
            }
            $filename = $newFiles->first();
            if (!preg_match('/.+\.md$/', $filename)) {
                $this->error("Skipping MR #{$mergeRequest->id()} '{$mergeRequest->title()}': doesn't contain any .md file");
                continue;
            }
            if (basename($filename) != $filename) {
                $this->error("Skipping MR #{$mergeRequest->id()} '{$mergeRequest->title()}': $filename must be in the root folder");
                continue;
            }
            if (in_array($filename, $ideas)) {
                $this->error("Skipping MR #{$mergeRequest->id()} '{$mergeRequest->title()}': duplicated $filename, another MR #$ideas[$filename]->id");
                continue;
            }
            $project = Project::where('filename', $filename)->first();
            if ($project && $this->proposalFileExists($filename)) {
                $this->error("Skipping MR #{$mergeRequest->id()} '{$mergeRequest->title()}': already have a project $filename");
                continue;
            }
            $this->info("Idea MR #{$mergeRequest->id()} '{$mergeRequest->title()}': $filename");

            $prop = new stdClass();
            $prop->name = htmlspecialchars(trim($mergeRequest->title()), ENT_QUOTES);
            $prop->{'gitlab-url'} = htmlspecialchars($mergeRequest->url(), ENT_QUOTES);
            $prop->author = htmlspecialchars($mergeRequest->author(), ENT_QUOTES);
            $prop->date = date('F j, Y', $mergeRequest->created_at());
            $responseProposals[] = $prop;
        }

        $group->proposals = $this->sortProposalsByDateDesc($responseProposals);
        return $group;
    }

    private function formatProposal($proposal)
    {
        $prop = new stdClass();
        $prop->name = $proposal->title;
        $prop->{'donate-address'} = $proposal->address;
        $prop->{'donate-qr-code'} = $proposal->address_uri ? $proposal->getQrCodeSrcAttribute() : null;
        $prop->{'gitlab-url'} = $proposal->gitlab_url;
        $prop->{'local-url'} = '/proposals/'. pathinfo($proposal->filename, PATHINFO_FILENAME) . '.html';
        $prop->contributions = $proposal->contributions;
        $prop->milestones = $proposal->milestones;
        $prop->{'milestones-completed'} = $proposal->milestones_completed;
        $milestones_percentage = min(100, (int)(($proposal->milestones_completed * 100) / $proposal->milestones));
        $prop->{'milestones-percentage'} = $milestones_percentage;
        $prop->percentage = $proposal->percentage_funded;
        $prop->amount = $proposal->target_amount;
        $prop->{'amount-funded'} = $proposal->raised_amount;
        $prop->author = $proposal->author;
        $prop->date = $proposal->created_at->format('F j, Y');
        return $prop;
    }

    private function getProposals($stage, $state)
    {
        $group = new stdClass();
        $group->stage = $stage;
        $responseProposals = [];
        $proposals = Project::where('state', $state)->get();
        foreach ($proposals as $proposal) {
            if ($this->proposalFileExists($proposal->filename)) {
                $responseProposals[] = $this->formatProposal($proposal);
            }
        }
        $group->proposals = $this->sortProposalsByDateDesc($responseProposals);
        return $group;
    }
}
