# Diagramme UML

Voici un diagramme de classe UML en notation **Mermaid**.  
GitHub va automatiquement générer le diagramme quand tu consulteras ce fichier en ligne.

```mermaid
classDiagram
    class User {
        -string name
        -string firstname
        -string email
        -string telephone
        -boolean show_phone
        -string password
        -string role
        -boolean must_reset_password
        +isAdmin()
        +isTechnician()
        +shouldResetPassword()
    }

    class Client {
        -string name
        -string firstname
        -string email
        -string telephone
        -string address
    }

    class Ticket {
        -string titre
        -string description
        -string statut
        -string[] images
        -int client_id
        -int technicien_id
    }

    class Intervention {
        -string titre
        -string description
        -string statut
        -string[] images
        -int client_id
        -int ticket_id
    }

    %% Relations
    Client "1" -- "*" Ticket : possède
    Client "1" -- "*" Intervention : possède
    Ticket "1" -- "*" Intervention : contient
    User "1" -- "*" Ticket : gère
    User "*" -- "*" Intervention : effectue

    %% Notes
    note for User "Rôles possibles : admin, technicien"
    note for Ticket "Statuts : en attente, en cours, terminé"
    note for Intervention "Statuts : en attente, en cours, terminé"
