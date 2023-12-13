# oss-contribs

simple contributions statistics generator

- generates a list of **merged** pull requests in **public** repositories
- generates a list of issues, these pull requests addressed
- generates a count of user reactions on these pull requests and issues

the result is grouped by repository


## Setup / Run

- `composer global require staabm/oss-contribs`
- run `oss-contribs` on your console
  - or use `php bin/oss-contribs` from within the projects folder, in case global composer binaries are not on your PATH

On first run the tool will ask you for an GitHub.com api token.

On any subsequent run you only need to enter a username and a year you want to get the statistics for.

## Example output

```
> oss-contribs

micronax/carbon-german-holidays:
  1 Pull Request(s)
    #3 - Syntax highlighting

composer/pcre:
  1 Pull Request(s)
    #6 - use more precise phpdoc

amazon-php/sp-api-sdk:
  1 Pull Request(s)
    #112 - fix copy/paste issue in FixArgumentDefaultValuesNotMatchingTypeRector

TomasVotruba/unused-public:
  6 Pull Request(s)
    #23 - Revert "drop nette/utils dependency (#21)"
    #22 - Fixed reading of *.twig files recursively
    #21 - drop nette/utils dependency
    #20 - Drop symfony/finder dependency
    #10 - added failling test
    #2 - remove tool config files from release artifacts
  1 Fixed Issue(s)
    #17 - relax symfony constraint

…

@staabm contributed to 66 open-source projects on github.com in 2023
  693 merged Pull Request(s) - fixing 90 reported Issue(s) - addressing 12 Reaction(s)

```


## Give back

In case you find this tool useful, [please consider supporting my open source work](https://github.com/sponsors/staabm).

