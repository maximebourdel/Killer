# Mode opératoire de configuration de serveur

Ce mode opératoire permettra à n'importe quel utilisateur de reconfigurer un serveur afin de permettre un hébergement de sites internet et ce, avec les sécurités qui vont avec.

#### Version
1.0

## Table des matières
  * [Outils de base](#outils_de_base)
  * [Symfony 2](#symfony_2)
  * [Composer](#composer)
  * [Installer le site web](#installer_le_site_web)
  * [Configurer Ip pour nom de domaine](#configure_ip_nom_domaine)
  * [Fichier .conf](#fichier_.conf)
  * [Fichier php.ini](#fichier_php.ini)
  * [Vérifications](#verifications)
  * [Installation autre composantes](#installation_autre_composantes)
  * [Optimisations](#optimisations)


## Outils de base <a id="outils_de_base"></a>

### Pensez à faire des updates
```sh
$ sudo apt-get update
```

### GIT
```sh
$ sudo apt-get install git
```

### Apache 
```sh
$ sudo apt-get install apache2
```

### PHP
```sh
$ sudo apt-get install php php-xml
```

### MySQL
```sh
$ sudo apt-get install mysql-server php-mysql 
```

A présent, le serveur est prêt à accueillir les sites webs sur le répertoire :
```sh
$ /var/www/html
```

## Symfony 2 <a id="symfony_2"></a>
Initialisation des variable globales
```sh
$ sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
$ sudo chmod a+x /usr/local/bin/symfony
```
## Composer <a id="composer"></a>
```sh
$ sudo apt-get install composer
```

## Installer le site web <a id="installer_le_site_web"></a>

### Import du code source
Se positionner dans le répertoire web  :
```sh
$ cd /var/www/html
```

Lancer la commande :
```sh
$ sudo git clone https://github.com/maximebourdel/Killer.git
```
Un répertoire "Killer" est maintenant présent, c'est le code source.

### Installation de la base de données et des tables

```sh
$ cd Killer
$ php bin/console doctrine:database:create
$ php app/console doctrine:schema:update --force
```

### Installation des librairies

Se positionner dans le projet 
```sh
$ sudo composer update
```

Les composants du projet vont maintenant s'installer (peut prendre du temps).
Les assets (raccourcis) seront eux aussi générés.

Il faut ensuite renseigner les éléments essentiels pour la connexion puis le site sera opérationnel !

###Autorisations

Pour ne plus avoir l'erreur de type "Failed to write cache" :

 1. Installation du composant "acl"
 2. Configuration des droits

```sh
$ sudo apt-get install acl
$ su root
$ cd /var/www/html/Killer
$ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
$ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
$ exit
```

## Configurer Ip pour nom de domaine <a id="configure_ip_nom_domaine"></a>

###  /etc/bind/named.conf.local
Ajouter les lignes suivantes à la fin du fichier /etc/bind/named.conf.local
```sh
zone "killer-game.com" {
  type master;
  file "/etc/bind/zones/db.killer-game.com";
  allow-transfer {5.39.77.232;};
  allow-query{any;};
  notify yes;
};

zone "232.77.39.5.in-addr.arpa" {
  type master;
  file "/etc/bind/zones/232.77.39.5.in-addr.arpa";
};
```

Vérification que tout fonctionne (il ne devrait rien y avoir d'affiché) :
```sh
$ named-checkconf /etc/bind/named.conf
```

Ajouter le dossier zones puis se positionner dedans :
```sh
$ mkdir /etc/bind/zones
$ cd /etc/bind/zones
```
puis créez les fichiers suivants :

### /etc/bind/zones/db.killer-game.com
Créer le fichier /etc/bind/zones/db.killer-game.com et y insérer :
```sh
$TTL 12H
$ORIGIN killer-game.com.
@          IN              SOA             ns.kimsufi.com. ownercheck.killer-game.com. (
           2016041702      ; Serial
           8H              ; Refresh
           30M             ; Retry
           4W              ; Expire
           8H              ; Minimum TTL
)
           IN              NS              ns324635.ip-5-39-77.eu.
           IN              NS              ns.kimsufi.com.
killer-game.com.           IN              A               5.39.77.232
ns         IN              CNAME           killer-game.com.
mail       IN              CNAME           killer-game.com.
www        IN              CNAME           killer-game.com.
ftp        IN              CNAME           killer-game.com.
ownercheck IN              TXT             "xxxxxx"
```

Vérification : 
```sh
$ named-checkzone killer-game.com db.killer-game.com 
zone killer-game.com/IN: loaded serial 2016041702
OK
```


### /etc/bind/zones/232.77.39.5.in-addr.arpa
Créer le fichier /etc/bind/zones/232.77.39.5.in-addr.arpa et y insérer :
```sh
$TTL 12H
@          IN              SOA             killer-game.com. postmaster.killer-game.com. (
           2016041702      ; Serial
           8H              ; Refresh
           30M             ; Retry
           4W              ; Expire
           8H              ; Minimum TTL
)
           IN NS   ns.kimsufi.com.
           IN PTR  killer-game.com.
```
Vérification : 
```sh
$ named-checkzone killer-game.com 232.77.39.5.in-addr.arpa 
zone killer-game.com/IN: loaded serial 2016041702
OK
```


```sh
$ nslookup killer-game.com
Server:  127.0.0.1
Address: 127.0.0.1#53

Name: killer-game.com
Address: 5.39.77.232
```


## Fichier .conf  <a id="fichier_.conf"></a>
### /etc/apache2/apache2.conf
Editer le fichier /etc/apache2/apache2.conf et y insérer à la fin :

```sh
###### Ajout ######

<VirtualHost *:80>
    ServerName killer-game.com
    ServerAlias www.killer-game.com

    DocumentRoot /var/www/html/Killer/web
    <Directory /var/www/html/Killer/web>
        AllowOverride None
        Order Allow,Deny
        Allow from All

        <IfModule mod_rewrite.c>
            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ app.php [QSA,L]
        </IfModule>
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeScript assets
    # <Directory /var/www/Killer>
    #     Options FollowSymlinks
    # </Directory>

    # optionally disable the RewriteEngine for the asset directories
    # which will allow apache to simply reply with a 404 when files are
    # not found instead of passing the request into the full symfony stack
    <Directory /var/www/Killer/web/bundles>
        <IfModule mod_rewrite.c>
            RewriteEngine Off
        </IfModule>
    </Directory>
    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>

###### Ajout ######
```

Puis lancer la commande :
```sh
$ sudp a2enmod rewrite
$ sudo service apache2 restart

```

## Fichier php.ini <a id="fichier_php.ini"></a>

Editer le fichier /etc/php5/cli/php.ini :
```sh
$ sudo editor /etc/php5/cli/php.ini
```
### Initialiser la date systeme du serveur 

Puis faites en sorte de retirer le ";" au début de la ligne et ajouter "Europe/Paris"
La ligne devrait ressembler à ceci
```sh
[Date]
; Defines the default timezone used by the date functions
; http://php.net/date.timezone
date.timezone = "Europe/Paris"
```


##Vérifications <a id="verifications"></a>

Afin de s'assurer qu'il n'y a aucun soucis sur le serveur :
```sh
$ cd /var/www/html/Killer
$ php app/check.php
```
Tout devrait être OK.

##Installation autre composantes <a id="installation_autre_composantes"></a>


###Elastic Search
S'assurer que le package suivant est déjà présent :
```sh
$ sudo apt-get install php5-curl
``` 
Ensuite, récupérer la version elasticsearch-1.4.0 
Puis se situer dans le répertoire :
```sh
$ cd elasticsearch-1.4.0/bin
``` 
Puis lancer la elastic search en tache de fond de la sorte :
```sh
$ sudo ./elasticsearch &
``` 
(le PID sera alors affiché, si vous souhaitez le tuer faire ) :
```sh
$ sudo kill -9 <PID>
``` 
Redémarrer Apache2 :
```sh
$ sudo service apache2 restart
``` 

Aller dans le répertoire du Killer :
```sh
$ cd/var/www/html/Killer 
```

Charger les données pour Elastica :
```sh
$ sudo php app/console fos:elastica:populate
```

###Vérification
Créer un Killer, il devrait se créer sans renvoyer d'erreur.

##Optimisations <a id="optimisations"></a>

### Côté Symfony2
Par défaut, composer génère un autoloader qui n'est pas entièrement optimisé. En effet, lorsque vous avez de nombreuses classes, la génération de l'autoloader peut prendre un temps considérable..

En environnement de production, vous voulez que l'autoloader soit rapide. Composer peut générer un autoloader optimisé qui convertit les arborescences / espace de nom normalisés via PSR-0 en un fichier classmap, améliorant les performances
```sh
$ cd /var/www/html/Killer
$ composer dump-autoload --optimize
```



