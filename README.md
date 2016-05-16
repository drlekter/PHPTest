# ProntoPro.it

### Version
1.0.0

### Requisiti

Per installare ProntoPro.it hai bisogno di:

* PHP versione minima di PHP 5.5.9
* JSON deve essere abilitato
* ctype deve essere abilitato
* il tuo php.ini deve avere l'impostazione date.timezone
* npm e bower

### Installazione

Hai bisogno di Composer installato:

```sh
$ curl -sS https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer

# controlla che composer funzioni
composer
```

Dopo aver scaricato la distribuzione modifica il file

```
/app/config/parameters.yml
```

Ora sei pronto per installare ProntoPro.it. Dal terminale naviga nella cartella root ed esegui:

```
$ composer install
```
