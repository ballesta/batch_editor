<?php
class Message 
{
    var $Type_emetteur;
    var $Type_message;
    var $Identifiant_unique;

    function __construct ($Type_emetteur, $Type_message, $Identifiant_unique)
    {
        $this->Type_emetteur = $Type_emetteur;
        $this->Type_message = $Type_message;
        $this->Identifiant_unique = $Identifiant_unique;
    }
}
