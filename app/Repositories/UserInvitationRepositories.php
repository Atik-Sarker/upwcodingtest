<?php

namespace App\Repositories;

use App\Mail\Pin;
use Illuminate\Support\Facades\DB;
use App\Contacts\UserInvitationInterface;
use App\Http\Traits\ApiResponse;
use App\Notifications\InviteNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploadTo;

class UserInvitationRepositories implements UserInvitationInterface{

    // Use ApiResponse Trait in this repository
    use ApiResponse, FileUploadTo;

    public function getAllUser(){
        try{
            $data = DB::table('users')->select('id', 'name', 'email')->where('user_role', 2)->get();
            return $this->success('200','success', true, $data);
        }catch(\Exception $e){
            report($e);
            return $this->errors('errors', $e->getMessage(), $e->getCode(), false);
        }
    }
    public function sendUserInvitation($data){

        try{
            $validator = Validator::make($data, [
                'email' => 'required|email|unique:users,email'
            ]);
            if ($validator->fails()) {
                return $this->errors('403',$validator->errors());
            }
            do {
                $token = \Str::random(20);
            } while (User::where('api_token', $token)->first());
            $user = User::create([
                'api_token' => $token,
                'email' => $data['email']
            ]);
            $url = \URL::temporarySignedRoute(

                'registration', now()->addMinutes(300), ['token' => $token]
            );

            $user->notify(new InviteNotification($url));
            return $this->success('200','success', true, 'The Invite has been sent successfully');
        }catch(\Exception $e){
            report($e);
            return $this->errors('errors', $e->getMessage(), $e->getCode(), false);
        }
    }



    public function userRegistration($data){

        try{
            $validator = Validator::make($data, [
                'username' => 'required|min:4|max:20',
                'password' => 'required|min:6|max:20'
            ]);
            if ($validator->fails()) {
                return $this->errors('403',$validator->errors());
            }

            DB::beginTransaction();
            do {
                $pin = rand(121319,565758);
            } while (User::where('pin', $pin)->first());

            User::where('email', $data['email'])->update([
                'user_name' => $data['username'],
                'password' => bcrypt($data['password']),
                'registered_at' => date('Y-m-d h:i:s'),
                'pin' => $pin
            ]);
            DB::commit();
            Mail::to($data['email'])->send(new Pin($pin));
            return $this->success(200, 'pin send successfully');
        }catch(\Exception $e){
            report($e);
            DB::rollback();
            return $this->errors('errors', $e->getMessage(), $e->getCode(), false);
        }
    }



    public function userProfileUpdate($data){

        try{
            $validator = Validator::make($data, [
                'name' => 'required|string|max:100',
                'avatar' => 'dimensions:min_width=256,min_height=256'
            ]);
            if ($validator->fails()) {
                return $this->errors('403',$validator->errors());
            }
            $path = '';
            if ($data['avater']){
                $path =  $this->UploadImage($data['avater'], 'image');
            }

            DB::beginTransaction();
            User::where('email',auth('api')->user()->email)->update([
                'name' => $data['name'],
                'avatar' => $path,
                'updated_at' => date('Y-m-d h:i:s'),
            ]);
            DB::commit();
            return $this->success(200,'Profile has been updated');
        }catch(\Exception $e){
            report($e);
            DB::rollback();
            return $this->errors('errors', $e->getMessage(), $e->getCode(), false);
        }
    }







    public function userActive($data){



        try{
            $validator = Validator::make($data, [
                'pin' => 'required|string|max:6|min:6',
            ]);
            if ($validator->fails()) {
                return $this->errors('403',$validator->errors());
            }



            if (!$user = User::where('pin', $data['pin'])->first()) {
                return $this->errors(403,'invalid pin');
            }

            DB::beginTransaction();
            $user->pin = null;
            $user->isActive = true;
            $user->save();
            DB::commit();
            return $this->success(200, 'Good job! Account accepted!');
        }catch(\Exception $e){
            report($e);
            DB::rollback();
            return $this->errors('errors', $e->getMessage(), $e->getCode(), false);
        }
    }










}
