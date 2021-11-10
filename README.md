## Setup for Development

Requirements: Docker (version **17.05** or higher)

- Clone the repo into a folder of your choice
- Inside the folder in the console, run "docker build - log-parser ." to create the container
- When the container is ready , run "docker run -dp 3000:3000 log-parser" to start the container 
- Enter to the console of the container and go to the folder /usr/src/myapp
- Inside this folder the folder run "composer install"
- The last step is run the script with the command "php log_parser.php" 

This will print all the data and after this export the data to the export_log.csv
