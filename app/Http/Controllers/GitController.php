<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GitServices;

class GitController extends Controller
{
    /**
     * Show project list.
     *
     * @return \Illuminate\Http\Response
     */
    public function project()
    {
        return GitServices::projects();
    }

    /**
     * Show list of a project's branch
     * 
     * @return \Illuminate\Http\Response
     */
    public function projectBranch(Request $request)
    {
        return GitServices::projectBranches($request);
    }

    /**
     * Project git checkout to selected branch
     */
    public function projectBranchCheckout(Request $request)
    {
        return GitServices::projectBranchCheckout($request);
    }
}
