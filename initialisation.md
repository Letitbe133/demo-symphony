# Steps

## installation composer
> sudo apt-get install composer

## création d'un projet
<!-- template site -->
> composer create-project symfony/website-skeleton nom_du_projet
<!-- template api -->
> composer create-project symfony/skaleton nom_du_projet

## installation de la CLI
> wget https://get.symfony.com/cli/installer -O - | bash

## export de la variable PATH
> export PATH="$HOME/.symfony/bin:$PATH"

## démarrage du serveur
> symfony serve

## arrêt du serveur
> symfony server:stop
