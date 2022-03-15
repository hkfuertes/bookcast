@echo off
for %%I in (.) do set CurrDirName=%%~nxI
cls
echo Your IP adress is:
ipconfig | findstr /C:IPv4
echo Go to http://your_ip:8321/%CurrDirName%/feed.php on your podcast app to download the audiobook.
docker run --name bookcast -p 8321:80 -v %cd%:/var/www/html/%CurrDirName% -d php:7.3.32-apache
echo Server running, press any key to stop it!
pause
docker container rm bookcast -f