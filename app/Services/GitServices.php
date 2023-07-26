<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class GitServices
{

    public static function gitExec()
    {
        $gitExecutable = env('GIT_EXEC');

        return $gitExecutable;
    }

    public static function projects()
    {
        // Specify the directory path
        $directory = '../../';

        // Get all directories
        $directories = glob($directory . '*', GLOB_ONLYDIR);

        // Store directory in array
        $arrayDirectory =  [];

        foreach ($directories as $dir) {
            // Get the directory name from the full path
            $dirName = basename($dir);

            // Push to directory
            array_push($arrayDirectory, $dirName);
        }

        // Remove webgit from directory list
        return array_diff($arrayDirectory, array('webgit'));
    }

    public static function projectBranches($request)
    {
        $project = $request->project;

        if (!$project) {
            return response()->json([
                'message' => 'Choose project first',
            ], 400);
        }

        // Check if project exist
        $projectExist = in_array($project, self::projects());

        if (!$projectExist) {
            return response()->json([
                'message' => 'Project does not exist',
            ], 404);
        }

        // Path to the repository of project
        $repositoryPath = '../../' . $project;

        // Git executable
        $gitExecutable = self::gitExec();

        // Git process
        $process = new Process([$gitExecutable, '--git-dir=' . $repositoryPath . '/.git', '--work-tree=' . $repositoryPath, 'branch']);

        // Run the process and capture the output
        $process->run();

        // Get the output as a string
        $output = $process->getOutput();

        // Process the output to extract the branch names
        $branches = explode(PHP_EOL, trim($output));

        // Variable to store branch name
        $arrayBranches = explode("\n", str_replace('\n', "\n", $branches[0]));

        // This loop used to remove space character
        foreach ($arrayBranches as &$value) {
            if (strpos($value, ' ') !== false) {
                $value = trim($value);
            }
        }

        return $arrayBranches;
    }

    public static function projectBranchCheckout($request)
    {
        $project = $request->project;
        $selectedBranch = $request->branch; // Full branch name included astersik (*) sign        
        $branch = explode(' ', $selectedBranch)[1] ?? $selectedBranch; // Get branch name only without asterisk * sign on active branch

        // Check if branch exist
        $branchExist = in_array($selectedBranch, self::projectBranches($request));
        if (!$branchExist) {
            return response()->json([
                'message' => 'Branch '.$branch.' does not exist',
            ], 404);
        }

        // Git executable
        $gitExecutable = self::gitExec();

        // Path to the repository of project
        $repositoryPath = '../../' . $project;

        // Create a new process to execute the git checkout command
        $processGitCheckout = new Process([
            $gitExecutable,
            '--git-dir=' . $repositoryPath . '/.git',
            '--work-tree=' . $repositoryPath,
            'checkout',
            $branch
        ]);

        // Run git checkout process
        $processGitCheckout->run();

        // When git checkout failed, return error message
        if (!$processGitCheckout->isSuccessful()) {
            return response()->json([
                'message' => 'Failed to checkout branch ' . $branch,
                'error' => $processGitCheckout->getErrorOutput()
            ], 500);
        }

        // Run git pull process
        if (env('GIT_PULL')) {
            $gitPull = self::projectBranchPull($project, $branch);
        
            return response()->json([
                'message' => $gitPull->original['message'],
                'error' => $gitPull->original['error'] ?? ''
            ], 200);
        }
    }

    public static function projectBranchPull($project, $branch)
    {
        // Git executable
        $gitExecutable = self::gitExec();

        // Path to the repository of project
        $repositoryPath = '../../' . $project;

        // Create a new process to execute the git checkout command
        $processGitPull = new Process([
            $gitExecutable,
            '--git-dir=' . $repositoryPath . '/.git',
            '--work-tree=' . $repositoryPath,
            'pull'
        ]);

        // Run the pull process
        $processGitPull->run();

        // When git pull failed, return error message
        if (!$processGitPull->isSuccessful()) {
            return response()->json([
                'message' => 'Failed to pull branch ' . $branch,
                'error' => $processGitPull->getErrorOutput()
            ], 400);
        }

        return response()->json([
            'message' => 'Branch ' . $branch . ' checked out and pulled successfully',
        ], 200);
    }
}
