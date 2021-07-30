<?php

namespace App\Contacts;

interface UserInvitationInterface{


    public function getAllUser();

    public function sendUserInvitation($data);

    public function userRegistration($data);

    public function userProfileUpdate($data);

    public function userActive($data);




}
