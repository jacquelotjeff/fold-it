Fold-it
======= 

Written in PHP, this application gives you several commands to make your folder better organized.

**Installation**

You need to install: 
- PHP 7.1 +
- Composer

Install dependencies:

```bash
composer install
```

**Available commands:**

Rename your files from their date of creation in order to have your folders more organized:

```shell
php app.php fold:rename-by-date input-directory output-directory
```

You can precise the timezone with option -t "Europe/Berlin".


*Example of use:*

- A folder named `photos` in the project directory contains:

<img src="docs/rename-by-date-example-input.png" alt="input folder" width="250"/>

- Execute the program: `php app.php fold:rename-by-date photos reclassified-photos`

- A new folder will be created:

<img src="docs/rename-by-date-example-output.png" alt="output folder" width="250"/>

**TODO:**

- Add unit tests
