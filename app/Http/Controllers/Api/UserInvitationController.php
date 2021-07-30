<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contacts\UserInvitationInterface;


class UserInvitationController extends Controller
{
    private $invite;

    public function __construct(UserInvitationInterface $invite)
    {
        $this->invite = $invite;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userList()
    {
        return $this->invite->getAllUser();
    }

    /**
     * inviteUserWithEmail  a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inviteUser(Request $request)
    {
        return $this->invite->sendUserInvitation($request->all());
    }

    /**
     * Display the specified resource.
     *e
     */
    public function userRegistration(Request $request)
    {
        return $this->invite->userRegistration($request->all());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function userProfileUpdate(Request $request)
    {

        return $this->invite->userProfileUpdate($request->all());
    }

    /**
     * Active pin the specified resource in storage.
     *
     */
    public function userActive(Request $request)
    {
        return $this->invite->userActive($request->all());
    }


}
