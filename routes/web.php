<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/git/branches', function () {
    // Create a new process to execute the git branch command
    $process = new Process(['C:\laragon\bin\git\bin\git.exe', 'branch']);

    // Run the process and capture the output
    $process->run();

    // Get the output as a string
    $output = $process->getOutput();

    // Process the output to extract the branch names
    $branches = explode(PHP_EOL, trim($output));

    // Variable to store branch name
    $arrayBranches = explode("\n  ", str_replace('\n', "\n", $branches[0]));

    // Print the list of branches
    return $arrayBranches;
});

Route::get('/git/branches/checkout', function () {
    $coBr = 'feature/login';

    // Create a new process to execute the git checkout command
    $checkoutProcess = new Process(['C:\laragon\bin\git\bin\git.exe', 'checkout', $coBr]);

    // Run the checkout process
    $checkoutProcess->run();

    // Check if the checkout was successful
    if ($checkoutProcess->isSuccessful()) {
        echo 'Branch checked out successfully.';
    } else {
        echo 'Failed to checkout branch.';
    }

    // Create a new process to execute the git branch command
    $process = new Process(['C:\laragon\bin\git\bin\git.exe', 'branch']);

    // Run the process and capture the output
    $process->run();

    // Get the output as a string
    $output = $process->getOutput();

    // Process the output to extract the branch names
    $branches = explode(PHP_EOL, trim($output));

    // Variable to store branch name
    $arrayBranches = explode("\n  ", str_replace("\n", "\n", $branches[0]));

    // Print the list of branches
    return $arrayBranches;
});