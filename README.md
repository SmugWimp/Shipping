# Shipping Application
This is a simple WAMP application meant to track shipments to clients for a small business. A file with the source code to create 
the database the application is created for is included in the MySQL folder. A file with hardcoded database parameters is included as well,
however this is purely for demonstrative purposes and is not best practice to use if/when the application is put in place.

# Security
Security is not tightented as it is not meant to hold any sensitive information and, as such is meant to
be open for all users to access. While the principle of least privelage is not actively utitlized, it is configured 
to allow for it to be implemented in the future if need be. Stored procedures are included in the source code for 
the database, however this was done to increase performance, not as a security measure at this time.

----

May 1st 2020

I decided to fork this repository and enhance a few things for a project I'm working on.

I'm making changes, but I'm not sure how I should document them.

For almost all documents, it checks for a current session before it tries to initiate one.
I put the database credentials in a PHP file for a bit of security. Subsequently, the login function is changed slightly but Invisible to the user.

Not all of the stored SQL procedures worked for me. In those cases I replaced the stored procedure with a typical function.

Added a dropdown calendar picker to aid in date selection and formatting. Needs to be spread around a bit.

Some other stuff. I'll try to remember what.