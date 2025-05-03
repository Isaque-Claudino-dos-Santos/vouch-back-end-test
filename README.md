## Run Project

### Requirement

- copy `.env.example` in new file `.env`
- configure `DB_*` variables
 
### Run Daemon ![](doc/icons/terminal.svg)

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

or use

````bash
    php -S localhost:8000 -t ./app
````

## Directories and Files

### autoload.php

Carrega classes.

### daemon.php

Arquivo roda em background gerenciando as mensagens da fila.

### /app

Pasta tendo o formulário que envia mensagens para a fila rodando em background.

### /Constants

Contentem as constantes (Enum, Classes) do projeto

### /Lib/Env.php

Pegar variáveis de um arquivo para uso ex: .env, .env.local

### /Lib/Database.php

Class que configura a conexão com o banco de dados utilizando `PDO` class.

### /Lib/MsgQueue.php

Wrapper da funcionalidade `msg_queue` do PHP

### /Models/Message.php

Model que contem operações de banco e uma representação da entidade `message` no banco
