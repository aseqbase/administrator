# Administrator (Project)
> aseqbase/administrator

A default Content Management System is special for an aseqbase website...A standard, out-of-the-box Content Management System holds particular significance when it comes to building and maintaining an aseqbase website. The inherent characteristics of a default CMS provide a foundational structure that is especially well-suited for the specific requirements and potential of such a website. Utilizing a pre-configured CMS offers several advantages for aseqbase websites, streamlining the development process and ensuring a cohesive user experience. Therefore, opting for a default Content Management System is a strategic choice that can greatly enhance the functionality and overall effectiveness of an aseqbase website.

## Dependencies
* <a href="http://github.com//aseqbase/aseqbase">aseqbase/aseqbase</a>
<h2>Managements</h2>
<h3>Installing</h3>

  1. Install all dependencies mentioned before
  2. Follow one of these options:
		* Open a terminal in the destination directory (for example, `D:\MyWebsite\administrator\`) of the website, then install the project by:
			``` bash
			> composer create-project aseqbase/administrator
			```
		* Prompts below to create a manageable project (update, uninstall, etc.):
			``` bash
			> composer require aseqbase/administrator
			> cd vendor/aseqbase/administrator
			vendor/aseqbase/administrator> composer dev:install
			```
  3. Put the destination directory of your project on the appeared step (for example, `D:\MyWebsite\administrator\`)
		``` bash
		Destination Directory [D:\MyWebsite\]: D:\MyWebsite\administrator\
		```
  4. Follow the steps to finish the installation of sources, database, etc.
  5. [optional] On the local server, create an optional file named `global.php` in the `administrator` directory  to change your-parent-directory-name (from the `aseq`) using:
		``` bash
		> composer administrator:create global --aseq "administrator" --base "your-parent-directory-name" -f
		```
		or
		``` bash
		> cd vendor/aseqbase/administrator
		vendor/aseqbase/administrator> composer dev:create global --aseq "administrator" --base "your-parent-directory-name" -f
		```
		**Note**: Do not forget to replace "your-parent-directory-name" with your item (default `aseq`). 
  6. Enjoy...

<h3>Using</h3>

  1. Do one of the following options:
	  	* Visit its special URL (for example, `http://administrator.[my-domain-name].com`, or `http://[my-domain-name].com/administrator`)
		* On the local server:
			1. Use the following command on the root directory
				``` bash
				> composer start
		  		```
		  	2. Visit the URL `localhost:8000` (for default) on the local browser
  2. Enjoy...

<h3>Updating</h3>

  1. Keep your project updated using
		``` bash
		> composer administrator:update
		```
		or
		``` bash
  		> cd vendor/aseqbase/administrator
		vendor/aseqbase/administrator> composer dev:update
		```
  2. Follow the steps to finish the update of sources, database, etc.
  3. Enjoy...

<h3>Uninstalling</h3>

  1. Uninstall the project and the constructed database using:
		``` bash
		> composer administrator:unistall
		```
		or
		``` bash
  		> cd vendor/aseqbase/administrator
		vendor/aseqbase/administrator> composer dev:unistall
		```
  2. Follow the steps to finish the uninstallation of sources, database, etc.
  3. Enjoy...

<h4>Creating</h4>

  1. Create a new file by a predefined template name (for example, global, config, back, router, front, user, info, etc.) using:
		``` bash
		> composer administrator:create [predefined-template-name]
		```
		or
		``` bash
  		> cd vendor/aseqbase/administrator
		vendor/aseqbase/administrator> composer dev:create [predefined-template-name]
		```
  2. Follow the steps to finish creating the file.
  3. Enjoy...
