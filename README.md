## Run Project

### Requirement

- copy `.env.example` in new file `.env`
- configure `DB_*` variables

### Run Daemon

use make

```bash
  make start-daemon QUEUE=123
```

or use

```bash
    php ./daemon.php 123
```

### Run App

use make

````bash
    make start-app QUEUE=123
````

````bash
    php -S localhost:8000 -t ./app
````