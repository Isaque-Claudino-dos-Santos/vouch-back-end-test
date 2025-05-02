## Run Project

### Requirement

- copy `.env.example` in new file `.env`

### Run Daemon

use make

```bash
  make start-daemon
```

or use

```bash
    php ./daemon.php
```

### Run App

use make

````bash
    make start-app
````

````bash
    php -S localhost:8000 -t ./app
````