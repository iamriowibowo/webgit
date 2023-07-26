## About Webgit

Webgit is a web based GUI tool to help software company to test feature by switching git branch with only few clicks instead of typing git checkout manually on server.  

Webgit is accessible, powerful, and provides tools required for large, robust applications.

## Installation
Once you've cloned the repo, run command these commands 
```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Set up your database in `.env` file and then run

```
php artisan migrate
```

You need to get the path to the Git executable on your system, you can use the which command in the terminal. Here's how you can do it:

1. Open a terminal or command prompt.

2. Type the following command and press Enter:

```
which git
```
This command will search for the Git executable in the system's PATH and display the path to it.

Note: If you are using Windows and have installed Git with the official Git for Windows installer, the which command may not be available. In that case, you can try using the where command instead:

```
where git
```
The where command will perform a similar search and display the path to the Git executable.

3. The command output will show the path to the Git executable. It might look something like this:

```
C:\laragon\bin\git\bin\git.exe
```
This path can be used in your PHP code or Laravel application when executing Git commands using the Process component or other methods.

Note that the exact path may vary depending on your system and how Git is installed.

Now you only need to edit git path in `.env` file.

```
GIT_EXEC=[YOUR_GIT_PATH_HERE]
GIT_PULL=true
```

`GIT_EXEC` is the path to the git executable.

`GIT_PULL` is a boolean value, this variable is used to determine wether you want to git pull after git checkout. Default value is true.