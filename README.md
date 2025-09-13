# cdr-db
A simple database for Call Detail Records

# TestUtils
Visit /TestUtils/CDRUnitTests.php to test CDR parsing
Visit /TestUtils/CDRFileUploadTest.php to test uploading a few CDR files

# Frontend UI
Visit main.html for the main front end where you can upload a file or view the table. Note that uploading a file erases the current database prior to adding the new. You would not do this for a production site. You would want to add robust handling for cases where the same id is received multiple times.