[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SvanteF/mvc-report/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/SvanteF/mvc-report/?branch=main) [![Code Coverage](https://scrutinizer-ci.com/g/SvanteF/mvc-report/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/SvanteF/mvc-report/?branch=main) [![Build Status](https://scrutinizer-ci.com/g/SvanteF/mvc-report/badges/build.png?b=main)](https://scrutinizer-ci.com/g/SvanteF/mvc-report/build-status/main)


![MVC](/public/img/mvc_small.jpg)

# MVC - Objektorienterade webbteknologier

### Instruction on how to clone the repo and run the website

- Install the lab environment
    - [PHP](https://dbwebb.se/kurser/mvc-v2/labbmiljo/php)
    - [Composer](https://dbwebb.se/kurser/mvc-v2/labbmiljo/php-composer)
    - [Make](https://dbwebb.se/kurser/mvc-v2/labbmiljo/make2)
    - [Node.js](https://nodejs.org/)
    - [Git](https://dbwebb.se/kurser/mvc-v2/labbmiljo/git)
    - [Encore](https://github.com/dbwebb-se/mvc/blob/main/example/symfony/README.md)

- Clone repo to local computer

```bash
git clone https://github.com/SvanteF/mvc-report

cd mvc-report

```
- Build the assets
```bash
npm run build
```

- Run the application

```bash
symfony server:start

```

You can reach it here http://127.0.0.1:8000.

### Purpose and content

This repository is home to the code written by the author during the course "DV1608 V25 lp4 Objektorienterade webbteknologier". It also contains examples provided by Blekinge Tekniska Högskola. 

All tasks from the course can be found in the /src/ directory.

Most importantly, the project `Adventure - Laundry Master` is located in src/Adventure/.

A selection of important directories:
- `/docs/`
    - Coverage and documentation
- `/public/`
    - Public web
- `/src/`
    - Source code
- `/templates/`
    - Twig files
- `/tests/`
    - Test of source code
- `/tools/`
    - Tools for development phpdoc, phpmd, phpmetrics and phpstan


List of important web routes:
- `/proj`
    - The main project of the course. It is an adventure game titled Laundry Master.
- `/me`
    - Introduction of the author.
- `/about`
    - Information about the course.
- `/report`
    - All the reports from the course's modules.
- `/API`
    - JSON routes.
