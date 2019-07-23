# translation-tools

** /!\ This tool is under heavy development.**


The tools help symfony developers to sync their translation between branches or with other teams such as translator.

* `php bin/console debug` show what translations that the tools has been capable to detect.
* `php bin/console clean` generate a copy of the translations in a separate folder and order all key alphabetically, flatten keys.
* `php bin/console find` search for translation files, it helps you to understand how the tool detect files thanks to Symfony Finder Component.
* `php bin/console yaml:xlsx` convert yaml translation files to Excel (xlsx).
* `php bin/console xlsx:yaml` convert Excel (xlsx) to yaml translation files.

## Install

* clone this repository: `git clone https://github.com/sportfinder/translation-tools.git`
* install vendors: `composer install`

## php bin/console clean

This command will search for your translations files and create a clean copy of them ordered alphabetically and add #fixme
where translation is missing.

### arguments

* path: where to find yaml translation files (eg: `/path/to/your/project/translations/` )
* name: the pattern of the yaml translation files (most of the time: `.yaml` )
* output: file to generate (eg `/path/to/your/excel.xlsx`)
* locales: add as many locale as you want (eg: `fr nl en`, in my case I have *fr* and *en*, and you want to add *nl*.)


## php bin/console yaml:xlsx

### arguments

* path: where to find yaml translation files (eg: `/path/to/your/project/translations/` )
* name: the pattern of the yaml translation files (most of the time: `.yaml` )
* output: file to generate (eg `/path/to/your/excel.xlsx`)
* locales: add as many locale as you want (eg: `fr nl en`, in my case I have *fr* and *en*, and you want to add *nl*.)


Example: 

* `php bin\console yaml:xlsx \www\project\translations\ *.yaml \tmp\translations.xlsx en nl fr it` 

## php bin/console yaml

### arguments

* xlsx: where is located the Excel (xlsx) file
* output: Where to store newly generated files

### Example:
* `php bin\console xlsx:yaml \tmp\translations.xlsx \www\project\translations\`

