<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Project;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FundingController extends Controller
{
    /**
     * Shows all projects
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return ProjectResource::collection(Project::all());
    }

    /**
     * Shows the project based on the payment id
     *
     * @param Request $request
     * @param $subaddr_index
     * @return ProjectResource|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $subaddr_index)
    {
        $project = Project::where('subaddr_index', $subaddr_index)->firstOrFail();

        if ($request->wantsJson())
        {
            return new ProjectResource($project);
        }

        return view('projects.show')
            ->with('project', $project);
    }

    public function donate(Request $request, $subaddr_index)
    {
        $project = Project::where('subaddr_index', $subaddr_index)->firstOrFail();

        if ($request->wantsJson())
        {
            return new ProjectResource($project);
        }

        return view('projects.donate')
            ->with('project', $project);
    }
}
