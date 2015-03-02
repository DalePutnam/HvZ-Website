
     #####    ###     ##  ### ########             ###     ##  ###    ###TM
   #######  #######   ####### ########           #######   #######  #######
  ###      ###   ###  ###         ###           ###   ###  ###     ###   ##
  ###      ###   ###  ##       ###              ###   ###  ##      ###   ##
   #######  #######   ##      ########    ###    #######   ##        ######
     #####    ###     ##      ########    ###      ###     ##           ###
                                                                     #####

	Please view the license for this free software, here..

	http://corz.org/public/docs/license/free-scripts-licence.php



    Anti-Hammer

	Automatically set temporary bans for web site hammering.
	Protect your valuable server resources for genuine clients.

    Quickstart:

        *   Drop the contents of the zip into the root of your site somewhere,
            maybe in /includes/ or /inc/ or something like that.

        *   Make the "anti-hammer" directory writable.

            IN your ftp client, simply set all the permissions to world-writable
            which is 777. In a shell, do: chmod 777 /full/path/to/anti-hamme

        *   Set your preferences inside anti-hammer.php (in a text editor)
            Especially if you are using a custom location.

        *   Add a line to your site's root .htaccess file..

                php_value auto_prepend_file "/full/real/server/path/to/anti-hammer.php"

           ..replacing the path with the full, real path to anti-hammer.php


        *   You're done!

            Now go load a page over and over and over, quickly!


	If you want more power,


	Full details here..

		http://corz.org/serv/tools/anti-hammer/


    Have fun!

	;o) Cor

	(c) corz.org 2007 ->

    -= nothing is foolproof to the sufficiently talented fool =-
