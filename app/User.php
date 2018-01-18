<?php

namespace App;

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
        'name', 'email', 'password',
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
        $user->password = bcrypt( $fields["password"] );
        $user->save();

        return $user;
    }

    public static function edit( $fields ) {
        $this->fill( $fields );
        $this->password = bcrypt( $fields["password"] );
        $this->save();
    }

    public static function remove() {
        Storage::delete( 'uploads/' . $this->image );
        $this->delete();
    }

    public function uploadAvatar( $image ) {

        if ( $image == null ) return;

        // delete post image
        Storage::delete( 'uploads/' . $this->image );

        $filename = str_random(10) . '.' . $image->extension();
        $image->saveAs( 'uploads', $filename );
        $this->image = $filename;
        $this->save();
    }

    public function getAvatar() {
        return ( $this->image == null ) ? '/img/no-user-image.png' : '/uploads/' . $this->image;
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
