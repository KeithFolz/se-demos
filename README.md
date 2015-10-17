## Downloading and installing Janrain demos
January, 2015

http://demos.janrain.com

## Overview

The code that generates the demos available at demos.janrain.com is available
to the public for download at https://github.com/janrain/se-demos. You can
review the source code to see how various integrations and functions work.

### Technical architecture

The Janrain demo site is written in PHP.

### Prerequisites

* If you just want to view the source code, the only prerequisite is a text
  editor to view the source code, and a tool to uncompress the .zip archive.

* If you want to run the demos on your local machine, you will need a local web
  server environment with PHP enabled (such as MAMP).

* If you want to get the Social Login demos running, you will need your own
  instance of Janrain Social Login. Please contact your Janrain sales
  representative for an instance of Janrain Social Login.

### High-level process

* Download the `se-demos` git repo from github.
* Rename the parent directory and make sure it’s in the web root
* If you are using Social Login, add your API key in a place where the demos
  can find it (instructions below)
* Test your demo site

---

## *Step 1*: Download the se-demos git repo from github

Go to https://github.com/janrain/se-demos and click on "Download ZIP"

This will download a zip archive to your local machine: `se-demos-master.zip`

Unzip the archive. This will create the directory `se-demos-master`

## *Step 2*: Rename the parent directory and make sure it’s in the web root

Rename the directory `se-demos-master` to `JanrainDemoSites`

Move the JanrainDemoSites directory to your web root.

    [webroot]/JanrainDemoSites

For example, if you are using MAMP as your local web server, and its web root is

    /Applications/MAMP/htdocs

then the path for the Janrain demos should be:

    /Applications/MAMP/htdocs/JanrainDemoSites

## *Step 3*: If you are using Social Login, add your API key

The files that need the API key are:

    JanrainDemoSites/default/templates/socialRedirect/tokenURL.php

and

    JanrainDemoSites/default/templates/socialAjax/ajaxScript.php

By default, these files will expect your API key to be in the following location:

    [filesystemRoot]/Janrain/apiKey.txt

Create this file (apiKey.txt) and paste your API key in the file. The API key
should be the only thing in the file.

Looking at the source code for tokenURL.php and socialAjax.php, you can elect
to store your API key in another way if you wish, as long as these files can
get the value.

## *Step 4*: Test your demo site

You should be all set now, and your demo site available at:

    http://localhost/JanrainDemoSites/

or whatever your web server’s home path is.

Elements
DocType
Instantiated value (htmlPageClass constructor):
    <!DOCTYPE html> by default
Can be assigned by:
    setDocType() method.

<HTML>, <head>, <body>
Cannot be directly set, other than by setting member elements.

meta
Instantiated value (demoClass constructor):
    <meta charset='utf-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
Can be assigned by:
    addMeta() method.

title
Assigned by:
    user input (params): non-html string
    No local dir check.
    if user does not supply a value, then a value will be automatically created.

css
Instantiated value (demoClass constructor):
    <link rel='stylesheet' href='/JanrainDemoSites/default/styles/screen.css'>

Can be assigned by:

heading
Assigned by:
    user input (params): non-html string
    No local dir check.
    if user does not supply a value, then a value will be automatically created.
