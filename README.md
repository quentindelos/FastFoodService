# Présentation de FastFoodService

**FastFoodService** est un projet de digitalisation d’un fast-food, permettant d’optimiser la gestion des commandes, le suivi des stocks, la gestion du personnel, et d’offrir une expérience client moderne et fluide.  
Ce projet répond au besoin croissant de digitaliser la restauration rapide, en proposant un système complet, multi-interface et accessible aussi bien côté clients, employés que gestionnaires.

## Objectifs

- **Digitaliser la prise de commande** : chaque client passe sa commande via une interface web responsive ou sur tablette (PWA).
- **Fluidifier la gestion des commandes** : les employés disposent d’une interface dédiée pour visualiser, préparer et suivre les commandes en temps réel.
- **Centraliser la gestion** : un back-office complet permet de gérer les menus, les stocks, les employés et d’accéder aux statistiques.
- **Paiement sécurisé** : intégration du paiement en ligne via Stripe.
- **Notifications automatiques** : envoi de notifications par email et SMS (via OVH et Brevo) pour informer les clients de l’avancement de leur commande.

## Architecture

- **Backend** : PHP & MySQL, API REST pour la gestion des données.
- **Frontend** :  
  - Client : PWA (Progressive Web App), ergonomique et rapide.  
  - Employés : Interface “Cuisine” en temps réel (Tailwind CSS).  
  - Back-office : Gestion avancée des stocks, menus, commandes, utilisateurs.
- **Paiement** : Intégration Stripe.
- **Notifications** : Email (PHPMailer), SMS (OVH).
- **Déploiement** : Automatisation via GitHub Workflows sur le serveur FTP.

## Fonctionnalités principales

- Prise de commande en ligne ou sur place
- Gestion des statuts de commande (en attente, en préparation, prête, servie…)
- Gestion des menus et stocks
- Tableau de bord pour les employés et le manager
- Historique des commandes et système de fidélité (évolutif)
- Notifications automatiques (Email/SMS)
