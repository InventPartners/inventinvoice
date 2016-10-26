# inventinvoice
Fabulous, functional and FREE invoicing software

1. Clone the git repository at  https://github.com/InventPartners/inventinvoice to the desired location on your web server.
  * `git clone https://github.com/InventPartners/inventinvoice.git`
2. Create a database for InventInvoice on your web server as well as a user who has all privileges for accessing and modifying it.
3. Run the install script by visiting [http://localhost/inventinvoice/install.php](http://localhost/inventinvoice/install.php) in your web browser.
  * Note: Your URL may be be different, depending on your web host and where you cloned the git repo to.
  * The install script will ask for your database host, database name, username and password.
  * It will also ask for an Admin username and password, so it can set up an admin account for you.
4. All being well, the script will install the database tables etc as well as create the `config.inc.php file` in the `/config` directory. 
  * Depending on your file permissions, this step may fail, in which case you should create this file yourself by copying and pasting the code the install script generates for you.
