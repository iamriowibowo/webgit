<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class GitServices {

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
        
        if(!$project){
            return response()->json([
                'message' => 'Choose project first',
            ], 400);
        }

        // Check if project exist
        $projectExist = in_array($project, self::projects());

        if(!$projectExist){
            return response()->json([
                'message' => 'Project does not exist',
            ], 404);
        }

        // Path to the repository of project B
        $repositoryPath = '../../'.$project;

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
        $branch = $request->branch;

        // Check if branch active
        $branchActive = in_array('* '.$branch, self::projectBranches($request));

        if($branchActive){
            return response()->json([
                'message' => 'Branch '.$branch.' is currently active.',
            ], 404);
        }

        // Check if branch exist
        $branchExist = in_array($branch, self::projectBranches($request));

        if(!$branchExist){
            return response()->json([
                'message' => 'Branch does not exist',
            ], 404);
        }

        // Git executable
        $gitExecutable = self::gitExec();

        // Path to the repository of project B
        $repositoryPath = '../../'.$project;

        // Create a new process to execute the git checkout command
        $processGitCheckout = new Process([
            $gitExecutable, 
            '--git-dir=' . $repositoryPath . '/.git', 
            '--work-tree=' . $repositoryPath, 
            'checkout', 
            $branch
        ]);
    
        // Run the checkout process
        $processGitCheckout->run();

        // When git checkout failed, return error message
        if (!$processGitCheckout->isSuccessful()) {
            return response()->json([
                'message' => 'Failed to checkout branch '.$branch,
                'error' => $processGitCheckout->getErrorOutput()
            ], 500);
        }
        
        if(env('GIT_PULL')){
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
                    'message' => 'Failed to pull branch '.$branch,
                    'error' => $processGitPull->getErrorOutput()
                ], 500);
            }

            return response()->json([
                'message' => 'Branch '.$branch.' checked out and pulled successfully',
            ]);

        }

        return response()->json([
            'message' => 'Branch '.$branch.' checked out successfully',
        ]);
    }    
}