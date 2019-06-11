# Greenpeace Planet4 Blocks Plugin

![Planet4](./planet4.png)

## Introduction

This WordPress plugin provides the necessary blocks to be used with Shortcake UI plugin.

## Overide default twig templates of blocks

You can overide the default block twig templates by including in your child theme a file with the same name in the subdirectory
`/templates/plugins/planet4-plugin-blocks/includes/`


**How to develop a new block you ask?**

1. Create a new controller class that extends Controller inside directory _classes/controller/blocks_. The class name should follow naming convention, for example **Blockname**_Controller and its file name should be class-**blockname**-controller.php.

2. Implement its parent's class two abstract methods. In method **prepare_fields()** you need to define the blocks fields and in method **prepare_data()** you need to prepare the data which will be used for rendering.

3. Create the template file that will be used to render your block inside directory _includes/blocks_. If the name of the file is **blockname**.twig then
you need to set the BLOCK_NAME constant as **'blockname'** It also works with html templates. Just add 'php' as the 3rd argument of the block() method.

4. Add your new class name to the array that the Loader function takes as an argument in the plugin's main file.

5. Finally, before committing do **composer update --no-dev** and **composer dump-autoload --optimize** in order to add your new class to composer's autoload.


## Task automation
We use gulp as automation tools for local development.

Available tasks

* `gulp lint_css` 'checks for css/sass lint errors'
* `gulp lint_js` 'checks for js lint errors'
* `gulp sass` 'concatanates/compiles sass files into a minified single stylesheet'
* `gulp sass_admin` 'concatanates/compiles admin sass files into a minified single stylesheet'
* `gulp uglify` 'concatanates/mangles js files into a minified single js file'
* `gulp uglify_admin` 'concatanates/mangles admin js files into a minified single js file'
* `gulp watch` 'watches for changes in js or sccs and runs the minification tasks'
* `gulp git_hooks` 'copies repo's git hooks to local git repo'

## Contribute

Please read the [Contribution Guidelines](https://planet4.greenpeace.org/handbook/dev-contribute-to-planet4/) for Planet4.
