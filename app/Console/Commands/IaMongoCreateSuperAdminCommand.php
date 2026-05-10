<?php

namespace App\Console\Commands;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class IaMongoCreateSuperAdminCommand extends Command
{
    protected $signature = 'ia:mongo-create-super-admin
        {name : Usuario}
        {email : Correo}
        {password : Contraseña}
        {--profile_id=1 : Perfil (1=SUPER)}';

    protected $description = '[IA] Crea/actualiza un super admin en MongoDB.';

    public function __construct(private readonly UserRepository $users)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $name = (string) $this->argument('name');
        $email = (string) $this->argument('email');
        $password = (string) $this->argument('password');
        $profileId = (int) $this->option('profile_id');

        $existing = $this->users->findBy('email', $email) ?? $this->users->findBy('name', $name);
        if ($existing) {
            $existing->name = $name;
            $existing->email = $email;
            $existing->password = Hash::make($password);
            $existing->profile_id = $profileId;
            $existing->status = true;
            $existing->updated_by = 'system';
            $this->users->updateMongo($existing);
            $this->info("Usuario actualizado en Mongo: {$existing->user_id}");

            return self::SUCCESS;
        }

        $user = new User();
        $user->fill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'profile_id' => $profileId,
            'status' => true,
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);
        $this->users->saveMongo($user);
        $this->info("Usuario creado en Mongo: {$user->user_id}");

        return self::SUCCESS;
    }
}

