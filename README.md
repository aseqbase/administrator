# Administration panel for the aseqbase website
A goldsmith management system, is special for an aseqbase website...
## Dependencies
* <a href="http://github.com//aseqbase/aseqbase">aseqbase/aseqbase</a>
## Installations
1. Install all dependencies
  ### To make the Main Domain of aseqbase website
  2. Copy and replace all files and folders to the home directory (public_html) of the website
  ### To make the Sub Domain of aseqbase website
  2. Copy and replace all files and folders to your subdomain directory (public_html/[my-subdomain-name]/)
3. Change the value of `$GLOBALS["ASEQ"]` located on `/index.php`, to the current subdomains sequence (like [my-subdomain-name]) or if this file is in the root address, leave null for that
4. Use it through its special link (http://[my-domain-name].com/administration or http://[my-subdomain-name].[my-domain-name].com/administration)
5. Enjoy...
