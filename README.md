# Greenpeace Planet 4

This WordPress plugin provides the necessary blocks to be used with Shortcake UI plugin.

**How to develop a new block you ask?**

1. Create a new controller class that extends Controller inside directory _classes/controller/blocks_. The class name should follow naming convention, for example **Blockname**_Controller and its file name should be class-p4bks-blocks-**blockname**-controller.php. 

2. Implement its parent's class two abstract methods. In method **prepare_fields()** you need to define the blocks fields and in method **prepare_template()** you need to prepare them for rendering.

3. Create the template file that will be used to render your block inside directory _includes/blocks_. If the name of the file is **block_name**.twig then
you need to override method load() and set the inherited property inside like this **$this->block_name = 'block_name'** It also works with html templates. Just add 'php' as the 3rd argument of the block() method.

4. Add your new class name to the array that the Loader function takes as an argument in the plugin's main file.

5. Finally, before committing do **composer update --no-dev** and **composer dump-autoload --optimize** in order to add your new class to composer's autoload.

_(Steps 4, 5 will not be necessary later on, since we will use our own autoloading instead of Composer's autoloader)_