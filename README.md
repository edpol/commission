install sqlsrv drivers
https://learn.microsoft.com/en-us/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-ver16&tabs=ubuntu18-install%2Calpine17-install%2Cdebian8-install%2Credhat7-13-install%2Crhel7-offline


1. Update Package Lists: First, update your package lists to ensure you have the latest information about available packages.  
```
sudo apt update
```
2. Install Dependencies: Install the required dependencies.  
```
sudo apt install -y apt-transport-https curl gnupg
```
3. Download and Install the Microsoft SQL Server Driver for PHP: Microsoft provides a repository for the SQL Server driver for PHP. Add the repository and install the driver.  
```
# Import the public repository GPG keys
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -

# Add the Microsoft Ubuntu repository
sudo curl -o /etc/apt/sources.list.d/mssql.list https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list

# Update the package lists once more
sudo apt update

# Install the SQL Server driver for PHP
sudo apt install -y php7.4-sqlsrv php7.4-pdo-sqlsrv
```
4. Restart the Web Server: After installing the driver, restart your web server to apply the changes.
```
sudo systemctl restart apache2
```



--------------------------

CREATE NEW REPO  

git init
git add .
git commit -m "First Commit"

go to github.com and create repo
get the ssh clone address git@github.com:edpol/commission.git

git push --set-upstream origin master


**  when I created the repo in github the default branch was main
    but in the last command I uploaded all the code to master branch 

