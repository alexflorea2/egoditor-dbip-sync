DB-IP Sync

![](preview.gif)


### Architecture
Core functionality files are under the ```Egoditor``` folder.

I built it around a ```Download Job``` entity, that keeps track of the sync process.

Artisan command
```
php artisan dbip:update --stage=fetch --auto=false
```

Where ```stage``` can be in ```complete, fetch, unzip, insert``` and ```auto``` means asking for user input if necessary. Both are optional.

The schedule is set in the ```app/Console/Kernel.php``` and a cron that runs the artisan scheduler must be set on server.

After testing, I have decided to use **MySQL**'s load infile functionality. As there are 30 million rows, inserting via PHP, even directly using the PDO object from Larave's DB connection and batching imports, takes too much time. On my machine, aprox 1h for 1 million rows. MySQL must have the load_infile option enabled.

As I believe this table will be used for reading mostly, I have used the MyIsam engine. This means the csv file is loaded, on my machine, in ~10 minutes. Same load with InnoDB takes about 4 time longer.

### Setup
A basic Docker setup is available, that spawns 2 containers, one for PHP and one for MySql.

Please check the ```.env``` file and configure values accordingly, if changes are made, or using the **local** setup;

#### Docker
Run
```
docker-compose build --no-cache
```
this will take some time, so treat yourself to some pop-corn:) and then
```
docker-compose up
```
You should see output for migration and tests.
Then login to the PHP container and you can run the command.

#### Possible improvements and considerations
 - inject interfaces instead of using Laravel Facades directly
 - better control of the sync flow for a job that fails
 - add more tests
 
 - add indexes after import or/and convert ip to their numeric values for easier lookup.
 
 We could consider writing a mysql script, as MySQL 8 shell allows parallel importing of infile data, that would speed this particular task quite a bit.
