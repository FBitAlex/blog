<?php

namespace App;

use \Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Notifiable;


    const IS_BANNED = 1;
    const IS_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function comments() {
        return $this->hasMany(Comments::class);
    }


    public static function add( $fields ) {
        $user = new static;
        $user->fill( $fields );
        $user->save();

        return $user;
    }

    public function edit( $fields ) {
        
        $this->fill( $fields );
        $this->save();
    }

    public function generatePassword( $password ) {
        if( $password != null ) {
            $this->password = bcrypt( $password );
            $this->save();
        }
    }

    public function remove() {
        $this->removeAvatar();
        $this->delete();
    }

    public function uploadAvatar( $image ) {

        if ( $image == null ) return;

        // delete post image
        $this->removeAvatar();

        $filename = str_random(10) . '.' . $image->extension();
        $image->storeAs( 'uploads', $filename );
        $this->avatar = $filename;
        $this->save();
    }

    public function removeAvatar() {
        if ( $this->avatar != null ) Storage::delete( 'uploads/' . $this->avatar );
    }

    public function getAvatar() {
        return ( $this->avatar == null ) ? '/img/no-image.png' : '/uploads/' . $this->avatar;
    }
    


    public function makeAdmin() {
        $this->is_admin = 1;
        $this->save();
    }

    public function makeNormal() {
        $this->is_admin = 0;
        $this->save();
    }

    public function toggleAdmin( $value ) {
        return ( $value == null ) ? makeNormal() : makeAdmin();
    }



    public function ban() {
        $this->status = User::IS_BANNED;
        $this->save();
    }

    public function unBan() {
        $this->status = User::IS_ACTIVE;
        $this->save();
    }

    public function toggleBan( $value ) {
        return ( $value == null ) ? ban() : unBan();
    }


}
